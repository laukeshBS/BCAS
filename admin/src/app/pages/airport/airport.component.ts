import { Component } from '@angular/core';
import { AirportDatatableComponent } from '../../datatables/airport-datatable/airport-datatable.component';

@Component({
  selector: 'app-airport',
  standalone: true,
  imports: [AirportDatatableComponent],
  templateUrl: './airport.component.html',
  styleUrl: './airport.component.css'
})
export class AirportComponent {

}
