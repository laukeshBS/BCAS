import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { OpsSecurityService } from '../../services/ops-security.service';

declare var bootstrap: any;

@Component({
  selector: 'app-ops-security-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './ops-security-datatable.component.html',
  styleUrl: './ops-security-datatable.component.css'
})
export class OpsSecurityDatatableComponent {

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
  lastPage: number = 0; // Last page]
  userId: number | null = null;
  apiBasePath='';

  constructor(private opsSecurityService: OpsSecurityService) {}

  ngOnInit(): void {
    this.loadList();
  }

  loadList(): void {
    this.apiBasePath=environment.apiDocBaseUrl;
    this.loading = true; // Start loading
    this.opsSecurityService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
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
  // Get the page numbers to display in the pagination
  getPageNumbers(): number[] {
    const pagesToShow: number[] = [];
    const range = 2;  // Number of pages before and after current page to show
    const start = Math.max(2, this.currentPage - range); // Ensure at least 2 pages before current
    const end = Math.min(this.lastPage - 1, this.currentPage + range); // Ensure at least 2 pages after current

    // Generate the pages to show
    for (let i = start; i <= end; i++) {
      pagesToShow.push(i);
    }
    return pagesToShow;
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
      event.date_of_approval = new Date(event.date_of_approval).toLocaleDateString('en-GB');
      event.date_of_validity = new Date(event.date_of_validity).toLocaleDateString('en-GB');
      switch (event.status) {
        case "1":
          event.status = 'Draft';
          break;
        case "2":
          event.status = 'Pending';
          break;
        case "3":
          event.status = 'Published';
          break;
        default:
          event.status = '';
          break;
      }
      switch (event.sec_type) {
        case "asp":
          event.sec_type = 'Aerodrome security programme';
          break;
        case "ffsp":
          event.sec_type = '=Fuel farm Security programme';
          break;
        case "gha":
          event.sec_type = 'Ground Handling Agent';
          break;
        case "mr":
          event.sec_type = 'Maintenance repair';
          break;
        case "fbo":
          event.sec_type = 'Fixed Base operator';
          break;
        case "psa":
          event.sec_type = 'Private Security Agency';
          break;
        case "aa":
          event.sec_type = 'Authorised agent';
          break;
        case "phg":
          event.sec_type = 'Power Hang Glider';
          break;
        case "rpa":
          event.sec_type = 'Remotely Piloted Aircraft';
          break;
        default:
          event.sec_type = '';
          break;
      }
    });
  }

  editEvent(id: number): void {
    this.opsSecurityService.getEvent(id).subscribe(data => {
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
      'application_id',
      'entity_name',
      'airport_name',
      'region_name',
      'cso_acso_name',
      'cso_acso_email',
      'cso_acso_mobile',
      'station_name',
      'date_of_approval',
      'status',
      'sec_type',
      'date_of_validity',
      'lang_code',
    ];
    
    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);

    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }

    // Create FormData to submit
    const formData = new FormData();
    formData.append('application_id', this.selectedEvent.application_id);
    formData.append('entity_name', this.selectedEvent.entity_name);
    formData.append('airport_name', this.selectedEvent.airport_name);
    formData.append('region_name', this.selectedEvent.region_name);
    formData.append('cso_acso_name', this.selectedEvent.cso_acso_name);
    formData.append('cso_acso_email', this.selectedEvent.cso_acso_email);
    formData.append('cso_acso_mobile', this.selectedEvent.cso_acso_mobile);
    formData.append('station_name', this.selectedEvent.station_name);
    formData.append('date_of_approval', this.selectedEvent.date_of_approval);
    formData.append('status', this.selectedEvent.status);
    formData.append('sec_type', this.selectedEvent.sec_type);
    formData.append('date_of_validity', this.selectedEvent.date_of_validity);
    formData.append('lang_code', this.selectedEvent.lang_code);

    // Now, send the formData to the backend
    this.opsSecurityService.storeEvent(formData).subscribe(
      response => {
          alert(response.message || 'Event Created successfully!');
          this.closeAddModal(); // Close the modal or form
          this.loadList(); // Refresh the list of events
          
      },
      error => {
        // Check if the error contains validation messages (assuming error is an object)
        let errorMessage = 'An error occurred while saving the event.';
  
        if (error && error.error && error.error.messages) {
          // Extract error messages from the response, assuming it's an array or object
          errorMessage = Object.values(error.error.messages).join(', ');
        }
  
        // Display the error message in an alert
        alert(errorMessage);
      }
    );
  }

  modifyEvent(): void {
    // Validate the form data
    const requiredFields = [
      'application_id',
      'entity_name',
      'airport_name',
      'region_name',
      'cso_acso_name',
      'cso_acso_email',
      'cso_acso_mobile',
      'station_name',
      'date_of_approval',
      'status',
      'sec_type',
      'date_of_validity',
      'lang_code',
    ];
  
    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);
  
    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }
  
    // Prepare data for submission
    const formData = new FormData();
    formData.append('application_id', this.selectedEvent.application_id);
    formData.append('entity_name', this.selectedEvent.entity_name);
    formData.append('airport_name', this.selectedEvent.airport_name);
    formData.append('region_name', this.selectedEvent.region_name);
    formData.append('cso_acso_name', this.selectedEvent.cso_acso_name);
    formData.append('cso_acso_email', this.selectedEvent.cso_acso_email);
    formData.append('cso_acso_mobile', this.selectedEvent.cso_acso_mobile);
    formData.append('station_name', this.selectedEvent.station_name);
    formData.append('date_of_approval', this.selectedEvent.date_of_approval);
    formData.append('status', this.selectedEvent.status);
    formData.append('sec_type', this.selectedEvent.sec_type);
    formData.append('date_of_validity', this.selectedEvent.date_of_validity);
    formData.append('lang_code', this.selectedEvent.lang_code);
  
    // Assuming you have a service to handle the API call
    this.opsSecurityService.updateEvent(this.selectedEvent.id,formData).subscribe(
      response => {
        alert(response.message || 'Event updated successfully!');
      
        // Close the modal (assuming you are using Bootstrap modal, you can modify as per your modal library)
        this.closeEditModal(); // Define this method to close the modal

        // Optionally, reload the list of events (or handle the updated event)
        this.loadList();
      },
      error => {
        // Check if the error contains validation messages (assuming error is an object)
        let errorMessage = 'An error occurred while saving the event.';
  
        if (error && error.error && error.error.messages) {
          // Extract error messages from the response, assuming it's an array or object
          errorMessage = Object.values(error.error.messages).join(', ');
        }
  
        // Display the error message in an alert
        alert(errorMessage);
      }
    );
  }
  

  deleteEvent(id: number): void {
    if (confirm('Are you sure you want to delete this event?')) {
      this.opsSecurityService.deleteEvent(id).subscribe(() => {
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
