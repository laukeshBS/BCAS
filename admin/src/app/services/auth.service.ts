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
      status: number;
      id: number;
      name: string;
      roles: []; // Add roles array here
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
  private userRoles: string[] = []; // Store roles here
  user: any;
  
  constructor(private http: HttpClient, private router: Router) {
    this.user = JSON.parse(localStorage.getItem('user') || '{}');
  }

  public isAuthenticated(): boolean {
    return typeof window !== 'undefined' && localStorage.getItem('token') !== null;
  }

  isLoggedIn(): Observable<boolean> {
    return of(this.isAuthenticated());
  }

  login(email: string, password: string,iv:string): Observable<LoginResponse | null> {
    return this.http.post<LoginResponse>(`${this.apiUrl}login`, { email, password,iv })
  }

  // Re-registration method
  reRegister(registrationData: { email: string; questions: { questionId: number; answer: string }[] }): Observable<any> {
    const body = registrationData;
    return this.http.post<any>(`${this.apiUrl}re-register`, body); // Adjust API endpoint as needed
  }

  // Verify the OTP entered by the user
  verifyOtp(email: string, otp: string) {
    return this.http.post<any>(`${this.apiUrl}otp-verification`, { email, otp });
  }

  // Re-registration method
  forgotPassword(registrationData: { email: string; questions: { questionId: number; answer: string }[] }): Observable<any> {
    const body = registrationData;
    return this.http.post<any>(`${this.apiUrl}forgot-password`, body); // Adjust API endpoint as needed
  }

  // get all questions 
  getQuestions(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}question-list`);
  }

  handleLogin(email: string, password: string,iv:string): void {
    this.login(email, password,iv).subscribe(response => {
      // console.log('Response:', response);
        if (response) {
            if (response.success) {
                if (response.data) { // Check if data exists
                    const accessToken = response.data.access_token; 
                    if (accessToken) {
                        // Check if the user is a new user (status == 1)
                        if (response.data.user.status === 1) {
                          alert('Your Id is Not Activated Yet Please Activate Your Id');
                          this.router.navigate(['re-registration']);
                        } else if(response.data.user.status === 2) {
                            // User is logged in, redirect to dashboard
                            this.storeUserData(accessToken, response.data.user);
                            this.userRoles = response.data.user.roles.map((role: any) => role.name);
                            this.loggedInSubject.next(true);
                            this.router.navigate(['dashboard']);
                        } else if(response.data.user.status === 3) {
                          alert('Your Id is Deactivated Please Contact to Admin');
                        } else {
                          alert('Login failed: No Status Found');
                        }
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
        // console.error('Login error:', error);
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
    this.router.navigate(['/']);
  }

  getToken(): string | null {
    return localStorage.getItem('token');
  }

  checkLoginStatus(): void {
    this.loggedInSubject.next(this.isAuthenticated());
  }
  getUserRoles(): string[] {
    return this.userRoles; // Now it should return the role names correctly
  }
 
}
