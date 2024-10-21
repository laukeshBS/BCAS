import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { PermissionsService } from '../../services/permissions.service';

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [RouterModule, CommonModule],
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css'] // Corrected 'styleUrl' to 'styleUrls'
})
export class SidebarComponent implements OnInit {
  loading = false; // Loading state
  permissions: any[] = []; // Define type based on your response

  constructor(
    private permissionsService: PermissionsService,
    private router: Router
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

  // Determines if the current route matches the provided one
  isActive(route: string): boolean {
    return this.router.url === route;
  }

  // Toggles the visibility of the sidebar
  toggleNav(): void {
    // Implement the logic to toggle the sidebar
    const sidebar = document.querySelector('.sidebar'); // Adjust selector as needed
    if (sidebar) {
      sidebar.classList.toggle('is-collapsed'); // Add/remove a class for toggling
    }
  }
}
