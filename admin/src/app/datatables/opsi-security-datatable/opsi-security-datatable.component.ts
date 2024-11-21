import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';
import { environment } from '../../environments/environment';import { OpsiSecurityService } from '../../services/opsi-security.service';

declare var bootstrap: any;

@Component({
  selector: 'app-opsi-security-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './opsi-security-datatable.component.html',
  styleUrl: './opsi-security-datatable.component.css'
})
export class OpsiSecurityDatatableComponent {

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

  constructor(private opsiSecurityService: OpsiSecurityService) {}

  ngOnInit(): void {
    this.loadList();
  }

  loadList(): void {
    this.apiBasePath=environment.apiDocBaseUrl;
    this.loading = true; // Start loading
    this.opsiSecurityService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
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
      event.date_of_application_submitted = new Date(event.date_of_application_submitted).toLocaleDateString('en-GB');
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
        case "axu":
          event.sec_type = 'Auxiliary';
          break;
        case "cat":
          event.sec_type = 'Catering';
          break;
        case "con":
          event.sec_type = 'Concessionaire';
          break;
        case "fbo":
          event.sec_type = 'Fixed Base Operator';
          break;
        case "ff":
          event.sec_type = 'Fuel Farm';
          break;
        case "gas":
          event.sec_type = 'Gas';
          break;
        case "gha":
          event.sec_type = 'Ground Handling Agency';
          break;
        case "psa":
          event.sec_type = 'PSA';
          break;
        case "ra":
          event.sec_type = 'Regulated Agent';
          break;
        default:
          event.sec_type = '';
          break;
      }
    });
  }

  editEvent(id: number): void {
    this.opsiSecurityService.getEvent(id).subscribe(data => {
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
      'application_id','company_name','date_of_application_submitted','date_of_approval','status','positions','sec_type','date_of_validity','lang_code'
    ];
    
    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);

    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }

    // Create FormData to submit
    const formData = new FormData();
    formData.append('application_id', this.selectedEvent.application_id);
    formData.append('company_name', this.selectedEvent.company_name);
    formData.append('date_of_application_submitted', this.selectedEvent.date_of_application_submitted);
    formData.append('date_of_approval', this.selectedEvent.date_of_approval);
    formData.append('status', this.selectedEvent.status);
    formData.append('positions', this.selectedEvent.positions);
    // formData.append('division', this.selectedEvent.division);
    formData.append('sec_type', this.selectedEvent.sec_type);
    formData.append('date_of_validity', this.selectedEvent.date_of_validity);
    formData.append('lang_code', this.selectedEvent.lang_code);

    // Now, send the formData to the backend
    this.opsiSecurityService.storeEvent(formData).subscribe(
      response => {
          alert(response.message || 'Created Successfully!');
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
      'application_id','company_name','date_of_application_submitted','date_of_approval','status','positions','positions','sec_type','date_of_validity','lang_code'
    ];
  
    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);
  
    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }
  
    // Prepare data for submission
    const formData = new FormData();
    formData.append('application_id', this.selectedEvent.application_id);
    formData.append('company_name', this.selectedEvent.company_name);
    formData.append('date_of_application_submitted', this.selectedEvent.date_of_application_submitted);
    formData.append('date_of_approval', this.selectedEvent.date_of_approval);
    formData.append('status', this.selectedEvent.status);
    formData.append('positions', this.selectedEvent.positions);
    // formData.append('division', this.selectedEvent.division);
    formData.append('sec_type', this.selectedEvent.sec_type);
    formData.append('date_of_validity', this.selectedEvent.date_of_validity);
    formData.append('lang_code', this.selectedEvent.lang_code);
  
    // Assuming you have a service to handle the API call
    this.opsiSecurityService.updateEvent(this.selectedEvent.id,formData).subscribe(
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
    if (confirm('Are you sure you want to delete this event?')) {
      this.opsiSecurityService.deleteEvent(id).subscribe(() => {
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
