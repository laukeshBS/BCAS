import { Component } from '@angular/core';
import { QuizDatatableComponent } from '../../datatables/quiz-datatable/quiz-datatable.component';

@Component({
  selector: 'app-quiz',
  standalone: true,
  imports: [QuizDatatableComponent],
  templateUrl: './quiz.component.html',
  styleUrl: './quiz.component.css'
})
export class QuizComponent {

}
