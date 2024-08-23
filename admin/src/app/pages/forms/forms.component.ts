import { Component } from '@angular/core';
import { FormsdatatableComponent } from '../../datatables/formsdatatable/formsdatatable.component';


@Component({
  selector: 'app-Forms',
  standalone: true,
  imports: [FormsdatatableComponent],
  templateUrl: './forms.component.html',
  styleUrl: './forms.component.css'
})
export class FormsComponent {

}
