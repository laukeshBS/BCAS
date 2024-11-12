import { Component } from '@angular/core';
import { OpsiSecurityDatatableComponent } from '../../datatables/opsi-security-datatable/opsi-security-datatable.component';

@Component({
  selector: 'app-opsi-security',
  standalone: true,
  imports: [OpsiSecurityDatatableComponent],
  templateUrl: './opsi-security.component.html',
  styleUrl: './opsi-security.component.css'
})
export class OpsiSecurityComponent {

}
