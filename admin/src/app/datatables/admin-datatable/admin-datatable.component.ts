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
    // console.log('Changing to page:', page); // Debugging line
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
      // Ensure roleIds is an array and exists in the selectedEvent
      if (this.selectedEvent.roleIds && Array.isArray(this.selectedEvent.roleIds)) {
      
        // Loop through all roles and mark them as selected if their id is included in selectedEvent.roleIds
        this.roles.forEach((role: { selected: boolean; id: any }) => {
          const roleIdNumber = Number(role.id);
      
          role.selected = this.selectedEvent.roleIds.includes(Number(roleIdNumber));
          // console.log(role.selected);
        });
      } else {
        this.roles.forEach((role: { selected: boolean }) => {
          role.selected = false;
        });
      }
      



      const rankInput = document.getElementById('rank') as HTMLSelectElement;
      // Check if this.rank is an object and loop through it
      if (this.rank && typeof this.rank === 'object') {
        rankInput.innerHTML = '';  // Remove all existing options
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
        // console.log(this.rank);  // Debugging: Log rank data to inspect it
  
        // Check if this.rank is an object and loop through it
        if (this.rank && typeof this.rank === 'object') {
          // Iterate over each rank (id, name)
          rankInput.innerHTML = '';  // Remove all existing options
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
    type Field = 'name' | 'username' | 'email' | 'phone' | 'rank';
     // Validate the form data
    const requiredFields: Field[] = ['name', 'username', 'email', 'phone','rank'];
    const maxLengths: Record<Field, number> = {
      name: 50,
      username: 100,
      email: 100,
      phone: 10,
      rank:50,
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

    // Phone number validation (only 10 digits allowed)
    const phonePattern = /^\d{10}$/;
    if (this.selectedEvent.phone && !phonePattern.test(this.selectedEvent.phone)) {
      alert('Phone number must be exactly 10 digits.');
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
    if (this.selectedEvent.roleIds && this.selectedEvent.roleIds.length > 0) {
      this.selectedEvent.roleIds.forEach((roleId: { toString: () => string | Blob; }) => {
        formData.append('roles[]', roleId.toString());
      });
    }
  
    // Now, send the formData to the backend
    this.adminService.storeEvent(formData).subscribe(
      response => {
          alert(response.message || 'User Created successfully!');
          this.closeAddModal(); // Close the modal or form
          this.loadList(); // Refresh the list of events
          
      },
      error => {
        // Check if the error contains validation messages (assuming error is an object)
        let errorMessage = 'An error occurred while saving the user.';
  
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
    // Define the keys of the fields to validate
    type Field = 'name' | 'username' | 'email'| 'phone' | 'rank';
     // Validate the form data
    const requiredFields: Field[] = ['name', 'username', 'email', 'phone', 'rank'];
    const maxLengths: Record<Field, number> = {
      name: 50,
      username: 100,
      email: 100,
      phone: 10,
      rank:50,
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

    // Phone number validation (only 10 digits allowed)
    const phonePattern = /^\d{10}$/;
    if (this.selectedEvent.phone && !phonePattern.test(this.selectedEvent.phone)) {
      alert('Phone number must be exactly 10 digits.');
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
    if (this.selectedEvent.roleIds && this.selectedEvent.roleIds.length > 0) {
      console.log(this.selectedEvent.roleIds );
      this.selectedEvent.roleIds.forEach((roleId: { toString: () => string | Blob; }) => {
        console.log(formData.append('roles[]', roleId.toString()));
      });
    }
  
    // Now, send the formData to the backend
    this.adminService.updateEvent(this.selectedEvent.id, formData).subscribe(
      response => {
          alert(response.message || 'User Update successfully!');
          this.closeEditModal(); // Close the modal or form
          this.loadList(); // Refresh the list of events
          
      },
      error => {
        // Check if the error contains validation messages (assuming error is an object)
        let errorMessage = 'An error occurred while saving the user.';
  
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
      this.adminService.deleteEvent(id).subscribe(() => {
        this.events = this.events.filter(event => event.id !== id);
        alert('User Deleted successfully!');
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
    if (Array.isArray(this.roles)) {
      // Filter selected roles
      this.selectedEvent.roleIds = this.roles
        .filter((role: { selected: boolean }) => role.selected === true)
        .map((role: { id: any }) => role.id);
    } else {
      console.error('Error: roles is not an array');
    }
  }
  
  removeHtmlTags(input: string) {
    return input.replace(/<[^>]*>/g, '');
  }
  // Check if all roles are selected
  isAllSelected(): boolean {
    return this.roles.every((role: { selected: boolean; }) => role.selected);
  }

  // Toggle Select All functionality
  toggleSelectAll(): void {
    const selectAll = !this.isAllSelected();  // Toggle select/deselect based on current state
    this.roles.forEach((role: { selected: boolean, name: string }) => {
      role.selected = selectAll;  // Set all roles to selected or deselected
    });
  
    // Update the selected roles array
    this.updateSelectedRoles();
  }
  // Method to toggle individual role selection
  toggleSelectRole(roleId: string): void {
    // Find the role by its ID
    const role = this.roles.find((r: { id: string; }) => r.id === roleId);

    // If the role exists, toggle its selection state
    if (role) {
      role.selected = !role.selected;
    }

    // After toggling, update the selected roles list
    this.updateSelectedRoles();
  }
  // Method to toggle user status
  toggleUserStatus(userId: number, currentStatus: number): void {
    const newStatus = currentStatus === 3 ? 2 : 3;
  
    this.adminService.updateUserStatus(userId, newStatus).subscribe(
      (response) => {
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

