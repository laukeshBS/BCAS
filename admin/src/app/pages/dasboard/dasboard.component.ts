import { Component } from '@angular/core';
import { PermissionsService } from '../../services/permissions.service';
import { CommonModule } from '@angular/common'; // Import CommonModule
import { DocumentCategoryChartComponent } from '../../document-category-chart/document-category-chart.component'; // Import the chart component
import { DocumentDataService } from '../../services/document-data.service';


@Component({
  selector: 'app-dasboard',
  standalone: true,
  imports: [CommonModule,DocumentCategoryChartComponent],
  templateUrl: './dasboard.component.html',
  styleUrl: './dasboard.component.css'
})
export class DasboardComponent {

  loading = false; // Loading state
  permissions: any[] = []; // Define type based on your response
  documentCount: any = {}; // Store document count data
  cardColors: string[] = [];

  constructor(
    private permissionsService: PermissionsService,private documentDataService: DocumentDataService,
  ) {}
  
  ngOnInit(): void {
    this.fetchPermissions();
    this.fetchDocumentCountData();
  }
  fetchPermissions(): void {
    this.loading = true; // Set loading to true
    const limit = 10; // Adjust as needed
    const lang_code = 'en'; // Adjust as needed

    console.log('Fetching permissions...'); // Debug log

    this.permissionsService.fetchPermissions(limit, lang_code).subscribe(
      response => {
        this.permissions = response; // Store permissions
        console.log('Permissions fetched successfully:', response);
      },
      error => {
        console.error('Error fetching permissions:', error);
        // Optionally, display an error message to the user
      },
      () => {
        this.loading = false; // Set loading to false on completion
      }
    );
  }

  fetchDocumentCountData(): void {
    this.loading = true;
    console.log('Fetching document count data...');

    this.documentDataService.getDocumentCountData().subscribe(
      response => {
        this.documentCount = response;
        this.generateRandomColors(); // Generate random colors for each card
      },
      error => {
        console.error('Error fetching document count data:', error);
      },
      () => {
        this.loading = false;
      }
    );
  }

  generateRandomColors(): void {
    // Generate random colors for the number of document categories
    this.cardColors = Object.keys(this.documentCount.data).map(() => this.getRandomColor());
  }

  getRandomColor(): string {
    let color = '#';
    
    // Generate a random value for each of the RGB channels (red, green, blue)
    for (let i = 0; i < 3; i++) {
      // Generate a random value between 90 and 210 for a balanced, medium-tone color
      const component = Math.floor(Math.random() * (210 - 90 + 1) + 90).toString(16);
      // Ensure each component is two digits long (pad with 0 if necessary)
      color += component.padStart(2, '0');
    }
  
    return color;
  }

  // Checks if the user has the given permission
  hasPermission(permission: string): boolean {
    return this.permissionsService.hasPermission(permission);
  }

  // Checks if the user has any of the given permissions
  hasAnyPermission(permissions: string[]): boolean {
    return this.permissionsService.hasAnyPermission(permissions);
  }
}
