import { Component } from '@angular/core';
import { ContactDatatableComponent } from '../../datatables/contact-datatable/contact-datatable.component';

@Component({
  selector: 'app-contact',
  standalone: true,
  imports: [ContactDatatableComponent],
  templateUrl: './contact.component.html',
  styleUrl: './contact.component.css'
})
export class ContactComponent {

}
