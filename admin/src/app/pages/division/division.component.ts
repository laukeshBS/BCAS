import { Component } from '@angular/core';
import { DivisiondatatableComponent } from '../../datatables/divisiondatatable/divisiondatatable.component';


@Component({
  selector: 'app-division',
  standalone: true,
  imports: [DivisiondatatableComponent],
  templateUrl: './division.component.html',
  styleUrl: './division.component.css'
})
export class DivisionComponent {

}
