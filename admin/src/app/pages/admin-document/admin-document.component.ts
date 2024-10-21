import { Component } from '@angular/core';
import { AdminDocumentDatatableComponent } from '../../datatables/admin-document-datatable/admin-document-datatable.component';

@Component({
  selector: 'app-admin-document',
  standalone: true,
  imports: [AdminDocumentDatatableComponent],
  templateUrl: './admin-document.component.html',
  styleUrl: './admin-document.component.css'
})
export class AdminDocumentComponent {

}
