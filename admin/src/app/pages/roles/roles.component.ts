import { Component } from '@angular/core';
import { RolesdatatableComponent } from "../../datatables/rolesdatatable/rolesdatatable.component";

@Component({
  selector: 'app-roles',
  standalone: true,
  imports: [RolesdatatableComponent],
  templateUrl: './roles.component.html',
  styleUrl: './roles.component.css'
})
export class RolesComponent {

}
