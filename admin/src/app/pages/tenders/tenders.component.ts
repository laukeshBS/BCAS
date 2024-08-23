import { Component } from '@angular/core';
import { TendersdatatableComponent } from '../../datatables/tendersdatatable/tendersdatatable.component';


@Component({
  selector: 'app-Tenders',
  standalone: true,
  imports: [TendersdatatableComponent],
  templateUrl: './tenders.component.html',
  styleUrl: './tenders.component.css'
})
export class TendersComponent {

}
