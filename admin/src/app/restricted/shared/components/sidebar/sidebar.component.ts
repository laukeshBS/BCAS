import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { PermissionsService } from '../../../../services/permissions.service';

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css'] // Corrected typo: 'styleUrls' should be used instead of 'styleUrl'
})
export class SidebarComponent {
  constructor(
    private permissionsService: PermissionsService,
    private router: Router
  ) {}

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
