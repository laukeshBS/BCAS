import { Component } from '@angular/core';
import { AdminDocumentCategoryDatatableComponent } from '../../datatables/admin-document-category-datatable/admin-document-category-datatable.component';

@Component({
  selector: 'app-admin-document-category',
  standalone: true,
  imports: [AdminDocumentCategoryDatatableComponent],
  templateUrl: './admin-document-category.component.html',
  styleUrl: './admin-document-category.component.css'
})
export class AdminDocumentCategoryComponent {

}

