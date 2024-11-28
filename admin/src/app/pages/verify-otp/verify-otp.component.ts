import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';  // For capturing the email from query params
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';
import { FormsModule, NgForm } from '@angular/forms';


@Component({
  standalone: true,
  imports: [CommonModule, FormsModule],
  selector: 'app-verify-otp',
  templateUrl: './verify-otp.component.html',
  styleUrls: ['./verify-otp.component.css']
})
export class VerifyOtpComponent implements OnInit {
  email: string = '';
  otp: string = '';
  message: string = '';
  isSuccess: boolean = false;

  constructor(
    private route: ActivatedRoute, // Capture the query parameters
    private authService: AuthService, private router: Router
  ) {}

  ngOnInit(): void {
    // Get the email from query parameters
    this.email = localStorage.getItem('userEmail') || '';
  }

  // Verify OTP
  verifyOtp() {
    this.authService.verifyOtp(this.email, this.otp).subscribe(
      (response) => {
        this.isSuccess = true;
        this.message = response.message;
        alert(response.message);
        this.router.navigate(['login']);
        // After OTP verification, redirect to the reset password page
        // You can handle the redirection to password reset here
      },
      (error) => {
        this.isSuccess = false;
        // this.message = error.error.message || 'Invalid OTP';
        alert(error.error.message);
      }
    );
  }
}
