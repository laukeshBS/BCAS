import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RolesService } from '../../services/roles.service';
declare var bootstrap: any;

@Component({
  selector: 'app-rolesdatatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './rolesdatatable.component.html',
  styleUrls: ['./rolesdatatable.component.css']
})
export class RolesdatatableComponent implements OnInit {
  roleData = { name: '', permissions: [], id: 0 }; // Role data structure with id for edit mode
  permissionGroups: any[] = [];
  selectAllPermissions: boolean = false;
  events: any[] = [];
  selectedEvent: any = {};
  fileToUpload: File | null = null;
  limit = 10;
  lang_code = '';
  selectedFile: any;
  selectedFileError: string | null = null;
  currentPage: number = 1; // Current page for
  totalItems: number = 0; // Total items to calculate total pages
  loading: boolean = false;
  lastPage: number = 0; // Last page]
  userId: number | null = null;

  constructor(private rolesService: RolesService) {}

  ngOnInit(): void {
    this.loadList();
    this.loadPermissions();
  }

  // Load all permissions and group them by group_name
  loadPermissions(): void {
    this.rolesService.getAllPermissions().subscribe(
      (data: any) => {
        if (data && Array.isArray(data.all_permissions)) {
          // Group permissions by group_name
          const groupedPermissions = data.all_permissions.reduce((acc: any, permission: any) => {
            const groupName = permission.group_name;
            if (!acc[groupName]) {
              acc[groupName] = { group_name: groupName, permissions: [] };
            }
            acc[groupName].permissions.push(permission);
            return acc;
          }, {});

          // Convert object to array and initialize selected property
          this.permissionGroups = Object.values(groupedPermissions);
          this.permissionGroups.forEach(group => {
            group.permissions.forEach((perm: { selected: boolean }) => (perm.selected = false));
          });
        } else {
          console.error('Invalid permissions data');
        }
      },
      error => console.error('Error loading permissions:', error)
    );
  }

  // Toggle selection for all permissions
  toggleSelectAllPermissions(): void {
    this.permissionGroups.forEach(group => {
      group.selected = this.selectAllPermissions;
      group.permissions.forEach((permission: any) => {
        permission.selected = this.selectAllPermissions;
      });
    });
  }

  // Toggle selection for individual groups
  toggleGroupPermissions(group: any): void {
    group.permissions.forEach((permission: any) => {
      permission.selected = group.selected;
    });
  }

  // Load the list of roles
  loadList(): void {
    this.loading = true; // Start loading
    this.rolesService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
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
    if (page < 1 || page > this.lastPage) return; // Prevent out of bounds
    this.currentPage = page;
    this.loadList(); // Reload data
  }

  // Total pages calculation
  totalPages(): number {
    return Math.ceil(this.totalItems / this.limit);
  }

  // Format event dates and statuses
  formatEventDates(): void {
    this.events.forEach(event => {
      event.created_at = new Date(event.created_at).toLocaleDateString('en-GB');
      event.status = this.mapStatus(event.status);
    });
  }

  // Map status numbers to text
  mapStatus(status: number): string {
    switch (status) {
      case 3: return 'Published';
      case 2: return 'Pending';
      default: return 'Draft';
    }
  }

  // Close the edit modal
  closeEditModal(): void {
    const modalElement = document.getElementById('editEventModal');
    if (modalElement) {
      const modal = bootstrap.Modal.getInstance(modalElement);
      modal?.hide();
    }
  }

  // Open the edit modal and load event data
  editEvent(id: number): void {
    this.rolesService.getEvent(id).subscribe(data => {
      this.selectedEvent = data.data;
      this.roleData = { ...data.data.role, permissions: data.data.role.permissions, id: id }; // Fill in role data
      this.openModal('editEventModal');
    });
  }

  // Open modal by ID
  private openModal(modalId: string): void {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
    }
  }

  // Add new role
  addEvent(): void {
    const modalElement = document.getElementById('addEventModal');
    if (modalElement) {
      this.selectedEvent = {};
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
    }
  }

  removeHtmlTags(input: string) {
    return input.replace(/<[^>]*>/g, '');
  }

  // Save new role
  saveEvent(): void {
    if (!this.roleData.name.trim()) {
      alert('Role Name is required.');
      return;
    }

    const selectedPermissions = this.permissionGroups
      .flatMap(group => group.permissions.filter((permission: any) => permission.selected))
      .map((permission: any) => permission.id);

    if (selectedPermissions.length === 0) {
      alert('No permissions selected');
      return;
    }

    const roleData = { name: this.removeHtmlTags(this.roleData.name), permissions: selectedPermissions };

    this.rolesService.createRole(roleData).subscribe(
      response => {
        this.showNotification('Role created successfully');
        this.loadList();
        this.closeAddModal();
        this.clearForm();
      },
      error => this.showNotification('Error creating role: ' + error.message)
    );
  }

  // Modify existing role
  modifyEvent(): void {
    const selectedPermissions = this.permissionGroups
      .flatMap(group => group.permissions.filter((permission: { selected: any }) => permission.selected))
      .map((permission: { name: any }) => permission.name);

    const updatedRoleData = { name: this.removeHtmlTags(this.roleData.name), permissions: selectedPermissions };

    this.rolesService.updateEvent(this.roleData.id, updatedRoleData).subscribe(
      response => {
        this.showNotification('Role updated successfully');
        this.closeEditModal();
        this.loadList(); // Refresh data after update
      },
      error => console.error('Error updating role:', error)
    );
  }

  // Delete event by id
  deleteEvent(id: number): void {
    if (confirm('Are you sure you want to delete this event?')) {
      this.rolesService.deleteEvent(id).subscribe(() => {
        this.events = this.events.filter(event => event.id !== id);
      });
    }
  }

  // Handle file change for new event
  onFileChange(event: any): void {
    if (event.target.files.length > 0) {
      this.fileToUpload = event.target.files[0];
    }
  }

  // Handle file change for edit mode
  oneditFileChange(event: any): void {
    const file = event.target.files[0];
    if (file) {
      if (file.size > 5000000) {
        this.selectedFileError = 'File size exceeds 5MB limit.';
      } else {
        this.selectedFile = file;
        this.selectedFileError = '';
      }
    }
  }

  // Close the "Add" modal
  closeAddModal(): void {
    const modalElement = document.getElementById('addEventModal');
    if (modalElement) {
      const modal = bootstrap.Modal.getInstance(modalElement);
      modal?.hide();
    }
  }

  // Clear form after submission
  private clearForm(): void {
    this.roleData.name = '';
    this.permissionGroups.forEach(group => {
      group.permissions.forEach((permission: { selected: boolean }) => {
        permission.selected = false;
      });
      group.selected = false;
    });
  }

  // Simple notification logic
  private showNotification(message: string): void {
    alert(message); // Placeholder alert, replace with more advanced UI if needed
  }
}
