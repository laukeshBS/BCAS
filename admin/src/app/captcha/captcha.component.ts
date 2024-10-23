import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-captcha',
  standalone: true,
  imports: [FormsModule, CommonModule, ],
  templateUrl: './captcha.component.html',
  styleUrls: ['./captcha.component.css']
})
export class CaptchaComponent implements OnInit {
  captchaText: string = '';
  userInput: string = '';

  ngOnInit(): void {
    this.generateCaptcha();
  }

  generateCaptcha() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    this.captchaText = '';
    for (let i = 0; i < 6; i++) {
      this.captchaText += chars.charAt(Math.floor(Math.random() * chars.length));
    }
  }

  validateCaptcha(): boolean {
    return this.userInput === this.captchaText;
  }
}

