import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../services/auth.service';
import { BehaviorSubject } from 'rxjs';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [],
  templateUrl: './header.component.html',
  styleUrl: './header.component.css'
})
export class HeaderComponent {
  loggedInSubject: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(false);
  userData: any = null;
  
  constructor(private authService: AuthService) {}

  ngOnInit(): void {
    const token = localStorage.getItem('token');
    if (token) {
      this.loggedInSubject.next(true);  // Set logged-in status to true if token exists
      const user = localStorage.getItem('user');
      if (user) {
        this.userData = JSON.parse(user);  // Parse the user data from string to object
      }
    } else {
      this.loggedInSubject.next(false);  // Set logged-in status to false if no token
    }
  }
  
  logout() {
    this.authService.logout(); // Call the logout method from your AuthService
  }
}
