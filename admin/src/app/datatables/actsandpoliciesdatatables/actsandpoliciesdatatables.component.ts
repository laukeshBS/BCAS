import { Component } from '@angular/core';
import { ActsandpoliciesService } from '../../services/actsandpolicies.service';  // Import the service
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';
import { environment } from '../../environments/environment';

declare var bootstrap: any;

@Component({
  selector: 'app-actsandpoliciesdatatables',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './actsandpoliciesdatatables.component.html',
  styleUrl: './actsandpoliciesdatatables.component.css'
})
export class ActsandpoliciesdatatablesComponent {
  events: any[] = [];
  selectedEvent: any = {};
  fileToUpload: File | null = null;
  limit = 10; 
  lang_code = 'en'; 
  selectedFile: any;
  selectedFileError: string | null = null; // Initialized with null
  apiBasePath='';

  constructor(private actsAndPoliciesService: ActsandpoliciesService) {}

  ngOnInit(): void {
    this.loadList();
  }

  loading: boolean = false;
  currentPage: number = 1;
  totalItems: number = 0;
  lastPage: number = 0;

  loadList(): void {
    this.loading = true; // Start loading
    this.apiBasePath=environment.apiDocBaseUrl;
    this.actsAndPoliciesService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
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
      if (event.document!='') {
        event.document = '<a href="'+this.apiBasePath+'public/documents/'+event.document+'">'+event.title+' Document</a>';
      }else{
        event.document = '';
      }
    });
  }

  editEvent(id: number): void {
    this.actsAndPoliciesService.getEvent(id).subscribe(data => {
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
      'start_date',
      'end_date',
    ];
    
    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);
    if (!this.fileToUpload) {
      missingFields.push('document');
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
    formData.append('start_date', this.selectedEvent.start_date);
    formData.append('end_date', this.selectedEvent.end_date);
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

    this.actsAndPoliciesService.storeEvent(formData).subscribe(
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
    const requiredFields = [
      'title',
      'lang_code',
      'status',
      'start_date',
      'end_date',
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
    formData.append('start_date', this.selectedEvent.start_date);
    formData.append('end_date', this.selectedEvent.end_date);
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

    this.actsAndPoliciesService.updateEvent(this.selectedEvent.id, formData).subscribe(
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
      this.actsAndPoliciesService.deleteEvent(id).subscribe(() => {
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
