import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { AirportService } from '../../services/airport.service';

declare var bootstrap: any;

@Component({
  selector: 'app-airport-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './airport-datatable.component.html',
  styleUrl: './airport-datatable.component.css'
})
export class AirportDatatableComponent {

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

  constructor(private airportService: AirportService) {}

  ngOnInit(): void {
    this.loadList();
  }

  loadList(): void {
    this.apiBasePath=environment.apiDocBaseUrl;
    this.loading = true; // Start loading
    this.airportService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
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
      event.date_of_approval_clearance = new Date(event.date_of_approval_clearance).toLocaleDateString('en-GB');
      event.date_of_approval_programme = new Date(event.date_of_approval_programme).toLocaleDateString('en-GB');
      event.valid_till = new Date(event.valid_till).toLocaleDateString('en-GB');
    });
  }

  editEvent(id: number): void {
    this.airportService.getEvent(id).subscribe(data => {
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
      'region_name','lang_code','sr_no','airport_name','entity_name','address','mobile_no','phone_no','unique_reference_number','approved_status_clearance','date_of_approval_clearance','approved_status_programme','date_of_approval_programme','valid_till'
    ];
    
    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);

    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }

    // Create FormData to submit
    const formData = new FormData();
    // formData.append('airport_orders', this.selectedEvent.airport_orders);
    formData.append('region_name', this.selectedEvent.region_name);
    formData.append('lang_code', this.selectedEvent.lang_code);
    formData.append('sr_no', this.selectedEvent.sr_no);
    formData.append('airport_name', this.selectedEvent.airport_name);
    formData.append('entity_name', this.selectedEvent.entity_name);
    formData.append('address', this.selectedEvent.address);
    formData.append('mobile_no', this.selectedEvent.mobile_no);
    formData.append('phone_no', this.selectedEvent.phone_no);
    formData.append('unique_reference_number', this.selectedEvent.unique_reference_number);
    formData.append('approved_status_clearance', this.selectedEvent.approved_status_clearance);
    formData.append('date_of_approval_clearance', this.selectedEvent.date_of_approval_clearance);
    formData.append('approved_status_programme', this.selectedEvent.approved_status_programme);
    formData.append('date_of_approval_programme', this.selectedEvent.date_of_approval_programme);
    formData.append('valid_till', this.selectedEvent.valid_till);

    // Now, send the formData to the backend
    this.airportService.storeEvent(formData).subscribe(
      response => {
          alert(response.message || 'Created successfully!');
          this.closeAddModal(); // Close the modal or form
          this.loadList(); // Refresh the list of events
          
      },
      error => {
        // Check if the error contains validation messages (assuming error is an object)
        let errorMessage = 'An error occurred while saving.';
  
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
      'region_name','lang_code','sr_no','airport_name','entity_name','address','mobile_no','phone_no','unique_reference_number','approved_status_clearance','date_of_approval_clearance','approved_status_programme','date_of_approval_programme','valid_till'
    ];
  
    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);
  
    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }
  
    // Prepare data for submission
    const formData = new FormData();
    // formData.append('airport_orders', this.selectedEvent.airport_orders);
    formData.append('region_name', this.selectedEvent.region_name);
    formData.append('lang_code', this.selectedEvent.lang_code);
    formData.append('sr_no', this.selectedEvent.sr_no);
    formData.append('airport_name', this.selectedEvent.airport_name);
    formData.append('entity_name', this.selectedEvent.entity_name);
    formData.append('address', this.selectedEvent.address);
    formData.append('mobile_no', this.selectedEvent.mobile_no);
    formData.append('phone_no', this.selectedEvent.phone_no);
    formData.append('unique_reference_number', this.selectedEvent.unique_reference_number);
    formData.append('approved_status_clearance', this.selectedEvent.approved_status_clearance);
    formData.append('date_of_approval_clearance', this.selectedEvent.date_of_approval_clearance);
    formData.append('approved_status_programme', this.selectedEvent.approved_status_programme);
    formData.append('date_of_approval_programme', this.selectedEvent.date_of_approval_programme);
    formData.append('valid_till', this.selectedEvent.valid_till);
  
    // Assuming you have a service to handle the API call
    this.airportService.updateEvent(this.selectedEvent.id,formData).subscribe(
      response => {
        alert(response.message || 'Updated Successfully!');
      
        // Close the modal (assuming you are using Bootstrap modal, you can modify as per your modal library)
        this.closeEditModal(); // Define this method to close the modal

        // Optionally, reload the list of events (or handle the updated event)
        this.loadList();
      },
      error => {
        // Check if the error contains validation messages (assuming error is an object)
        let errorMessage = 'An error occurred while saving.';
  
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
    if (confirm('Are you sure you want to delete?')) {
      this.airportService.deleteEvent(id).subscribe(() => {
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
  

}
