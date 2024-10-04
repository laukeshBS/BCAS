import { Component } from '@angular/core';
import { MenuService } from '../../services/menu.service';  // Import the service
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';

declare var bootstrap: any;

@Component({
  selector: 'app-menu-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './menu-datatable.component.html',
  styleUrl: './menu-datatable.component.css'
})
export class MenuDatatableComponent {
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

  constructor(private MenuService: MenuService) {}

  ngOnInit(): void {
    this.loadList();
  }

  loadList(): void {
    this.loading = true; // Start loading
    this.MenuService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
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
    this.MenuService.getEvent(id).subscribe(data => {
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
    if (!this.selectedEvent.title || !this.selectedEvent.slugs || !this.selectedEvent.status) {
      console.error('Missing required fields');
      return;
    }

    const formData = new FormData();
    formData.append('title', this.selectedEvent.title);
    formData.append('slugs', this.selectedEvent.slugs);
    formData.append('status', this.selectedEvent.status);

    this.MenuService.storeEvent(formData).subscribe(
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
    if (!this.selectedEvent.title || !this.selectedEvent.slugs || !this.selectedEvent.status || !this.selectedEvent.lang_code) {
      console.error('Missing required fields');
      return;
    }

    const formData = new FormData();
    formData.append('title', this.selectedEvent.title);
    formData.append('slugs', this.selectedEvent.slugs);
    formData.append('status', this.selectedEvent.status);
    formData.append('lang_code', this.selectedEvent.lang_code);

    this.MenuService.updateEvent(this.selectedEvent.id, formData).subscribe(
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
      this.MenuService.deleteEvent(id).subscribe(() => {
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
}
