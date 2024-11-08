import { Component, ElementRef, Renderer2, AfterViewInit,CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { AdminDocumentService } from '../../services/admin-document.service';  // Import the service
import { PermissionsService } from '../../services/permissions.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';

declare var bootstrap: any;

@Component({
  selector: 'app-admin-document-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  templateUrl: './admin-document-datatable.component.html',
  styleUrl: './admin-document-datatable.component.css'
})
export class AdminDocumentDatatableComponent {
  events: any[] = [];
  selectedEvent: any = {};
  fileToUpload: File | null = null;
  limit = 10; 
  lang_code = 'en'; 
  selectedFile: any;
  selectedFileError: string | null = null; // Initialized with null
  currentPage: number = 1; // Current page for pagination
  totalItems: number = 0; // Total items to calculate total pages
  lastPage: number = 0; // Last page
  fileToUploadDoc: File | null = null;
  fileToUploadBanner: File | null = null;
  fileToUploadImg: File | null = null;
  userId: number | null = null;
  userRoleIds: number[] | [] = [];
  userRankId: number = 0;
  categories: { [key: string]: string } = {};
  roles: any;
  selectedRoles: string[] = [];
  rolesArray: any;
  selectedRoleIds: any[] = [];
  documentUrl: SafeResourceUrl | null = null; // Change to SafeResourceUrl
  private modal: HTMLElement | null = null; // Store modal reference
  isOpen = false;
  isOpen2 = false;
  i: any;
  j: any;
  rank: any;
  rankArray: any;
  selectedRank: any;

  constructor(private AdminDocumentService: AdminDocumentService, private permissionsService: PermissionsService, private renderer: Renderer2, private el: ElementRef, private sanitizer: DomSanitizer) {}

  ngOnInit(): void {
    this.loadroles();
    this.loadUserId();
    this.loadCategories();
    this.loadRank();
    this.loadList();
  }

  loading: boolean = false;

  loadUserId(): void {
    const userData = localStorage.getItem('user'); // Retrieve user data from localStorage
    if (userData) {
      const user = JSON.parse(userData); // Parse the JSON string back to an object
      this.userId = user;
      if (Array.isArray(user.roles)) {
        this.userRoleIds = user.roles.map((role: { id: any; }) => role.id); // Extracting only the role IDs
        
        
      } else {
          this.userRoleIds = []; // Reset to an empty array if not valid
      }
      if (user.rank) {
        this.userRankId=user.rank;
      }
    } else {
      console.warn('No user data found in localStorage');
    }
  }
  loadCategories(): void {
    this.AdminDocumentService.documentCategory().subscribe(
      (data) => {
        this.categories = data.data; // Assuming the response is an object with id as keys and name as values
      },
      (error) => {
        console.error('Error fetching categories:', error);
      }
    );
  }
  loadRank(): void {
    this.AdminDocumentService.getRankList().subscribe(
      (data) => {
        this.rank = data.data;
        if (this.rank && typeof this.rank === 'object') {
          this.rankArray = Object.entries(this.rank).map(([id, name]) => ({ id, name }));
        } else {
          console.error('Rank is not defined or is not an object:', this.rank);
          this.rankArray = []; // Reset to an empty array if not valid
        }
      },
      (error) => {
        console.error('Error fetching categories:', error);
      }
    );
  }
  loadroles(): void {
    this.AdminDocumentService.getRole().subscribe(
      (data) => {
        this.roles = data.data;
        if (this.roles && typeof this.roles === 'object') {
          this.rolesArray = Object.entries(this.roles).map(([id, name]) => ({ id, name }));
        } else {
          console.error('Roles is not defined or is not an object:', this.roles);
          this.rolesArray = []; // Reset to an empty array if not valid
        }
      },
      (error) => {
        console.error('Error fetching categories:', error);
      }
    );
  }

  loadList(): void {
    this.loading = true; // Start loading
    this.AdminDocumentService.allList(this.limit, this.lang_code, this.currentPage, this.userRoleIds, this.userRankId ).subscribe(data => {
      this.events = data.data;
      this.totalItems = data.total; // Assuming the API returns total items
      this.lastPage = Math.ceil(this.totalItems / this.limit);
      this.formatEventDates();
      this.loading = false; // Stop loading
    }, error => {
      console.error('Error loading events:', error);
      this.loading = false; // Stop loading on error
    });
  }
  // Change page method
  changePage(page: number): void {
    //('Changing to page:', page); // Debugging line
    if (page < 1 || page > this.lastPage) return; // Prevent out of bounds
    this.currentPage = page;
    this.loadList(); // Reload data
  }

  // Total pages calculation
  totalPages(): number {
    return Math.ceil(this.totalItems / this.limit);
  }

  formatEventDates(): void {
    this.events.forEach(event => {
      event.created_at = new Date(event.created_at).toLocaleDateString('en-GB');
      event.start_date = new Date(event.start_date).toLocaleDateString('en-GB');
      event.end_date = new Date(event.end_date).toLocaleDateString('en-GB');
      
      switch (event.status) {
        case 1:
          event.status = 'Draft';
          break;
        case 2:
          event.status = 'Pending';
          break;
        case 3:
          event.status = 'Published';
          break;
        default:
          event.status = '';
          break;
      }
      
      // Store the document name and ID for binding
      if (event.doc) {
        event.documentLink = {
          id: event.id,
          name: event.doc_name
        };
      } else {
        event.documentLink = null; // No document link
      }
    });
  }
  

  editEvent(id: number): void {
    this.AdminDocumentService.getEvent(id).subscribe(data => {
      this.selectedEvent = data;
      // Set selectedRoles based on the fetched data
      this.selectedRoles = data.roleIds.map((id: any) => String(id)) || [];
      this.selectedRank = data.rankIds.map((id: any) => String(id)) || [];
      this.openEditModal();
    });
  }

  addEvent(): void {
    const modalElement = document.getElementById('addEventModal');
    if (modalElement) {
        this.selectedEvent = {};
        this.selectedRoles =[];
        this.selectedRank =[];
        this.isOpen =false;
        this.isOpen2 =false;
        
        const documentCategoryId = document.getElementById('document_category') as HTMLSelectElement;

        // Clear existing options
        documentCategoryId.innerHTML = '';
        const emptyOption = document.createElement('option');
        emptyOption.value = '';
        emptyOption.textContent = 'Select a category';
        documentCategoryId.appendChild(emptyOption);

        // Populate the select element with categories
        for (const [id, name] of Object.entries(this.categories)) {
          const option = document.createElement('option');
          option.value = id;
          option.textContent = name;
          documentCategoryId.appendChild(option);
        }

        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
  }

  
  // Method to handle role checkbox changes
  // handleRoleChange(event: Event, roleId: string | undefined): void {
  //   if (event.target instanceof HTMLInputElement && roleId) { // Check if roleId is defined
  //       const idWithoutPrefix = roleId.split('_').pop(); // Remove prefix

  //       // Ensure idWithoutPrefix is a string
  //       if (typeof idWithoutPrefix === 'string') {
  //           if (event.target.checked) {
  //               this.selectedRoleIds.push(idWithoutPrefix);
  //           } else {
  //               this.selectedRoleIds = this.selectedRoleIds.filter((id: string) => id !== idWithoutPrefix);
  //           }
  //       }
  //   }
  // }


  saveEvent(): void {
    console.log('Selected Event:', this.selectedEvent);  // Log selectedEvent for debugging
    
    // Validate the form data
    const requiredFields = [
        'doc_name', 'doc_type', 'status', 'position', 'start_date', 'end_date',
    ];

    // Check for missing required fields
    const missingFields = requiredFields.filter(field => !this.selectedEvent[field] && this.selectedEvent[field] !== 0);
    if (missingFields.length > 0) {
        alert(`Missing required fields: ${missingFields.join(', ')}`);
        return;
    }

    // Prepare FormData
    const formData = new FormData();
    formData.append('document_category_id', this.selectedEvent.document_category_id);
    formData.append('doc_name', this.selectedEvent.doc_name);
    formData.append('description', this.selectedEvent.description);
    formData.append('doc_type', this.selectedEvent.doc_type);
    formData.append('status', this.selectedEvent.status);
    formData.append('position', this.selectedEvent.position);
    formData.append('start_date', this.selectedEvent.start_date);
    formData.append('end_date', this.selectedEvent.end_date);

    // Log formData content before submission
    // console.log('Form Data:', this.selectedEvent);

    // Append selected roles to formData (if any)
    if (this.selectedRoles && this.selectedRoles.length > 0) {
        this.selectedRoles.forEach((roleId: string | Blob) => {
            formData.append('roles[]', roleId);  // Ensure 'roles[]' matches backend API
        });
    } else {
        alert('No roles selected');
    }

    // Append selected ranks to formData (if any)
    if (this.selectedRank && this.selectedRank.length > 0) {
      this.selectedRank.forEach((rankId: string | Blob) => {
          formData.append('ranks[]', rankId);  // Ensure 'ranks[]' matches backend API
      });
    } else {
        alert('No ranks selected');
    }

    // Validate and append file if it's present
    if (this.fileToUpload) {
        const validFileTypes = ['application/pdf'];  // Example: Only PDF files allowed
        const maxFileSize = 5 * 1024 * 1024;  // 5MB size limit

        // Validate file type
        if (!validFileTypes.includes(this.fileToUpload.type)) {
            alert('Invalid file type. Only PDF files are allowed.');
            return;
        }

        // Validate file size
        if (this.fileToUpload.size > maxFileSize) {
            alert('File size exceeds the limit of 5MB.');
            return;
        }

        const sanitizedFileName = this.fileToUpload.name.replace(/\s+/g, '_');  // Replace spaces with underscores

        formData.append('doc', this.fileToUpload, sanitizedFileName);
    } else {
        console.log('No file selected');
    }

    // Send the request to the backend
    this.AdminDocumentService.storeEvent(formData).subscribe(
        (event: HttpEvent<any>) => {
            console.log('Event saved successfully:', event);
            this.loadList();  // Refresh the list of events
            this.closeAddModal();  // Close the modal or form
        },
        error => {
            // Handle error response from backend
            if (error.status === 422) {
                // Log detailed error message from the backend
                console.error('Backend Validation Errors:', error.error);
                alert('Error saving event: ' + (error.error?.message || 'Unknown error'));
            } else {
                console.error('Error saving event:', error);
                alert('Error saving event: ' + (error.message || error));
            }
        }
    );
}

  

  modifyEvent(): void {
    // Validate the form data
    const requiredFields = [
      'doc_name',
      'doc_type',
      'status',
      'position',
      'start_date',
      'end_date',
    ];
    
    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);
  
    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }

    const formData = new FormData();
    formData.append('document_category_id', this.selectedEvent.document_category_id);
    formData.append('doc_name', this.selectedEvent.doc_name);
    formData.append('description', this.selectedEvent.description);
    formData.append('doc_type', this.selectedEvent.doc_type);
    formData.append('status', this.selectedEvent.status);
    formData.append('position', this.selectedEvent.position);
    formData.append('start_date', this.selectedEvent.start_date);
    formData.append('end_date', this.selectedEvent.end_date);

    // Append selected roles to formData
    this.selectedRoles.forEach((roleId: string | Blob) => {
      formData.append('roles[]', roleId); // Use roles[] for array input
    });

     // Append selected ranks to formData (if any)
     if (this.selectedRank && this.selectedRank.length > 0) {
      this.selectedRank.forEach((rankId: string | Blob) => {
          formData.append('ranks[]', rankId);  // Ensure 'ranks[]' matches backend API
      });
    } else {
        alert('No ranks selected');
    }

    // Append file only if it's present
    if (this.fileToUpload) {
      const validFileTypes = ['application/pdf']; // Example types
      const maxFileSize = 5 * 1024 * 1024; // 5MB
  
      if (!validFileTypes.includes(this.fileToUpload.type)) {
        alert('Invalid file type');
        return;
      }
      if (this.fileToUpload.size > maxFileSize) {
        alert('File size exceeds the limit of 5MB');
        return;
      }
      
      const sanitizedFileName = this.fileToUpload.name.replace(/\s+/g, '_'); // Replace spaces with underscores
        
      formData.append('document', this.fileToUpload, sanitizedFileName);
    }

    this.AdminDocumentService.updateEvent(this.selectedEvent.id, formData).subscribe(
      (event: HttpEvent<any>) => {
          this.loadList(); // Refresh the list of events
          this.closeEditModal(); // Close the modal or form
      },
      error => {
        console.error('Error saving event', error);
      }
    );
  }

  deleteEvent(id: number): void {
    if (confirm('Are you sure you want to delete this event?')) {
      this.AdminDocumentService.deleteEvent(id).subscribe(() => {
        this.events = this.events.filter(event => event.id !== id);
      });
    }
  }

  openEditModal(): void {
    const modalElement = document.getElementById('editEventModal');
    if (modalElement) {
      this.isOpen =false;
      this.isOpen2 =false;
        const documentCategoryId = document.getElementById('document_category_id') as HTMLSelectElement;

        // Clear existing options
        documentCategoryId.innerHTML = '';

        // Populate the select element with categories
        for (const [id, name] of Object.entries(this.categories)) {
            const option = document.createElement('option');
            option.value = id;
            option.textContent = name;
            documentCategoryId.appendChild(option);
        }

        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
  }

  

  closeEditModal(): void {
    const modalElement = document.getElementById('editEventModal');
    if (modalElement) {
      const modal = bootstrap.Modal.getInstance(modalElement);
      if (modal) {
        modal.hide();
      }
    }
  }

  closeAddModal(): void {
    const modalElement = document.getElementById('addEventModal');
    if (modalElement) {
      const modal = bootstrap.Modal.getInstance(modalElement);
      if (modal) {
        modal.hide();
      }
    }
  }

  onFileChange(event: any): void {
    if (event.target.files.length > 0) {
      this.fileToUpload = event.target.files[0];
    }
  }
  
  oneditFileChange(event: any) {
    const file = event.target.files[0];
    if (file) {
      // Optionally validate file type and size
      if (file.size > 5000000) { // Example: limit to 5MB
        this.selectedFileError = 'File size exceeds 5MB limit.';
      } else {
        this.selectedFile = file;
        this.selectedFileError = '';
        this.fileToUpload = event.target.files[0];
      }
    }
  }
  ngAfterViewInit(): void {
    this.modal = this.el.nativeElement.querySelector('#documentModal');
  }

  viewDocument(docId: number): void {
    if (Array.isArray(this.userRoleIds) && this.userRoleIds.length > 0) {
      this.userRoleIds.forEach(roleId => {
        this.AdminDocumentService.showDocument(docId, roleId).subscribe({
          next: (blob) => {
            if (blob) {
              const url = window.URL.createObjectURL(blob);
              this.documentUrl = this.sanitizer.bypassSecurityTrustResourceUrl(url); // Sanitize the URL

              // Show the modal
            if (this.modal) {
              this.renderer.addClass(this.modal, 'show');
              this.renderer.setStyle(this.modal, 'display', 'block');

              // Create and insert the iframe
              const iframe = this.renderer.createElement('iframe');
              this.renderer.setAttribute(iframe, 'src', this.documentUrl as string);
              this.renderer.setStyle(iframe, 'width', '100%');
              this.renderer.setStyle(iframe, 'height', '100%');
              this.renderer.setStyle(iframe, 'border', 'none');

              // Append the iframe to the modal
              this.renderer.appendChild(this.modal, iframe);

              // Disable right-click and print in the iframe
              iframe.onload = () => {
                const iframeWindow = iframe.contentWindow;
                if (iframeWindow) {
                  iframeWindow.document.addEventListener('contextmenu', (e: { preventDefault: () => any; }) => e.preventDefault());
                  iframeWindow.document.addEventListener('keydown', (e: { ctrlKey: any; key: string; preventDefault: () => void; }) => {
                    if (e.ctrlKey && (e.key === 'p' || e.key === 'P')) {
                      e.preventDefault();
                    }
                  });
                }
              };

              // Cleanup on modal close
              const listener = this.renderer.listen(this.modal, 'hidden.bs.modal', () => {
                window.URL.revokeObjectURL(url);
                this.documentUrl = null; // Reset URL for next use
                this.renderer.removeChild(this.modal, iframe); // Remove the iframe
                listener(); // Remove listener
              });
            }
              
            } else {
              console.error(`No file found for document ID ${docId} and role ID ${roleId}.`);
              alert(`No file found for this document. Please check your permissions or contact support.`);
            }
          },
          error: (error) => {
            console.error(`Error fetching document for role ID ${roleId}:`, error);
            alert(`Error fetching document. Please check your permissions or try again later.`);
          },
        });
      });
    } else {
      alert('No valid role IDs found. Please contact support.');
    }
  }

  closeModal(): void {
    if (this.modal) {
      this.renderer.removeClass(this.modal, 'show');
      this.renderer.setStyle(this.modal, 'display', 'none');
      this.documentUrl = null; // Clear URL on close
    }
  }
  
  // Checks if the user has the given permission
  hasPermission(permission: string): boolean {
    return this.permissionsService.hasPermission(permission);
  }

  // Checks if the user has any of the given permissions
  hasAnyPermission(permissions: string[]): boolean {
    return this.permissionsService.hasAnyPermission(permissions);
  }

  disableDownloadAndPrint(event: Event): void {
    const iframe = event.target as HTMLIFrameElement;
    const iframeWindow = iframe.contentWindow;

    if (iframeWindow) {
      iframeWindow.document.addEventListener('contextmenu', (e) => e.preventDefault());
      iframeWindow.document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && (e.key === 'p' || e.key === 'P')) {
          e.preventDefault();
        }
      });
    }
  }

  toggleDropdown() {
    this.isOpen = !this.isOpen;
  }

  toggleSelectAll(event: Event): void {
    const isChecked = (event.target as HTMLInputElement).checked;
    this.selectedRoles = isChecked ? this.rolesArray.map((role: { id: any; }) => role.id) : [];
    console.log(this.selectedRoles );
  }

  toggleSelection(roleId: string): void {
      const index = this.selectedRoles.indexOf(roleId);
      if (index === -1) {
          this.selectedRoles.push(roleId);
      } else {
          this.selectedRoles.splice(index, 1);
      }
  }

  isSelected(roleId: string): boolean {
      return this.selectedRoles.includes(roleId);
  }

  toggleDropdown2() {
    this.isOpen2 = !this.isOpen2;
  }

  toggleSelectAll2(event: Event): void {
    const isChecked = (event.target as HTMLInputElement).checked;
    this.selectedRank = isChecked ? this.rankArray.map((rank: { id: any; }) => rank.id) : [];
    console.log(this.selectedRank );
  }

  toggleSelection2(rankId: string): void {
      const index = this.selectedRank.indexOf(rankId);
      if (index === -1) {
          this.selectedRank.push(rankId);
      } else {
          this.selectedRank.splice(index, 1);
      }
  }

  isSelected2(rankId: string): boolean {
      return this.selectedRank.includes(rankId);
  }
  
}
