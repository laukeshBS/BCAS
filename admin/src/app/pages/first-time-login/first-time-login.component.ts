import { Component } from '@angular/core';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-first-time-login',
  standalone: true,
  imports: [FormsModule, CommonModule],
  templateUrl: './first-time-login.component.html',
  styleUrl: './first-time-login.component.css'
})
export class FirstTimeLoginComponent {
  email: string = '';
  errorMessage: string = '';
  questions: any;
  answers: any;
  reRegistrationForm: any;
  loading: boolean | undefined;

  constructor(private authService: AuthService, private router: Router) {}

  ngOnInit() {
    // Fetch security questions from the backend when the component initializes
    this.getSecurityQuestions();
  }

  // Method to fetch security questions from the backend
  getSecurityQuestions() {
    this.authService.getQuestions().subscribe(
      (response) => {
        this.questions = response; // Store questions in the questions array
        // console.log(this.questions);
      },
      (error) => {
        this.errorMessage = 'Failed to load questions from the server.';
      }
    );
  }
  register() {
    if (!this.reRegistrationForm.valid || this.questions.some((q: { answer: any; }) => !q.answer)) {
      this.errorMessage = 'Please answer all security questions';
      return; // Don't proceed if the form or any question answer is invalid
    }
  
    this.loading = true;
    
    // Use const to define registrationData inside the method (valid here)
    const registrationData = {
      email: this.email,
      questions: this.questions.map((question: { id: any; answer: any; }) => ({
        questionId: question.id,
        answer: question.answer
      }))
    };
  
    this.authService.reRegister(registrationData).subscribe(
      response => {
        this.loading = false;
        if (response.success) {
          this.router.navigate(['login']);
        } else {
          this.errorMessage = response.message || 'Re-registration failed';
        }
      },
      error => {
        this.loading = false;
        this.errorMessage = error.message || 'An unexpected error occurred';
      }
    );
  }
  
}
