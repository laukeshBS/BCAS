import { Component } from '@angular/core';
import { AuditDatatableComponent } from '../../datatables/audit-datatable/audit-datatable.component';

@Component({
  selector: 'app-audit',
  standalone: true,
  imports: [AuditDatatableComponent],
  templateUrl: './audit.component.html',
  styleUrl: './audit.component.css'
})
export class AuditComponent {

}

