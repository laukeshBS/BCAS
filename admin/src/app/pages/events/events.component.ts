import { Component } from '@angular/core';
import { EventsdatatableComponent } from '../../datatables/eventsdatatable/eventsdatatable.component';


@Component({
  selector: 'app-Events',
  standalone: true,
  imports: [EventsdatatableComponent],
  templateUrl: './events.component.html',
  styleUrl: './events.component.css'
})
export class EventsComponent {

}
