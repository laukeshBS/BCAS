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
  rank: any;
  users: any;
  

  constructor(private adminService: AdminService) {}

  ngOnInit(): void {
    this.loadRoles();
    this.loadList();
    this.loadRank();
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
  loadRank(): void {
    this.adminService.getRankList().subscribe(
      (data) => {
        this.rank = data.data; // Assuming the response is an object with id as keys and name as values
      },
      (error) => {
        console.error('Error fetching categories:', error);
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
      }else {
        alert('No roles found or invalid roles data in the selected event.');
      }
      const rankInput = document.getElementById('rank') as HTMLSelectElement;
      // Check if this.rank is an object and loop through it
      if (this.rank && typeof this.rank === 'object') {
        // Iterate over each rank (id, name)
        for (const [id, name] of Object.entries(this.rank)) {
          const option = document.createElement('option');
          option.value = id;  // Set the option's value to the rank id
          option.textContent = typeof name === 'string' ? name : 'Unknown Rank';  // Use rank name or fallback
          rankInput.appendChild(option);  // Append the option to the dropdown
        }
      } else {
        console.warn('Invalid or empty rank data');
      }

      // console.log(this.selectedEvent);
      this.openEditModal();
    });
  }

  addEvent(): void {
    const modalElement = document.getElementById('addEventModal');
    
    if (modalElement) {
      this.selectedEvent = {};  // Reset selected event
  
      const rankInput = document.getElementById('addrank') as HTMLSelectElement;
  
      // Check if rankInput is found and is a valid HTMLSelectElement
      if (rankInput) {
        // Clear existing options
        rankInput.innerHTML = '';
  
        // Add the default empty option
        const emptyOption = document.createElement('option');
        emptyOption.value = '';
        emptyOption.textContent = 'Select a rank';  // Default placeholder text
        rankInput.appendChild(emptyOption);
  
        // Log rank to confirm data structure
        console.log(this.rank);  // Debugging: Log rank data to inspect it
  
        // Check if this.rank is an object and loop through it
        if (this.rank && typeof this.rank === 'object') {
          // Iterate over each rank (id, name)
          for (const [id, name] of Object.entries(this.rank)) {
            const option = document.createElement('option');
            option.value = id;  // Set the option's value to the rank id
            option.textContent = typeof name === 'string' ? name : 'Unknown Rank';  // Use rank name or fallback
            rankInput.appendChild(option);  // Append the option to the dropdown
          }
        } else {
          console.warn('Invalid or empty rank data');
        }
  
        // Show the modal using Bootstrap's Modal API
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
      } else {
        alert('Rank input element not found!');
      }
    } else {
      alert('Modal element not found!');
    }
  }
  
  
  

  saveEvent(): void {
    // Define the keys of the fields to validate
    type Field = 'name' | 'username' | 'email' | 'phone';
     // Validate the form data
    const requiredFields: Field[] = ['name', 'username', 'email', 'phone'];
    const maxLengths: Record<Field, number> = {
      name: 50,
      username: 100,
      email: 100,
      phone: 10,
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
    formData.append('phone', this.selectedEvent.phone);
    formData.append('rank', this.selectedEvent.rank);
    
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
    type Field = 'name' | 'username' | 'email'| 'phone';
     // Validate the form data
    const requiredFields: Field[] = ['name', 'username', 'email', 'phone'];
    const maxLengths: Record<Field, number> = {
      name: 50,
      username: 100,
      email: 100,
      phone: 10,
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
    formData.append('phone', this.selectedEvent.phone);
    formData.append('rank', this.selectedEvent.rank);
  
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
  // Check if all roles are selected
  isAllSelected(): boolean {
    return this.roles.every((role: { selected: any; }) => role.selected);
  }

  // Toggle Select All functionality
  toggleSelectAll(): void {
    const selectAll = !this.isAllSelected();
    this.roles.forEach((role: { selected: boolean; }) => {
      role.selected = selectAll;
    });
  }
  // Method to toggle user status
  toggleUserStatus(userId: number, currentStatus: number): void {
    const newStatus = currentStatus === 3 ? 2 : 3;
  
    this.adminService.updateUserStatus(userId, newStatus).subscribe(
      (response) => {
        // const user = this.users.find((u: { id: number; }) => u.id === userId);
        // if (user) {
        //   user.status = newStatus;
        //   user.statusText = newStatus === 2 ? 'Active' : 'Inactive';
        // }
  
        // Log message instead of alert
        alert('User Status Updated Successfully');
        this.loadList(); // Reload data
      },
      (error) => {
        console.error('Error updating user status', error);
        console.log('There was an error updating the user status.');
      }
    );
  }
  


  
}

