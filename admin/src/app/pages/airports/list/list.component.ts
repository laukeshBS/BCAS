import { Component, OnInit } from '@angular/core';
import { AirportsService } from '../../../services/airports.service';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

@Component({
  selector: 'app-list',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './list.component.html',
  styleUrl: './list.component.css'
})
export class ListComponent implements OnInit  {

  page: any;
  per_page: any;
  airportlists: any;

constructor(private airportsService: AirportsService,
    private sanitizer: DomSanitizer,private router: Router,
   ) { }
 
  ngOnInit(): void {
   this.getAirportlist();
  }
  getAirportlist(){
    const params = {
      per_page: this.per_page,
      page: this.page,
    };
  
    this.airportsService.getAirportList(params).subscribe({
      next: (data: any) => {
       
        this.airportlists = Array.isArray(data.data) ? data.data : [];
        console.log( this.airportlists);
      }
    });
  }

   addEvent() {
    alert('Ok');
         this.router.navigate([`airports/add`]);
    }
    deleteEvent(arg0: any) {
      alert('Ok');
    }
    editEvent(arg0: any) {
      alert('Ok');
    }
  getSanitizedHtml(html: string): SafeHtml {
    //console.log(html);
          if (html === 'null' || !html) {
          return  this['sanitizer'].bypassSecurityTrustHtml('');
        }
        return this['sanitizer'].bypassSecurityTrustHtml(html);

   }

}
