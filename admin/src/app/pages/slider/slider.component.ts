import { Component } from '@angular/core';
import { SliderDatatableComponent } from '../../datatables/slider-datatable/slider-datatable.component';

@Component({
  selector: 'app-slider',
  standalone: true,
  imports: [SliderDatatableComponent],
  templateUrl: './slider.component.html',
  styleUrl: './slider.component.css'
})
export class SliderComponent {

}
