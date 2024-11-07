import { Component } from '@angular/core';
import { AdminService } from '../../services/admin.service';  // Import the service
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';

declare var bootstrap: any;

@Component({
  selector: 'app-admin-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './admin-datatable.component.html',
  styleUrl: './admin-datatable.component.css'
})
export class AdminDatatableComponent {
  events: any[] = [];
  selectedEvent: any = {};
  fileToUpload: File | null = null;
  limit = 10; 
  lang_code = 'en'; 
  selectedFile: any;
  selectedFileError: string | null = null; // Initialized with null
roles: any;

  constructor(private adminService: AdminService) {}

  ngOnInit(): void {
    this.loadRoles();
    this.loadList();
  }

  loading: boolean = false;
  currentPage: number = 1;
  totalItems: number = 0;
  lastPage: number = 0;

  loadRoles(): void {
    this.adminService.getRoles().subscribe(
      (response) => {
        this.roles = Object.keys(response.data).map(key => ({
          id: key,
          name: response.data[key],
          selected: false // You can track selection state here
        }));
      },
      (error) => {
        console.error('Error fetching roles', error);
      }
    );
  }
  loadList(): void {
    this.loading = true; // Start loading
    this.adminService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
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
      switch (event.status) {
        case 1:
          event.status = 'Active';
          break;
        case 2:
          event.status = 'Deactive';
          break;
        default:
          event.status = '';
          break;
      }
      // Get all role names from event.roles
      if (event.roles && Array.isArray(event.roles)) {
        event.roleNames = event.roles.map((role: { name: any; }) => role.name);
      } else {
        event.roleNames = [];
      }
    });
  }

  editEvent(id: number): void {
    this.adminService.getEvent(id).subscribe(data => {
      this.selectedEvent = data;
      // Update roles based on the selectedEvent's roles
      if (this.selectedEvent.roles && Array.isArray(this.selectedEvent.roles)) {
        this.roles.forEach((role: { selected: any; id: any; }) => {
          role.selected = this.selectedEvent.roles.includes(role.id);
        });
      }
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
    // Define the keys of the fields to validate
    type Field = 'name' | 'username' | 'email';
     // Validate the form data
    const requiredFields: Field[] = ['name', 'username', 'email'];
    const maxLengths: Record<Field, number> = {
      name: 50,
      username: 100,
      email: 100,
    };

    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);
    
    // Check for maximum length violations
    const lengthViolations = requiredFields.filter(field => 
      this.selectedEvent[field]?.length > maxLengths[field]
    );

    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }

    if (lengthViolations.length > 0) {
      alert(`Maximum length exceeded for fields: ${lengthViolations.join(', ')}`);
      return;
    }

    // Check email format using a regular expression
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Simple email regex
    if (this.selectedEvent.email && !emailPattern.test(this.selectedEvent.email)) {
      alert('Invalid email format.');
      return;
    }
  
    // Create FormData object
    const formData = new FormData();
    formData.append('name', this.removeHtmlTags(this.selectedEvent.name.trim()));
    formData.append('username', this.removeHtmlTags(this.selectedEvent.username.trim()));
    formData.append('email', this.selectedEvent.email);
    
    // Include selected roles
    if (this.selectedEvent.roles && this.selectedEvent.roles.length > 0) {
      this.selectedEvent.roles.forEach((roleId: { toString: () => string | Blob; }) => {
        formData.append('roles[]', roleId.toString());
      });
    }
  
    // Call the service to store the event
    this.adminService.storeEvent(formData).subscribe(
      (event: HttpEvent<any>) => {
        this.loadList(); // Refresh the list of events
        this.closeAddModal(); // Close the modal or form
      },
      error => {
        alert('Error updating event: ' + JSON.stringify(error.error));
      }
    );
  }
  

  modifyEvent(): void {
    // Define the keys of the fields to validate
    type Field = 'name' | 'username' | 'email';
     // Validate the form data
    const requiredFields: Field[] = ['name', 'username', 'email'];
    const maxLengths: Record<Field, number> = {
      name: 50,
      username: 100,
      email: 100,
    };

    const missingFields = requiredFields.filter(field => !this.selectedEvent[field]);
    
    // Check for maximum length violations
    const lengthViolations = requiredFields.filter(field => 
      this.selectedEvent[field]?.length > maxLengths[field]
    );

    if (missingFields.length > 0) {
      alert(`Missing required fields: ${missingFields.join(', ')}`);
      return;
    }

    if (lengthViolations.length > 0) {
      alert(`Maximum length exceeded for fields: ${lengthViolations.join(', ')}`);
      return;
    }
    // Check email format using a regular expression
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Simple email regex
    if (this.selectedEvent.email && !emailPattern.test(this.selectedEvent.email)) {
      alert('Invalid email format.');
      return;
    }

    const formData = new FormData();
    formData.append('name', this.removeHtmlTags(this.selectedEvent.name.trim()));
    formData.append('username', this.removeHtmlTags(this.selectedEvent.username.trim()));
    formData.append('email', this.selectedEvent.email);
  
    // Include selected roles
    if (this.selectedEvent.roles && this.selectedEvent.roles.length > 0) {
      this.selectedEvent.roles.forEach((roleId: { toString: () => string | Blob; }) => {
        formData.append('roles[]', roleId.toString());
      });
    }
  
    // Call the service to update the event
    this.adminService.updateEvent(this.selectedEvent.id, formData).subscribe(
      (event: HttpEvent<any>) => {
        this.loadList(); // Refresh the list of events
        this.closeEditModal(); // Close the modal or form
      },
      error => {
        alert('Error updating event: ' + JSON.stringify(error.error));
      }
    );
  }
  

  deleteEvent(id: number): void {
    if (confirm('Are you sure you want to delete this event?')) {
      this.adminService.deleteEvent(id).subscribe(() => {
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
  updateSelectedRoles(): void {
    // Update the selected roles array based on the checkboxes
    this.selectedEvent.roles = this.roles
      .filter((role: { selected: any; }) => role.selected)
      .map((role: { id: any; }) => role.id);
  }
  removeHtmlTags(input: string) {
    return input.replace(/<[^>]*>/g, '');
  }

  
}

