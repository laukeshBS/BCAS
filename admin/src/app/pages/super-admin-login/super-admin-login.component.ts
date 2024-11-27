import { Component, ViewChild } from '@angular/core';
import { AuthService } from '../../services/auth.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { CaptchaComponent } from '../../captcha/captcha.component';
import * as CryptoJS from 'crypto-js';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-super-admin-login',
  standalone: true,
  imports: [FormsModule, CommonModule, CaptchaComponent,RouterModule],
  templateUrl: './super-admin-login.component.html',
  styleUrl: './super-admin-login.component.css'
})
export class SuperAdminLoginComponent {

  email: string = '';
  password: string = '';
  errorMessage: string | null = null;

  @ViewChild(CaptchaComponent) captchaComponent!: CaptchaComponent;

  constructor(private authService: AuthService) {}

  login() {
    // Validate email and password
    if (!this.email || !this.password) {
      this.errorMessage = 'Email and password are required.';
      return;
    }

    // Validate email and password
    if (this.email !=="superadmin@example.com") {
      alert('No such username or password.');
      return;
    }
  
    // Validate CAPTCHA
    if (!this.captchaComponent.validateCaptcha()) {
      alert('CAPTCHA is incorrect, please try again.');
      return;
    }
    if (this.password) {
      
      const key = CryptoJS.enc.Utf8.parse('xWfR9K7h3gD5yTqV'); // Adjust to 16 bytes
      const iv = CryptoJS.lib.WordArray.random(16); // Generate 16-byte IV
      const encryptedPassword = CryptoJS.AES.encrypt(this.password, key, { iv: iv }).toString();
      this.authService.handleLogin(this.email, encryptedPassword,iv.toString(CryptoJS.enc.Base64));
    }else{
      alert('Please enter valid password.');
      return;
    }
  }

}
