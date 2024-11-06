import { Component } from '@angular/core';
import { PermissionsService } from '../../services/permissions.service';
import { CommonModule } from '@angular/common'; // Import CommonModule

@Component({
  selector: 'app-dasboard',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './dasboard.component.html',
  styleUrl: './dasboard.component.css'
})
export class DasboardComponent {

  loading = false; // Loading state
  permissions: any[] = []; // Define type based on your response

  constructor(
    private permissionsService: PermissionsService,
  ) {}
  
  ngOnInit(): void {
    this.fetchPermissions();
  }
  fetchPermissions(): void {
    this.loading = true; // Set loading to true
    const limit = 10; // Adjust as needed
    const lang_code = 'en'; // Adjust as needed

    console.log('Fetching permissions...'); // Debug log

    this.permissionsService.fetchPermissions(limit, lang_code).subscribe(
      response => {
        this.permissions = response; // Store permissions
        console.log('Permissions fetched successfully:', response);
      },
      error => {
        console.error('Error fetching permissions:', error);
        // Optionally, display an error message to the user
      },
      () => {
        this.loading = false; // Set loading to false on completion
      }
    );
  }
  // Checks if the user has the given permission
  hasPermission(permission: string): boolean {
    return this.permissionsService.hasPermission(permission);
  }

  // Checks if the user has any of the given permissions
  hasAnyPermission(permissions: string[]): boolean {
    return this.permissionsService.hasAnyPermission(permissions);
  }
}
