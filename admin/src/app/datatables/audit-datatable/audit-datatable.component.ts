import { Component } from '@angular/core';
import { AuditService } from '../../services/audit.service';  // Import the service
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';

declare var bootstrap: any;

@Component({
  selector: 'app-audit-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './audit-datatable.component.html',
  styleUrl: './audit-datatable.component.css'
})
export class AuditDatatableComponent {
  events: any[] = [];
  limit = 10; 
  lang_code = 'en'; 
  fromDate: string = '2024-01-01'; // Example from date
  toDate: string = '2024-12-31';

  constructor(private auditService: AuditService) {}

  ngOnInit(): void {
    this.loadList();
  }

  loading: boolean = false;
  currentPage: number = 1;
  totalItems: number = 0;
  lastPage: number = 0;

  loadList(): void {
    this.loading = true; // Start loading
    this.auditService.allList(this.limit, this.lang_code, this.currentPage).subscribe((data: { data: any[]; total: number; }) => {
      this.events = data.data;
      this.totalItems = data.total; // Assuming the API returns total items
      this.lastPage = Math.ceil(this.totalItems / this.limit);
      this.formatEventDates();
      this.loading = false; // Stop loading
    }, (error: any) => {
      console.error('Error loading events:', error);
      this.loading = false; // Stop loading on error
    });
  }
  // Change page method
  changePage(page: number): void {
    console.log('Changing to page:', page); // Debugging line
    if (page < 1 || page > this.lastPage) return; // Prevent out of bounds
    this.currentPage = page;
    this.loadList(); // Reload data
  }

  // Total pages calculation
  totalPages(): number {
    return Math.ceil(this.totalItems / this.limit);
  }

  formatEventDates(): void {
    this.events.forEach(event => {
      event.created_at = new Date(event.created_at).toLocaleDateString('en-GB');
      event.start_date = new Date(event.start_date).toLocaleDateString('en-GB');
      event.end_date = new Date(event.end_date).toLocaleDateString('en-GB');
      switch (event.status) {
        case 1:
          event.status = 'Draft';
          break;
        case 2:
          event.status = 'Pending';
          break;
        case 3:
          event.status = 'Published';
          break;
        default:
          event.status = '';
          break;
      }
      if (event.document!='') {
        event.document = '<a href="'+event.document+'">'+event.title+' Document</a>';
      }else{
        event.document = '';
      }
    });
  }
  exportPDF() {
    // Get today's date and the date 7 days ago
    const today = new Date();
    const sevenDaysAgo = new Date();
    sevenDaysAgo.setDate(today.getDate() - 7);
  
    // Convert fromDate and toDate to Date objects (assuming they are strings)
    const fromDateObj = new Date(this.fromDate);
    const toDateObj = new Date(this.toDate);
  
    // Check if the fromDate and toDate are within the last 7 days
    if (fromDateObj < sevenDaysAgo || toDateObj > today) {
      alert('You can only export data for the last 7 days.');
      return; // Exit the function if the date range is invalid
    }
  
    // Start by showing a loading spinner
    // (You may want to implement the spinner logic here)
  
    // Call the exportPDF method from the service
    this.auditService.exportPDF(this.fromDate, this.toDate).subscribe(
      (response: Blob) => {
        // Create a temporary URL for the Blob to trigger the file download
        const url = window.URL.createObjectURL(response);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'audit_report.pdf'; // Specify a default file name
        a.click();
        window.URL.revokeObjectURL(url); // Clean up after download
  
        // Show success notification or message
        alert('Report generated successfully.');
  
        // Hide loading spinner
        // (Hide the spinner here if you have implemented one)
      },
      (error) => {
        console.error('Error exporting PDF:', error);
        alert('An error occurred while generating the PDF. Please try again.');
  
        // Hide loading spinner
        // (Hide the spinner here if you have implemented one)
      }
    );
  }
  
  
  
  

}
