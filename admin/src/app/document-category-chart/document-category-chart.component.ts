import { Component, AfterViewInit } from '@angular/core';
import { Chart, registerables } from 'chart.js';
import { DocumentDataService } from '../services/document-data.service';  // Import the service

// Register all necessary components for Chart.js
Chart.register(...registerables);

@Component({
  selector: 'app-document-category-chart',
  standalone: true,
  imports: [],
  templateUrl: './document-category-chart.component.html',
  styleUrls: ['./document-category-chart.component.css']
})
export class DocumentCategoryChartComponent implements AfterViewInit {

  constructor(private documentDataService: DocumentDataService) {}

  ngAfterViewInit(): void {
    // Fetch the data from the service
    this.documentDataService.getDocumentData().subscribe(data => {
      // Prepare data for chart
      console.log(data);
      const years = [...new Set(data.map(d => d.year))]; // Get unique years
      const categories = [...new Set(data.map(d => d.category_name))]; // Get unique categories

      // Prepare the datasets for each year
      const datasets = years.map(year => {
        return {
          label: `Year ${year}`,
          data: categories.map(category => {
            const item = data.find(d => d.category_name === category && d.year === year);
            return item ? item.document_count : 0; // Return the document count or 0 if not found
          }),
          backgroundColor: 'rgba(255, 99, 132, 0.2)',  // Change to any color of your choice
          borderColor: 'rgba(255, 99, 132, 1)',  // Change to any color of your choice
          borderWidth: 1
        };
      });

      // Get the canvas element
      const canvas = document.getElementById('myChart') as HTMLCanvasElement | null;
      if (canvas) {
        const ctx = canvas.getContext('2d');
        if (ctx) {
          // Create the chart
          new Chart(ctx, {
            type: 'bar',  // Bar chart type
            data: {
              labels: categories,  // Categories on the X-axis
              datasets: datasets  // Datasets for each year
            },
            options: {
              responsive: true,
              scales: {
                y: {
                  beginAtZero: true  // Ensures the Y-axis starts from 0
                }
              }
            }
          });
        } else {
          console.error('Failed to get 2D context from canvas');
        }
      } else {
        console.error('Canvas element not found');
      }
    }, error => {
      console.error('Error fetching data:', error);  // Handle any errors from the API call
    });
  }
}
