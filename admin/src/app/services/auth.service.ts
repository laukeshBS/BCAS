import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { environment } from '../environments/environment';
import { Observable, of, BehaviorSubject, catchError } from 'rxjs';

interface LoginResponse {
  data: {
    access_token: string;
    user: {
      id: number;
      name: string;
    };
  };
}

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private apiUrl = environment.apiBaseUrl;
  private token: string | null = null;
  // private user: string | null = null;
  private loggedInSubject = new BehaviorSubject<boolean>(this.isAuthenticated());
  loggedIn$ = this.loggedInSubject.asObservable();
  private user: { id: number; name: string } | null = null; // Adjust this type as needed

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
      if (response && response.data && response.data.access_token) {
        this.token = response.data.access_token;
        localStorage.setItem('token', this.token);
  
        // Store user data as a JSON string
        this.user = response.data.user;
        localStorage.setItem('user', JSON.stringify(this.user)); // Stringify the user object
  
        this.loggedInSubject.next(true);
        this.router.navigate(['acts-and-policies']);
      } else {
        console.error('Login failed: No token returned or invalid response');
      }
    }, error => {
      console.error('Login error:', error);
    });
  }
  
  getUserId(): number | null {
    const userData = localStorage.getItem('user');
    if (userData) {
      const user = JSON.parse(userData); // Parse the JSON string back to an object
      return user.id || null; // Adjust based on the structure of your user object
    }
    return null;
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
