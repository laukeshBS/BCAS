import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { AstiService } from '../../services/asti.service';

declare var bootstrap: any;

@Component({
  selector: 'app-asti-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './asti-datatable.component.html',
  styleUrl: './asti-datatable.component.css'
})
export class AstiDatatableComponent {

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

  constructor(private astiService: AstiService) {}

  ngOnInit(): void {
    this.loadList();
  }

  loadList(): void {
    this.apiBasePath=environment.apiDocBaseUrl;
    this.loading = true; // Start loading
    this.astiService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
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
      event.bcas_date_of_approval = new Date(event.bcas_date_of_approval).toLocaleDateString('en-GB');
    });
  }

  editEvent(id: number): void {
    this.astiService.getEvent(id).subscribe(data => {
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
      'region_name','e_file_no','asti_name','asti_location','date_of_approval','in_principle_provisional','bcas_date_of_approval','approved_by_bcas','letter_no','lang_code'
    ];
    
    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);

    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }

    // Create FormData to submit
    const formData = new FormData();
    formData.append('region_name', this.selectedEvent.region_name);
    formData.append('e_file_no', this.selectedEvent.e_file_no);
    formData.append('asti_name', this.selectedEvent.asti_name);
    formData.append('asti_location', this.selectedEvent.asti_location);
    formData.append('date_of_approval', this.selectedEvent.date_of_approval);
    formData.append('in_principle_provisional', this.selectedEvent.in_principle_provisional);
    formData.append('bcas_date_of_approval', this.selectedEvent.bcas_date_of_approval);
    formData.append('approved_by_bcas', this.selectedEvent.approved_by_bcas);
    formData.append('letter_no', this.selectedEvent.letter_no);
    formData.append('lang_code', this.selectedEvent.lang_code);

    // Now, send the formData to the backend
    this.astiService.storeEvent(formData).subscribe(
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
      'region_name','e_file_no','asti_name','asti_location','date_of_approval','in_principle_provisional','bcas_date_of_approval','approved_by_bcas','letter_no','lang_code'
    ];
  
    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);
  
    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }
  
    // Prepare data for submission
    const formData = new FormData();
    formData.append('region_name', this.selectedEvent.region_name);
    formData.append('e_file_no', this.selectedEvent.e_file_no);
    formData.append('asti_name', this.selectedEvent.asti_name);
    formData.append('asti_location', this.selectedEvent.asti_location);
    formData.append('date_of_approval', this.selectedEvent.date_of_approval);
    formData.append('in_principle_provisional', this.selectedEvent.in_principle_provisional);
    formData.append('bcas_date_of_approval', this.selectedEvent.bcas_date_of_approval);
    formData.append('approved_by_bcas', this.selectedEvent.approved_by_bcas);
    formData.append('letter_no', this.selectedEvent.letter_no);
    formData.append('lang_code', this.selectedEvent.lang_code);
  
    // Assuming you have a service to handle the API call
    this.astiService.updateEvent(this.selectedEvent.id,formData).subscribe(
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
      this.astiService.deleteEvent(id).subscribe(() => {
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
