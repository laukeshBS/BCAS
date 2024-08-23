import { Component } from '@angular/core';
import { CircularsdatatableComponent } from '../../datatables/circularsdatatable/circularsdatatable.component';


@Component({
  selector: 'app-Circulars',
  standalone: true,
  imports: [CircularsdatatableComponent],
  templateUrl: './circulars.component.html',
  styleUrl: './circulars.component.css'
})
export class CircularsComponent {

}
