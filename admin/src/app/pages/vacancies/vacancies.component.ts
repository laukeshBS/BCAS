import { Component } from '@angular/core';
import { VacanciesdatatableComponent } from '../../datatables/vacanciesdatatable/vacanciesdatatable.component';


@Component({
  selector: 'app-Vacancies',
  standalone: true,
  imports: [VacanciesdatatableComponent],
  templateUrl: './vacancies.component.html',
  styleUrl: './vacancies.component.css'
})
export class VacanciesComponent {

}
