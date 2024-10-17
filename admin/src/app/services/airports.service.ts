import { Injectable } from '@angular/core';
import { environment } from '../environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs/internal/Observable';

@Injectable({
  providedIn: 'root'
})
export class AirportsService {


  baseUrl: string = environment.apiBaseUrl;
 constructor(private http: HttpClient) {}
 getAirportList(params: any): Observable<any[]> {

    const headers = new HttpHeaders({
      'Content-Type': 'application/json',
    });
  // // Make the HTTP request with the headers as options
    return this.http.post<any>(this.baseUrl + 'airport-list', params, { headers });
  }
}
