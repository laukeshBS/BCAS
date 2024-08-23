import { Component } from '@angular/core';
import { ActsandpoliciesService } from '../../services/actsandpolicies.service';  // Import the service
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';

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
  limit = 5; 
  lang_code = 'en'; 
  selectedFile: any;
  selectedFileError: string | null = null; // Initialized with null

  constructor(private actsAndPoliciesService: ActsandpoliciesService) {}

  ngOnInit(): void {
    this.loadList();
  }

  loadList(): void {
    this.actsAndPoliciesService.allList(this.limit, this.lang_code).subscribe(data => {
      this.events = data;
      this.formatEventDates(); // Optional: Format dates if needed
    });
  }

  formatEventDates(): void {
    this.events.forEach(event => {
      event.created_at = new Date(event.created_at).toLocaleDateString('en-GB');
      event.start_date = new Date(event.start_date).toLocaleDateString('en-GB');
      event.end_date = new Date(event.end_date).toLocaleDateString('en-GB');
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
    this.actsAndPoliciesService.getEvent(id).subscribe(data => {
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
    if (!this.selectedEvent.title || !this.selectedEvent.status || !this.selectedEvent.lang_code || 
        !this.selectedEvent.start_date || !this.selectedEvent.end_date || !this.fileToUpload) {
      console.error('Missing required fields');
      return;
    }

    const formData = new FormData();
    formData.append('title', this.selectedEvent.title);
    formData.append('description', this.selectedEvent.description);
    formData.append('status', this.selectedEvent.status);
    formData.append('lang_code', this.selectedEvent.lang_code);
    formData.append('start_date', this.selectedEvent.start_date);
    formData.append('end_date', this.selectedEvent.end_date);
    formData.append('document', this.fileToUpload, this.fileToUpload.name);

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
    if (!this.selectedEvent.title || !this.selectedEvent.status || !this.selectedEvent.lang_code || 
        !this.selectedEvent.start_date || !this.selectedEvent.end_date) {
      console.error('Missing required fields');
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
      formData.append('document', this.fileToUpload, this.fileToUpload.name);
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
      }
    }
  }
}
