import { Component } from '@angular/core';
import { DivisionGalleryDatatableComponent } from '../../datatables/division-gallery-datatable/division-gallery-datatable.component';

@Component({
  selector: 'app-division-gallery',
  standalone: true,
  imports: [DivisionGalleryDatatableComponent],
  templateUrl: './division-gallery.component.html',
  styleUrl: './division-gallery.component.css'
})
export class DivisionGalleryComponent {

}
