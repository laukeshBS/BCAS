import { Component } from '@angular/core';
import { AdminDocumentService } from '../../services/admin-document.service';  // Import the service
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
  

  constructor(private AdminDocumentService: AdminDocumentService) {}

  ngOnInit(): void {
    this.loadList();
    this.loadUserId();
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

  loadList(): void {
    this.loading = true; // Start loading
    this.AdminDocumentService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
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
      console.log(this.selectedEvent);
      this.openEditModal();
    });
  }

  addEvent(): void {
    const modalElement = document.getElementById('addEventModal');
    if (modalElement) {
      this.selectedEvent = '';
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
    }
  }

  saveEvent(): void {
    // Validate the form data
    if (!this.selectedEvent.document_category_id || !this.selectedEvent.doc_name || !this.selectedEvent.doc_type || !this.selectedEvent.status || !this.selectedEvent.position || !this.selectedEvent.start_date || !this.selectedEvent.end_date || !this.fileToUpload) {
      console.error('Missing required fields');
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
    formData.append('doc', this.fileToUpload, this.fileToUpload.name);

    this.AdminDocumentService.storeEvent(formData).subscribe(
      (event: HttpEvent<any>) => {
          this.loadList(); // Refresh the list of events
          this.closeAddModal(); // Close the modal or form
      },
      error => {
        console.error('Error saving event', error);
      }
    );
  }

  modifyEvent(): void {
    // Validate the form data
    if (!this.selectedEvent.document_category_id || !this.selectedEvent.doc_name || !this.selectedEvent.doc_type || !this.selectedEvent.status || !this.selectedEvent.position || !this.selectedEvent.start_date || !this.selectedEvent.end_date || !this.fileToUpload)  {
      console.error('Missing required fields');
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
    // Append file only if it's present
    if (this.fileToUpload) {
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
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.target = '_blank';
                    link.click();
                    window.URL.revokeObjectURL(url);
                },
                error: (error) => {
                    console.error(`Error fetching document for role ID ${roleId}:`, error);
                },
            });
        });
    } else {
        console.warn('No valid role IDs found');
    }
  }

  
}
