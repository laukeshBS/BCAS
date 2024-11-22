import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { Observable } from 'rxjs';
import { map, take } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class AuthGuard implements CanActivate {

  constructor(private authService: AuthService, private router: Router) {}

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): Observable<boolean> {
    const requiredRoles = route.data['roles'] as Array<string>;

    return this.authService.isLoggedIn().pipe(
      take(1), // Take the first emitted value and complete
      map((isLoggedIn) => {
        if (!isLoggedIn) {
          this.router.navigate(['/login']); // Redirect to login if not logged in
          return false;
        }

        const userRoles = this.authService.getUserRoles(); // Fetch the current user's roles

        if (requiredRoles && requiredRoles.length > 0) {
          const hasRole = requiredRoles.some(role => userRoles.includes(role)); // Check if the user has one of the required roles

          if (!hasRole) {
            this.router.navigate(['/unauthorized']); // Redirect to unauthorized if no role matches
            return false;
          }
        }

        return true; // User is logged in and has the required role(s)
      })
    );
  }
}
