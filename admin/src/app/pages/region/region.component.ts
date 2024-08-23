import { Component } from '@angular/core';
import { RegiondatatableComponent } from '../../datatables/regiondatatable/regiondatatable.component';


@Component({
  selector: 'app-region',
  standalone: true,
  imports: [RegiondatatableComponent],
  templateUrl: './region.component.html',
  styleUrl: './region.component.css'
})
export class RegionComponent {

}
