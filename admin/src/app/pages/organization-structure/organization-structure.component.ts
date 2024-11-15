import { Component } from '@angular/core';
import { OrganizationStructureDatatableComponent } from '../../datatables/organization-structure-datatable/organization-structure-datatable.component';

@Component({
  selector: 'app-organization-structure',
  standalone: true,
  imports: [OrganizationStructureDatatableComponent],
  templateUrl: './organization-structure.component.html',
  styleUrl: './organization-structure.component.css'
})
export class OrganizationStructureComponent {

}
