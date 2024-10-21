import { Component, OnInit, OnDestroy } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { HeaderComponent } from './layout/header/header.component';
import { FooterComponent } from './layout/footer/footer.component';
import { SidebarComponent } from './layout/sidebar/sidebar.component';
import { CommonModule } from '@angular/common';
import { AuthService } from './services/auth.service';
import { Subscription } from 'rxjs';
import { SidebarComponent as RestrictedSidebarComponent } from "./restricted/shared/components/sidebar/sidebar.component";

 
@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, HeaderComponent, FooterComponent, SidebarComponent, CommonModule],
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit, OnDestroy {
  title = 'admin';
  loggedIn$: boolean = false;
  loading: boolean = true;
  private subscription: Subscription = new Subscription();
 
  constructor(private authService: AuthService) {}
 
  ngOnInit(): void {
    this.subscription.add(
      this.authService.loggedIn$.subscribe(isLoggedIn => {
        this.loggedIn$ = isLoggedIn; // Update logged-in state based on the BehaviorSubject
        this.loading = false; // Set loading to false once we have the login state
      })
    );
  }
  logout() {
    this.authService.logout();
    // Reflect the logged-out state is handled via the BehaviorSubject in AuthService
  }
  ngOnDestroy(): void {
    this.subscription.unsubscribe(); // Clean up subscriptions on destroy
  }
}
 
 