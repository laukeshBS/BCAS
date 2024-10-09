import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { Observable } from 'rxjs';
import { map, take } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class NoAuthGuard implements CanActivate {

  constructor(private authService: AuthService, private router: Router) {}

  canActivate(): Observable<boolean> {
    return this.authService.isLoggedIn().pipe(
      take(1),
      map(isLoggedIn => {
        if (isLoggedIn) {
          // User is logged in, redirect to the home or dashboard page
          this.router.navigate(['/acts-and-policies']); // Adjust the path as needed
          return false; // Deny access to the login route
        } else {
          return true; // Allow access to the login route
        }
      })
    );
  }
}