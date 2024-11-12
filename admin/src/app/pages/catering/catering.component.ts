import { Component } from '@angular/core';
import { CateringDatatableComponent } from '../../datatables/catering-datatable/catering-datatable.component';

@Component({
  selector: 'app-catering',
  standalone: true,
  imports: [CateringDatatableComponent],
  templateUrl: './catering.component.html',
  styleUrl: './catering.component.css'
})
export class CateringComponent {

}
