import { Component, ViewChild } from '@angular/core';
import { AuthService } from '../../../services/auth.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { CaptchaComponent } from '../../../captcha/captcha.component';
import * as CryptoJS from 'crypto-js';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [FormsModule, CommonModule, CaptchaComponent],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
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
  
    // Validate CAPTCHA
    if (!this.captchaComponent.validateCaptcha()) {
      alert('CAPTCHA is incorrect, please try again.');
      return;
    }
    if (this.password) {
      
      // const key = CryptoJS.enc.Utf8.parse('xWfR9K7h3gD5yTqV6zUqE4mJ2d8C3sA4'); // 16-byte key
      // const iv = CryptoJS.lib.WordArray.random(16); // 16-byte IV
      // const encryptedPassword = CryptoJS.AES.encrypt(this.password, key, { iv: iv }).toString();
      const key = CryptoJS.enc.Utf8.parse('xWfR9K7h3gD5yTqV'); // Adjust to 16 bytes
      const iv = CryptoJS.lib.WordArray.random(16); // Generate 16-byte IV
      const encryptedPassword = CryptoJS.AES.encrypt(this.password, key, { iv: iv }).toString();
      // const bytes = CryptoJS.AES.decrypt(encryptedPassword, key);
      // alert(bytes.toString(CryptoJS.enc.Utf8));
      this.authService.handleLogin(this.email, encryptedPassword,iv.toString(CryptoJS.enc.Base64));
    }else{
      alert('Please enter valid password.');
      return;
    }

    // this.authService.handleLogin(this.email, this.password);
  
    // Define your secret key
    // const secretKey = 'xWfR9K7h3gD5yTqV6zUqE4mJ2d8C3sA4';
    
    // try {
    //   // Encrypt the password
    //   const parsedKey = CryptoJS.enc.Base64.parse(secretKey);
    //   console.log('Parsed Key Length:', parsedKey.words.length);
    //   const encryptedPassword = CryptoJS.AES.encrypt(this.password, parsedKey).toString();
  
    //   console.log('Plain Password:', this.password);
    //   console.log('Secret Key:', secretKey);
    //   console.log('Encrypted Password:', encryptedPassword);
  
    //   // Attempt to log in
    //   this.authService.handleLogin(this.email, encryptedPassword);
    // } catch (error) {
    //   this.errorMessage = 'An error occurred during encryption. Please try again.';
    //   console.error('Encryption error:', error);
    // }
  }
  
}
