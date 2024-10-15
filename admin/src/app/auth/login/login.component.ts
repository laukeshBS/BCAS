import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { Component } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { NgModule } from '@angular/core';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
  loginForm: FormGroup;

  constructor(private http: HttpClient, private fb: FormBuilder) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required]]
    });
  }

  onLogin() {
    alert('Ok');
    console.log(this.loginForm.valid);
    if (this.loginForm.valid) {
      
      const { email, password } = this.loginForm.value;
      this.http.post('http://localhost:8000/api/login', {
        email,
        password
      }).subscribe(response => {
        console.log('Login successful', response);
        // Handle login success (e.g., store token, navigate to dashboard)
      }, error => {
        console.error('Login failed', error);
        // Handle login error (e.g., show error message)
      });
    }
  }
}
