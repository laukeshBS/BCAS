import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { HeaderComponent } from './layout/header/header.component';
import { FooterComponent } from './layout/footer/footer.component';
import { SidebarComponent } from './layout/sidebar/sidebar.component';
import { CommonModule } from '@angular/common';
import { AuthService } from './services/auth.service';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, HeaderComponent, FooterComponent, SidebarComponent, CommonModule],
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'] // Make sure this is correct
})
export class AppComponent {

  constructor(private authService: AuthService) {}

  title = 'admin';
  private loggedIn: boolean = false;

  isLoggedIn(): boolean {
    const loggedIn = this.authService.isLoggedIn();
    // console.log('Is logged in:', loggedIn); // Debug log
    return loggedIn;
    // return this.loggedIn;
  }

  login() {
    this.loggedIn = true;
  }

  logout() {
    this.loggedIn = false;
  }
}
