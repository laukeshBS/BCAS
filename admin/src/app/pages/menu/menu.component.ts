import { Component } from '@angular/core';
import { MenuDatatableComponent } from '../../datatables/menu-datatable/menu-datatable.component';


@Component({
  selector: 'app-menu',
  standalone: true,
  imports: [MenuDatatableComponent], 
  templateUrl: './menu.component.html',
  styleUrl: './menu.component.css'
})
export class MenuComponent {

}
