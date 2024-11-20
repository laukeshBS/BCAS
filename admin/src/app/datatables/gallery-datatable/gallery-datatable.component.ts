import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { GalleryService } from '../../services/gallery.service';
import { DomSanitizer } from '@angular/platform-browser';

declare var bootstrap: any;

@Component({
  selector: 'app-gallery-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './gallery-datatable.component.html',
  styleUrl: './gallery-datatable.component.css'
})
export class GalleryDatatableComponent {
  events: any[] = [];
  selectedEvent: any = {};
  fileToUpload: File | null = null;
  limit = 5; 
  lang_code = 'en'; 
  selectedFile: any;
  selectedFileError: string | null = null; // Initialized with null
  currentPage: number = 1; // Current page for pagination
  totalItems: number = 0; // Total items to calculate total pages
  loading: boolean = false;
  lastPage: number = 0; // Last page]
  userId: number | null = null;
  apiBasePath='';

  constructor(private galleryService: GalleryService, private sanitizer: DomSanitizer) {}

  ngOnInit(): void {
    this.loadList();
  }

  loadList(): void {
    this.apiBasePath=environment.apiDocBaseUrl;
    this.loading = true; // Start loading
    this.galleryService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
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
      if (event.image!='') {
        let imageHTML = '<img src="'+this.apiBasePath+'public/gallery/'+event.image+'" alt="'+event.title+'" style="max-width:100px" />';
        event.image = this.sanitizer.bypassSecurityTrustHtml(imageHTML) as string;
      }else{
        event.image = '';
      }
    });
  }

  editEvent(id: number): void {
    this.galleryService.getEvent(id).subscribe(data => {
      this.selectedEvent = data;
      console.log(this.selectedEvent);
      this.openEditModal();
    });
  }
  addEvent(): void {
    const modalElement = document.getElementById('addEventModal');
    if (modalElement) {
      this.selectedEvent = {};
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
    }
  }

  saveEvent(): void {
    // Validate the form data
    const requiredFields = [
      'title',
      'lang_code',
      'status',
      'position',
    ];
    
    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);
    
    if (!this.fileToUpload) {
      missingFields.push('image');
    }

    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }

    const formData = new FormData();
    formData.append('title', this.selectedEvent.title);
    formData.append('description', this.selectedEvent.description);
    formData.append('status', this.selectedEvent.status);
    formData.append('lang_code', this.selectedEvent.lang_code);
    formData.append('position', this.selectedEvent.position);

    // Append file only if it's present
    if (this.fileToUpload) {
      const validFileTypes = ['image/jpeg', 'image/png']; // Example types
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
        
      formData.append('image', this.fileToUpload, sanitizedFileName);
    }

    this.galleryService.storeEvent(formData).subscribe(
      response => {
        alert(response.message || 'Created Successfully!');
        this.closeAddModal(); // Close the modal or form
        this.loadList(); // Refresh the list of events
        
      },
      error => {
        // Check if the error contains validation messages (assuming error is an object)
        let errorMessage = 'An error occurred while saving.';

        // Check if error contains a response body
        if (error && error.error && error.error.errors) {
          // Loop through the 'errors' object and join all error messages
          let errorMessages = Object.values(error.error.errors).flat();
          errorMessage = errorMessages.join(', ');
        }

        // Display the error message in an alert
        alert(errorMessage);
      }
    );
  }
  modifyEvent(): void {
    // Validate the form data
    const requiredFields = [
      'title',
      'lang_code',
      'status',
      'position',
    ];
    
    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);

    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }

    const formData = new FormData();
    formData.append('title', this.selectedEvent.title);
    formData.append('description', this.selectedEvent.description);
    formData.append('status', this.selectedEvent.status);
    formData.append('lang_code', this.selectedEvent.lang_code);
    formData.append('position', this.selectedEvent.position);

    // Append file only if it's present
    if (this.fileToUpload) {
      const validFileTypes = ['image/jpeg', 'image/png']; // Example types
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
        
      formData.append('image', this.fileToUpload, sanitizedFileName);
    }

    this.galleryService.updateEvent(this.selectedEvent.id, formData).subscribe(
      response => {
        alert(response.message || 'Updated Successfully!');
        this.closeEditModal(); // Close the modal or form
        this.loadList(); // Refresh the list of events
        
      },
      error => {
        // Check if the error contains validation messages (assuming error is an object)
        let errorMessage = 'An error occurred while saving.';

        // Check if error contains a response body
        if (error && error.error && error.error.errors) {
          // Loop through the 'errors' object and join all error messages
          let errorMessages = Object.values(error.error.errors).flat();
          errorMessage = errorMessages.join(', ');
        }

        // Display the error message in an alert
        alert(errorMessage);
      }
    );
  }

  deleteEvent(id: number): void {
    if (confirm('Are you sure you want to delete this event?')) {
      this.galleryService.deleteEvent(id).subscribe(() => {
        this.events = this.events.filter(event => event.id !== id);
        alert('Deleted Successfully');
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

}
