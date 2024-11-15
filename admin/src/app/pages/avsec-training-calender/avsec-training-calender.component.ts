import { Component } from '@angular/core';
import { AvsecTrainingCalenderDatatableComponent } from '../../datatables/avsec-training-calender-datatable/avsec-training-calender-datatable.component';

@Component({
  selector: 'app-avsec-training-calender',
  standalone: true,
  imports: [AvsecTrainingCalenderDatatableComponent],
  templateUrl: './avsec-training-calender.component.html',
  styleUrl: './avsec-training-calender.component.css'
})
export class AvsecTrainingCalenderComponent {

}
