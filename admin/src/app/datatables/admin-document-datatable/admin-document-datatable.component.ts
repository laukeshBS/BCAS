import { Component } from '@angular/core';
import { AdminDocumentService } from '../../services/admin-document.service';  // Import the service
import { PermissionsService } from '../../services/permissions.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';

declare var bootstrap: any;

@Component({
  selector: 'app-admin-document-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
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
  categories: { [key: string]: string } = {};
  roles: any;
  selectedRoles: string[] = [];
  rolesArray: any;
  selectedRoleIds: any[] = [];
  

  constructor(private AdminDocumentService: AdminDocumentService, private permissionsService: PermissionsService) {}

  ngOnInit(): void {
    this.loadroles();
    this.loadUserId();
    this.loadCategories();
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
    this.AdminDocumentService.allList(this.limit, this.lang_code, this.currentPage, this.userRoleIds ).subscribe(data => {
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
      // Set selectedRoleIds based on the fetched data
      this.selectedRoleIds = data.roleIds.map((id: any) => String(id)) || [];
      this.openEditModal();
    });
  }

  addEvent(): void {
    const modalElement = document.getElementById('addEventModal');
    if (modalElement) {
      this.selectedEvent = { document_category: '', doc_name: '', doc_type: '', status: '', position: '', start_date: '', end_date: '' };
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

      // Populate role checkboxes
      const rolesContainer = document.getElementById('rolesContainer') as HTMLElement;
      rolesContainer.innerHTML = ''; // Clear existing checkboxes
      if (this.rolesArray && Array.isArray(this.rolesArray)) {
        this.rolesArray.forEach((role: { id: string; name: string; }) => {
          const checkbox = document.createElement('input');
          checkbox.type = 'checkbox';
          checkbox.id = `add_${role.id}`;
          checkbox.value = role.id;
          checkbox.checked = this.selectedRoles.includes(role.id);
  
          // Use a method to handle checkbox changes
          checkbox.addEventListener('change', (event) => this.handleRoleChange(event, role.id));
  
          const label = document.createElement('label');
          label.htmlFor = role.id;
          label.textContent = role.name;
  
          rolesContainer.appendChild(checkbox);
          rolesContainer.appendChild(label);
          rolesContainer.appendChild(document.createElement('br')); // For spacing
        });
      } else {
        console.error('Roles is not defined or is not an array');
      }

      const modal = new bootstrap.Modal(modalElement);
      modal.show();
    }
  }
  // Method to handle role checkbox changes
  handleRoleChange(event: Event, roleId: string | undefined): void {
    if (event.target instanceof HTMLInputElement && roleId) { // Check if roleId is defined
        const idWithoutPrefix = roleId.split('_').pop(); // Remove prefix

        // Ensure idWithoutPrefix is a string
        if (typeof idWithoutPrefix === 'string') {
            if (event.target.checked) {
                this.selectedRoleIds.push(idWithoutPrefix);
            } else {
                this.selectedRoleIds = this.selectedRoleIds.filter((id: string) => id !== idWithoutPrefix);
            }
        }
    }
  }


  saveEvent(): void {
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
    this.selectedRoleIds.forEach((roleId: string | Blob) => {
      formData.append('roles[]', roleId); // Use roles[] for array input
    });
  
    // Validate and append file only if it's present
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
      
      formData.append('doc', this.fileToUpload, this.fileToUpload.name);
    }
  
    this.AdminDocumentService.storeEvent(formData).subscribe(
      (event: HttpEvent<any>) => {
        this.loadList(); // Refresh the list of events
        this.closeAddModal(); // Close the modal or form
      },
      error => {
        alert('Error saving event: '+error.message || error);
        // Optionally, display an error message to the user
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
    this.selectedRoleIds.forEach((roleId: string | Blob) => {
      formData.append('roles[]', roleId); // Use roles[] for array input
    });

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
      
      formData.append('doc', this.fileToUpload, this.fileToUpload.name);
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
  
      // Populate role checkboxes
      const rolesContainer = document.getElementById('rolesEditContainer') as HTMLElement;
      rolesContainer.innerHTML = ''; // Clear existing checkboxes
      if (this.rolesArray && Array.isArray(this.rolesArray)) {
        this.rolesArray.forEach((role: { id: string; name: string }) => {
          const checkbox = document.createElement('input');
          checkbox.type = 'checkbox';
          checkbox.id = `edit_${role.id}`;
          checkbox.value = role.id;
          // Check if the role is in the selectedRoleIds array
          checkbox.checked = this.selectedRoleIds.includes(String(role.id));

          // Use a method to handle checkbox changes
          checkbox.addEventListener('change', (event) => this.handleRoleChange(event, role.id));
  
          const label = document.createElement('label');
          label.htmlFor = `edit_${role.id}`; // Use the updated ID here
          label.textContent = role.name;
  
          rolesContainer.appendChild(checkbox);
          rolesContainer.appendChild(label);
          rolesContainer.appendChild(document.createElement('br')); // For spacing
        });
      } else {
        console.error('Roles is not defined or is not an array');
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
  viewDocument(docId: number): void {
    if (Array.isArray(this.userRoleIds) && this.userRoleIds.length > 0) {
        this.userRoleIds.forEach(roleId => {
            this.AdminDocumentService.showDocument(docId, roleId).subscribe({
                next: (blob) => {
                    if (blob) {
                        const url = window.URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.target = '_blank';
                        link.click();
                        window.URL.revokeObjectURL(url);
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
        console.warn('No valid role IDs found');
        alert('No valid role IDs found. Please contact support.');
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


  
}
