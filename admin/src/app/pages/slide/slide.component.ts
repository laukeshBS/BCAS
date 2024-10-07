import { Component } from '@angular/core';
import { SlideDatatableComponent } from '../../datatables/slide-datatable/slide-datatable.component';

@Component({
  selector: 'app-slide',
  standalone: true,
  imports: [SlideDatatableComponent],
  templateUrl: './slide.component.html',
  styleUrl: './slide.component.css'
})
export class SlideComponent {

}
