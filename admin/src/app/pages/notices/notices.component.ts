import { Component } from '@angular/core';
import { NoticesdatatableComponent } from '../../datatables/noticesdatatable/noticesdatatable.component';


@Component({
  selector: 'app-Notices',
  standalone: true,
  imports: [NoticesdatatableComponent],
  templateUrl: './notices.component.html',
  styleUrl: './notices.component.css'
})
export class NoticesComponent {

}
