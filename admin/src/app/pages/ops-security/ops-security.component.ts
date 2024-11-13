import { Component } from '@angular/core';
import { OpsSecurityDatatableComponent } from '../../datatables/ops-security-datatable/ops-security-datatable.component';

@Component({
  selector: 'app-ops-security',
  standalone: true,
  imports: [OpsSecurityDatatableComponent],
  templateUrl: './ops-security.component.html',
  styleUrl: './ops-security.component.css'
})
export class OpsSecurityComponent {

}
