import { Component } from '@angular/core';
import { CommonTitleDatatableComponent } from '../../datatables/common-title-datatable/common-title-datatable.component';

@Component({
  selector: 'app-common-title',
  standalone: true,
  imports: [CommonTitleDatatableComponent],
  templateUrl: './common-title.component.html',
  styleUrl: './common-title.component.css'
})
export class CommonTitleComponent {

}
