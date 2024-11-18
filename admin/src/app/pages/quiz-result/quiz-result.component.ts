import { Component } from '@angular/core';
import { QuizResultDatatableComponent } from '../../datatables/quiz-result-datatable/quiz-result-datatable.component';

@Component({
  selector: 'app-quiz-result',
  standalone: true,
  imports: [QuizResultDatatableComponent],
  templateUrl: './quiz-result.component.html',
  styleUrl: './quiz-result.component.css'
})
export class QuizResultComponent {

}
