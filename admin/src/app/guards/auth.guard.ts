import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { Observable } from 'rxjs';
import { map, take } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class AuthGuard implements CanActivate {

  constructor(private authService: AuthService) {}

  canActivate(
  ): Observable<boolean> {
    return this.authService.isLoggedIn().pipe(
      take(1), // Take the first emitted value and complete
      map(isLoggedIn => {
        if (isLoggedIn) {
          return true; // User is logged in, allow access
        } else {
          // Redirect to login page with the attempted URL as a query parameter
          return false; // User is not logged in, deny access
        }
      })
    );
  }
}
