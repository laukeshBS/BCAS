import { Component } from '@angular/core';
import { RegionService } from '../../services/region.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';

declare var bootstrap: any;

@Component({
  selector: 'app-regiondatatable',
  standalone: true,
  imports: [CommonModule,FormsModule],
  templateUrl: './regiondatatable.component.html',
  styleUrl: './regiondatatable.component.css'
})
export class RegiondatatableComponent {
  events: any[] = [];
  selectedEvent: any = {};
  fileToUpload: File | null = null;
  limit = 5; 
  lang_code = 'en'; 
  selectedFile: any;
  selectedFileError: string | null = null; // Initialized with null

  constructor(private regionService: RegionService) {}

  ngOnInit(): void {
    this.loadList();
  }

  

  loadList(): void {
    this.regionService.allList(this.limit, this.lang_code).subscribe(data => {
      this.events = data;
      this.formatEventDates(); // Optional: Format dates if needed
    });
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
        event.document = '<a href="'+event.document+'">'+event.title+' Document</a>';
      }else{
        event.document = '';
      }
    });
  }

  editEvent(id: number): void {
    this.regionService.getEvent(id).subscribe(data => {
      this.selectedEvent = data;
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
      'name',
      'status',
      'lang_code',
    ];
    
    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);

    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }

    const formData = new FormData();
    formData.append('name', this.selectedEvent.name);
    formData.append('status', this.selectedEvent.status);
    formData.append('lang_code', this.selectedEvent.lang_code);

    this.regionService.storeEvent(formData).subscribe(
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
      'name',
      'status',
      'lang_code',
    ];
    
    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);

    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }

    const formData = new FormData();
    formData.append('name', this.selectedEvent.name);
    formData.append('status', this.selectedEvent.status);
    formData.append('lang_code', this.selectedEvent.lang_code);

    this.regionService.updateEvent(this.selectedEvent.id, formData).subscribe(
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
      this.regionService.deleteEvent(id).subscribe(() => {
        this.events = this.events.filter(event => event.id !== id);
        alert('Deleted Successfully!');
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
