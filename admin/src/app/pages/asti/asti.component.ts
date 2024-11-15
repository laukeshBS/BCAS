import { Component } from '@angular/core';
import { AstiDatatableComponent } from '../../datatables/asti-datatable/asti-datatable.component';

@Component({
  selector: 'app-asti',
  standalone: true,
  imports: [AstiDatatableComponent],
  templateUrl: './asti.component.html',
  styleUrl: './asti.component.css'
})
export class AstiComponent {

}
