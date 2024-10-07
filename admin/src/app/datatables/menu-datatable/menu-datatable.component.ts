import { Component } from '@angular/core';
import { MenuService } from '../../services/menu.service';  // Import the service
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';

declare var bootstrap: any;

@Component({
  selector: 'app-menu-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './menu-datatable.component.html',
  styleUrl: './menu-datatable.component.css'
})
export class MenuDatatableComponent {
  events: any[] = [];
  selectedEvent: any = {};
  fileToUpload: File | null = null;
  limit = 10; 
  lang_code = 'en'; 
  selectedFile: any;
  selectedFileError: string | null = null; // Initialized with null
  currentPage: number = 1; // Current page for pagination
  totalItems: number = 0; // Total items to calculate total pages
  loading: boolean = false;
  lastPage: number = 0; // Last page
  fileToUploadDoc: File | null = null;
  fileToUploadBanner: File | null = null;
  fileToUploadImg: File | null = null;
  userId: number | null = null;

  constructor(private MenuService: MenuService) {}

  ngOnInit(): void {
    this.loadList();
    this.loadUserId();
  }

  loadList(): void {
    this.loading = true; // Start loading
    this.MenuService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
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
    console.log('Changing to page:', page); // Debugging line
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
      if (event.status==1) {
        event.status = 'Active';
      }else{
        event.status = 'Inactive';
      }
      if (event.document!='') {
        event.document = '<a href="'+event.document+'">'+event.title+' Document</a>';
      }else{
        event.document = '';
      }
    });
  }

  editEvent(id: number): void {
    this.MenuService.getEvent(id).subscribe(data => {
      this.selectedEvent = data;
      console.log(this.selectedEvent);
      this.openEditModal();
    });
  }

  addEvent(): void {
    const modalElement = document.getElementById('addEventModal');
    if (modalElement) {
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
    }
  }

  saveEvent(): void {
    // Validate the form data
    if (!this.selectedEvent.menu_type || !this.selectedEvent.language_id || !this.selectedEvent.menu_name || !this.selectedEvent.page_order || !this.selectedEvent.menu_position || !this.selectedEvent.menu_url || !this.selectedEvent.menu_title || !this.selectedEvent.approve_status || !this.selectedEvent.menu_description) {
      console.error('Missing required fields');
      return;
    }

    const formData = new FormData();
    formData.append('menu_type', this.selectedEvent.menu_type);
    formData.append('language_id', this.selectedEvent.language_id);
    formData.append('menu_name', this.selectedEvent.menu_name);
    formData.append('page_order', this.selectedEvent.page_order);
    formData.append('menu_position', this.selectedEvent.menu_position);
    formData.append('menu_url', this.selectedEvent.menu_url);
    formData.append('menu_title', this.selectedEvent.menu_title);
    formData.append('approve_status', this.selectedEvent.approve_status);
    formData.append('menu_description', this.selectedEvent.menu_description);
    formData.append('menu_child_id', this.selectedEvent.menu_child_id);
    if (this.fileToUploadDoc) {
      formData.append('doc_upload', this.fileToUploadDoc, this.fileToUploadDoc.name);
    }
    if (this.fileToUploadBanner) {
      formData.append('banner_img', this.fileToUploadBanner, this.fileToUploadBanner.name);
    }
    if (this.fileToUploadImg) {
      formData.append('img_upload', this.fileToUploadImg, this.fileToUploadImg.name);
    }
    if (this.userId) {
      formData.append('created_by', this.userId.toString()); // Convert to string
    }

    this.MenuService.storeEvent(formData).subscribe(
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
    if (!this.selectedEvent.menu_type || !this.selectedEvent.language_id || !this.selectedEvent.menu_name || !this.selectedEvent.page_order || !this.selectedEvent.menu_position || !this.selectedEvent.menu_url || !this.selectedEvent.menu_title || !this.selectedEvent.approve_status || !this.selectedEvent.menu_description) {
      console.error('Missing required fields');
      return;
    }

    const formData = new FormData();
    formData.append('menu_type', this.selectedEvent.menu_type);
    formData.append('language_id', this.selectedEvent.language_id);
    formData.append('menu_name', this.selectedEvent.menu_name);
    formData.append('page_order', this.selectedEvent.page_order);
    formData.append('menu_position', this.selectedEvent.menu_position);
    formData.append('menu_url', this.selectedEvent.menu_url);
    formData.append('menu_title', this.selectedEvent.menu_title);
    formData.append('approve_status', this.selectedEvent.approve_status);
    formData.append('menu_description', this.selectedEvent.menu_description);
    formData.append('menu_child_id', this.selectedEvent.menu_child_id);
    if (this.fileToUploadDoc) {
      formData.append('doc_upload', this.fileToUploadDoc, this.fileToUploadDoc.name);
    }
    if (this.fileToUploadBanner) {
      formData.append('banner_img', this.fileToUploadBanner, this.fileToUploadBanner.name);
    }
    if (this.fileToUploadImg) {
      formData.append('img_upload', this.fileToUploadImg, this.fileToUploadImg.name);
    }
    if (this.userId) {
      formData.append('created_by', this.userId.toString()); // Convert to string
    }

    this.MenuService.updateEvent(this.selectedEvent.id, formData).subscribe(
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
      this.MenuService.deleteEvent(id).subscribe(() => {
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

  onFileChange(event: any, type: string): void {
    if (event.target.files.length > 0) {
      const file = event.target.files[0];
      const validTypes = ['image/jpeg', 'image/png', 'application/pdf']; // Adjust types as needed
      if (!validTypes.includes(file.type)) {
        this.selectedFileError = 'Invalid file type. Only JPG, PNG, and PDF are allowed.';
        return;
      }
      if (file.size > 5000000) {
        this.selectedFileError = 'File size exceeds 5MB limit.';
        return;
      }
      switch (type) {
        case 'doc_upload':
          this.fileToUploadDoc = file;
          break;
        case 'banner_img':
          this.fileToUploadBanner = file;
          break;
        case 'img_upload':
          this.fileToUploadImg = file;
          break;
      }

      this.selectedFileError = null; // Clear any previous error
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
  loadUserId(): void {
    const userData = localStorage.getItem('user'); // Retrieve user data from localStorage
    if (userData) {
      const user = JSON.parse(userData); // Parse the JSON string back to an object
      this.userId = user.id; // Assign the user ID
      console.log('User ID:', this.userId); // Log the user ID for verification
    } else {
      console.warn('No user data found in localStorage');
    }
  }
}