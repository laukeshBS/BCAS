import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { environment } from '../environments/environment';
import { Observable, of, BehaviorSubject, catchError } from 'rxjs';

interface LoginResponse {
  success: boolean;
  message: string;
  data?: {
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
  private loggedInSubject = new BehaviorSubject<boolean>(this.isAuthenticated());
  loggedIn$ = this.loggedInSubject.asObservable();
  
  constructor(private http: HttpClient, private router: Router) {}

  public isAuthenticated(): boolean {
    return typeof window !== 'undefined' && localStorage.getItem('token') !== null;
  }

  isLoggedIn(): Observable<boolean> {
    return of(this.isAuthenticated());
  }

  login(email: string, password: string): Observable<LoginResponse | null> {
    return this.http.post<LoginResponse>(`${this.apiUrl}login`, { email, password });
  }

  handleLogin(email: string, password: string): void {
    this.login(email, password).subscribe(response => {
      console.log('Response:', response);
        if (response) {
            if (response.success) {
                if (response.data) { // Check if data exists
                    const accessToken = response.data.access_token; 
                    if (accessToken) {
                        this.storeUserData(accessToken, response.data.user);
                        this.loggedInSubject.next(true);
                        this.router.navigate(['acts-and-policies']);
                    } else {
                        alert('Login failed: No Access Token Found');
                    }
                } else {
                    alert('Login failed: No User Data Found');
                }
            } else {
                alert('Login failed: ' + response.message);
            }
        } else {
            alert('No Response Found');
        }
    }, error => {
        console.error('Login error:', error);
        alert('Login error: ' + (error.error?.message || 'An unexpected error occurred'));
    });
}


  private storeUserData(token: string, user: { id: number; name: string }): void {
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
  }

  getUserId(): number | null {
    const userData = localStorage.getItem('user');
    if (userData) {
      const user = JSON.parse(userData);
      return user.id || null;
    }
    return null;
  }

  logout(): void {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    this.loggedInSubject.next(false);
    this.router.navigate(['/login']);
  }

  getToken(): string | null {
    return localStorage.getItem('token');
  }

  checkLoginStatus(): void {
    this.loggedInSubject.next(this.isAuthenticated());
  }
 
}
