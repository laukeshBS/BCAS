import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { QuizResultService } from '../../services/quiz-result.service';

declare var bootstrap: any;

@Component({
  selector: 'app-quiz-result-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './quiz-result-datatable.component.html',
  styleUrl: './quiz-result-datatable.component.css'
})
export class QuizResultDatatableComponent {

  events: any[] = [];
  selectedEvent: any = {};
  fileToUpload: File | null = null;
  limit = 10; 
  lang_code = 'en'; 
  selectedFile: any;
  selectedFileError: string | null = null; // Initialized with null
  currentPage: number = 1; // Current page for pagination
  totalItems: number = 0; // Total items to calculate total pages
  loading: boolean = false;
  lastPage: number = 0; // Last page]
  userId: number | null = null;
  apiBasePath='';

  constructor(private quizResultService: QuizResultService) {}

  ngOnInit(): void {
    this.loadList();
  }

  loadList(): void {
    this.apiBasePath=environment.apiDocBaseUrl;
    this.loading = true; // Start loading
    this.quizResultService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
      this.events = data.data;
      this.totalItems = data.total; // Assuming the API returns total items
      this.lastPage = Math.ceil(this.totalItems / this.limit);
      this.formatEventDates();
      this.loading = false; // Stop loading
    }, error => {
      console.error('Error loading events:', error);
      this.loading = false; // Stop loading on error
    });
  }
  // Get the page numbers to display in the pagination
  getPageNumbers(): number[] {
    const pagesToShow: number[] = [];
    const range = 2;  // Number of pages before and after current page to show
    const start = Math.max(2, this.currentPage - range); // Ensure at least 2 pages before current
    const end = Math.min(this.lastPage - 1, this.currentPage + range); // Ensure at least 2 pages after current

    // Generate the pages to show
    for (let i = start; i <= end; i++) {
      pagesToShow.push(i);
    }
    return pagesToShow;
  }
  // Change page method
  changePage(page: number): void {
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
      switch (event.status) {
        case "1":
          event.status = 'Draft';
          break;
        case "2":
          event.status = 'Pending';
          break;
        case "3":
          event.status = 'Published';
          break;
        default:
          event.status = '';
          break;
      }
    });
  }
  viewEvent(eventId: number): void {
    this.loading = true;
    this.quizResultService.getEvent(eventId).subscribe(event => {
      this.selectedEvent = event;  // Set the selected event data

      // Show the modal
      const modal = new bootstrap.Modal(document.getElementById('eventModal')!);
      modal.show();
      
      this.loading = false;
    }, error => {
      alert(error.error.message);
      this.loading = false;
    });
  }

}
