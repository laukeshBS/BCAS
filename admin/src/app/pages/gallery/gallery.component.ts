import { Component } from '@angular/core';
import { GalleryDatatableComponent } from '../../datatables/gallery-datatable/gallery-datatable.component';

@Component({
  selector: 'app-gallery',
  standalone: true,
  imports: [GalleryDatatableComponent],
  templateUrl: './gallery.component.html',
  styleUrl: './gallery.component.css'
})
export class GalleryComponent {

}
