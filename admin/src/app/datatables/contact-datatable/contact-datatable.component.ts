import { Component } from '@angular/core';
import { ContactService } from '../../services/contact.service';  // Import the service
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';

declare var bootstrap: any;

@Component({
  selector: 'app-contact-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './contact-datatable.component.html',
  styleUrl: './contact-datatable.component.css'
})
export class ContactDatatableComponent {
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

  constructor(private ContactService: ContactService) {}

  ngOnInit(): void {
    this.loadList();
  }

  loadList(): void {
    this.loading = true; // Start loading
    this.ContactService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
      this.events = data.data;
      this.totalItems = data.total; // Assuming the API returns total items
      this.lastPage = Math.ceil(this.totalItems / this.limit);
      console.log('Total Items:', this.totalItems);
    console.log('Current Page:', this.currentPage);
    console.log('Last Page:', this.lastPage);
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
    });
  }

  editEvent(id: number): void {
    this.ContactService.getEvent(id).subscribe(data => {
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
      'name',
      'rank',
      'phone',
      'email',
      'type',
      'lang_code',
      'status',
    ];

    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);
  
    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }

    const formData = new FormData();
    formData.append('name', this.selectedEvent.name);
    formData.append('rank', this.selectedEvent.rank);
    formData.append('phone', this.selectedEvent.phone);
    formData.append('email', this.selectedEvent.email);
    formData.append('type', this.selectedEvent.type);
    formData.append('division_id', this.selectedEvent.division_id);
    formData.append('region_id', this.selectedEvent.region_id);
    formData.append('lang_code', this.selectedEvent.lang_code);
    formData.append('status', this.selectedEvent.status);
    formData.append('positions', this.selectedEvent.positions);

    this.ContactService.storeEvent(formData).subscribe(
      (event: HttpEvent<any>) => {
          this.loadList(); // Refresh the list of events
          this.closeAddModal(); // Close the modal or form
      },
      error => {
        alert('Error saving event: '+ error);
      }
    );
  }

  modifyEvent(): void {
    // Validate the form data
    const requiredFields = [
      'name',
      'rank',
      'phone',
      'email',
      'type',
      'lang_code',
      'status',
    ];

    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);
  
    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }

    const formData = new FormData();
    formData.append('name', this.selectedEvent.name);
    formData.append('rank', this.selectedEvent.rank);
    formData.append('phone', this.selectedEvent.phone);
    formData.append('email', this.selectedEvent.email);
    formData.append('type', this.selectedEvent.type);
    formData.append('division_id', this.selectedEvent.division_id);
    formData.append('region_id', this.selectedEvent.region_id);
    formData.append('lang_code', this.selectedEvent.lang_code);
    formData.append('status', this.selectedEvent.status);
    formData.append('positions', this.selectedEvent.positions);


    this.ContactService.updateEvent(this.selectedEvent.id, formData).subscribe(
      (event: HttpEvent<any>) => {
          this.loadList(); // Refresh the list of events
          this.closeEditModal(); // Close the modal or form
      },
      error => {
        alert('Error saving event: '+ error);
      }
    );
  }

  deleteEvent(id: number): void {
    if (confirm('Are you sure you want to delete this event?')) {
      this.ContactService.deleteEvent(id).subscribe(() => {
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
