import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { environment } from '../environments/environment';
import { Observable, of, BehaviorSubject, catchError } from 'rxjs';

interface LoginResponse {
  data: {
    access_token: string;
  };
}

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private apiUrl = environment.apiBaseUrl;
  private token: string | null = null;
  private loggedInSubject = new BehaviorSubject<boolean>(this.isAuthenticated());
  loggedIn$ = this.loggedInSubject.asObservable();

  constructor(private http: HttpClient, private router: Router) {}

  isAuthenticated(): boolean {
    return typeof window !== 'undefined' && localStorage.getItem('token') !== null;
  }

  isLoggedIn(): Observable<boolean> {
    return of(this.isAuthenticated());
  }

  login(email: string, password: string): Observable<LoginResponse | null> {
    return this.http.post<LoginResponse>(`${this.apiUrl}login`, { email, password })
      .pipe(
        catchError(error => {
          console.error('Login error', error);
          return of(null);
        })
      );
  }

  handleLogin(email: string, password: string): void {
    this.login(email, password).subscribe(response => {
      if (response?.data.access_token) {
        this.token = response.data.access_token;
        localStorage.setItem('token', this.token);
        this.loggedInSubject.next(true);
        this.router.navigate(['acts-and-policies']);
      } else {
        console.error('Login failed: No token returned');
      }
    });
  }

  logout(): void {
    this.token = null;
    localStorage.removeItem('token');
    this.loggedInSubject.next(false);
    this.router.navigate(['/login']);
  }

  getToken(): string | null {
    return localStorage.getItem('token');
  }
}
