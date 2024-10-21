import { Component } from '@angular/core';
import { AdminDocumentCategoryService } from '../../services/admin-document-category.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';

declare var bootstrap: any;

@Component({
  selector: 'app-admin-document-category-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './admin-document-category-datatable.component.html',
  styleUrl: './admin-document-category-datatable.component.css'
})
export class AdminDocumentCategoryDatatableComponent {
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
  userId: number | null = null;
  userRoleIds: number[] | [] = [];
  

  constructor(private AdminDocumentCategoryService: AdminDocumentCategoryService) {}

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
    this.AdminDocumentCategoryService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
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
    });
  }
  
  editEvent(id: number): void {
    this.AdminDocumentCategoryService.getEvent(id).subscribe(data => {
      this.selectedEvent = data;
      console.log(this.selectedEvent);
      this.openEditModal();
    });
  }

  addEvent(): void {
    const modalElement = document.getElementById('addEventModal');
    if (modalElement) {
      this.selectedEvent = { name: '', status: '' };
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
    }
  }

  saveEvent(): void {
    // Validate the form data
    if (!this.selectedEvent.name || !this.selectedEvent.status) {
      console.error('Missing required fields');
      return;
    }

    const formData = new FormData();
    formData.append('name', this.selectedEvent.name);
    formData.append('status', this.selectedEvent.status);

    this.AdminDocumentCategoryService.storeEvent(formData).subscribe(
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
    if (!this.selectedEvent.name || !this.selectedEvent.status)  {
      console.error('Missing required fields');
      return;
    }

    const formData = new FormData();
    formData.append('name', this.selectedEvent.name);
    formData.append('status', this.selectedEvent.status);

    this.AdminDocumentCategoryService.updateEvent(this.selectedEvent.id, formData).subscribe(
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
      this.AdminDocumentCategoryService.deleteEvent(id).subscribe(() => {
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
}
