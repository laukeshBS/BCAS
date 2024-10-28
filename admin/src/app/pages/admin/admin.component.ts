import { Component } from '@angular/core';
import { AdminDatatableComponent } from '../../datatables/admin-datatable/admin-datatable.component';

@Component({
  selector: 'app-admin',
  standalone: true,
  imports: [AdminDatatableComponent],
  templateUrl: './admin.component.html',
  styleUrl: './admin.component.css'
})
export class AdminComponent {

}
