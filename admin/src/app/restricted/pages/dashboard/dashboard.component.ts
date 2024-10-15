import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [],
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.css'
})
export class DashboardComponent  implements OnInit {

  title: string = '';

  constructor(private route: ActivatedRoute) { }

  ngOnInit(): void {
    // Subscribing to the route data to get the title
    this.route.data.subscribe(data => {
      this.title = data['title'];  // 'Orders' will be assigned based on the route
    });
  }
}
