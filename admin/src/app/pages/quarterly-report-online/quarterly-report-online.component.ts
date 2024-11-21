import { Component } from '@angular/core';
import { QuarterlyReportOnlineDatatableComponent } from '../../datatables/quarterly-report-online-datatable/quarterly-report-online-datatable.component';

@Component({
  selector: 'app-quarterly-report-online',
  standalone: true,
  imports: [QuarterlyReportOnlineDatatableComponent],
  templateUrl: './quarterly-report-online.component.html',
  styleUrl: './quarterly-report-online.component.css'
})
export class QuarterlyReportOnlineComponent {

}
