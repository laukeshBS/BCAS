import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { environment } from '../environments/environment';
import { Observable, catchError, of } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private loggedIn: boolean = false;
  private apiUrl = environment.apiBaseUrl;
  private token: string | null = null;

  constructor(private http: HttpClient, private router: Router) {}

  isLoggedIn(): boolean {
    return this.isAuthenticated(); // Check if authenticated based on token
  }

  login(email: string, password: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}login`, { email, password })
      .pipe(
        catchError(error => {
          console.error('Login error', error);
          return of(null); // Handle error gracefully
        })
      );
  }

  handleLogin(email: string, password: string): void {
    this.login(email, password).subscribe(response => {
      if (response && response.token) {
        this.token = response.token;
        if (this.token) {
          localStorage.setItem('token', this.token);
        } else {
          console.error('Token is null or undefined');
        }
        this.loggedIn = true;
        this.router.navigate(['/acts-and-policies']);
      } else {
        console.error('Login failed: No token returned');
      }
      this.token = response.token;
    });
  }

  logout(): void {
    this.token = null;
    localStorage.removeItem('token');
    this.loggedIn = false;
    this.router.navigate(['/login']);
  }

  isAuthenticated(): boolean {
    const token = localStorage.getItem('token');
    return token !== null;
  }

  getToken(): string {
    const token = localStorage.getItem('token');
    if (token) {
      return token;
    }
    throw new Error('Token is not available');
  }
}
