import { Component } from '@angular/core';
import { AirlinesDatatableComponent } from '../../datatables/airlines-datatable/airlines-datatable.component';

@Component({
  selector: 'app-airlines',
  standalone: true,
  imports: [AirlinesDatatableComponent],
  templateUrl: './airlines.component.html',
  styleUrl: './airlines.component.css'
})
export class AirlinesComponent {

}
