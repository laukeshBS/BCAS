import { Component, ViewChild } from '@angular/core';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule, NgForm } from '@angular/forms';

@Component({
  selector: 'app-first-time-login',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './first-time-login.component.html',
  styleUrls: ['./first-time-login.component.css']
})
export class FirstTimeLoginComponent {
  email: string = '';
  errorMessage: string = '';
  questions: any;
  answers: any;
  loading: boolean | undefined;

  @ViewChild('reRegistrationForm', { static: false }) reRegistrationForm!: NgForm;

  constructor(private authService: AuthService, private router: Router) {}

  ngOnInit() {
    this.getSecurityQuestions();
  }

  // Fetch security questions from the backend
  getSecurityQuestions() {
    this.authService.getQuestions().subscribe(
      (response) => {
        this.questions = response.map((question: any) => ({
          ...question,
          answer: '' // Initialize the answer property for each question
        }));
      },
      (error) => {
        this.errorMessage = 'Failed to load questions from the server.';
      }
    );
  }

  // Handle the form submission
  register() {
    if (!this.reRegistrationForm?.valid) {
      this.errorMessage = 'Form is invalid. Please complete all required fields.';
      return;
    }

    // Ensure all questions are answered
    for (let question of this.questions) {
      if (!question.answer.trim()) {
        this.errorMessage = `Please answer the question: "${question.question}"`;
        return;
      }
    }

    this.loading = true;
    
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
