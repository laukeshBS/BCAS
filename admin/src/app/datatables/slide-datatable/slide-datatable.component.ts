import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';
import { SlideService } from '../../services/slide.service';
import { environment } from '../../environments/environment';

declare var bootstrap: any;

@Component({
  selector: 'app-slide-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './slide-datatable.component.html',
  styleUrl: './slide-datatable.component.css'
})
export class SlideDatatableComponent {

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
  apiBasePath='';

  constructor(private slideService: SlideService) {}

  ngOnInit(): void {
    this.loadList();
  }

  loadList(): void {
    this.apiBasePath=environment.apiDocBaseUrl;
    this.loading = true; // Start loading
    this.slideService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
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
      // if (event.media!='') {
      //   event.media = '<a href="'+this.apiBasePath+'public/documents/'+event.media+'">'+event.title+' Document</a>';
      // }else{
      //   event.media = '';
      // }
    });
  }

  editEvent(id: number): void {
    this.slideService.getEvent(id).subscribe(data => {
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
    if (!this.selectedEvent.slider_id || !this.selectedEvent.title || !this.selectedEvent.status || !this.selectedEvent.lang_code || !this.selectedEvent.media_type || !this.selectedEvent.order_index) {
      console.error('Missing required fields');
      return;
    }

    const formData = new FormData();
    formData.append('slider_id', this.selectedEvent.slider_id);
    formData.append('title', this.selectedEvent.title);
    formData.append('description', this.selectedEvent.description);
    formData.append('status', this.selectedEvent.status);
    formData.append('lang_code', this.selectedEvent.lang_code);
    formData.append('url', this.selectedEvent.url);
    formData.append('media_type', this.selectedEvent.media_type);
    formData.append('order_index', this.selectedEvent.order_index);

    if (this.fileToUpload) {
      formData.append('media', this.fileToUpload, this.fileToUpload.name);
    }

    this.slideService.storeEvent(formData).subscribe(
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
    if (!this.selectedEvent.slider_id || !this.selectedEvent.title || !this.selectedEvent.status || !this.selectedEvent.lang_code || !this.selectedEvent.media_type || !this.selectedEvent.order_index) {
      console.error('Missing required fields');
      return;
    }

    const formData = new FormData();
    formData.append('slider_id', this.selectedEvent.slider_id);
    formData.append('title', this.selectedEvent.title);
    formData.append('description', this.selectedEvent.description);
    formData.append('status', this.selectedEvent.status);
    formData.append('lang_code', this.selectedEvent.lang_code);
    formData.append('url', this.selectedEvent.url);
    formData.append('media_type', this.selectedEvent.media_type);
    formData.append('order_index', this.selectedEvent.order_index);

    if (this.fileToUpload) {
      formData.append('media', this.fileToUpload, this.fileToUpload.name);
    }

    this.slideService.updateEvent(this.selectedEvent.id, formData).subscribe(
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
      this.slideService.deleteEvent(id).subscribe(() => {
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

}
