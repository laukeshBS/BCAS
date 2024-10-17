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
  constructor(
    private permissionsService: PermissionsService,
    private router: Router
  ) {}

  ngOnInit(): void {
    const limit = 10; // Adjust as needed
    const lang_code = 'en'; // Adjust as needed

    console.log('Fetching permissions...'); // Debug log

    this.permissionsService.fetchPermissions(10, 'en').subscribe(
      response => {
        console.log('Permissions fetched successfully:', response);
      },
      error => {
        console.error('Error fetching permissions:', error);
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
  }
}
