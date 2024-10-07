import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';


@Injectable({
  providedIn: 'root'
})
export class SlideService {

  private apiUrl = environment.apiBaseUrl + 'slide-list';
  private getbyidapiUrl = environment.apiBaseUrl + 'slide-by-id';
  private storeApiUrl = environment.apiBaseUrl + 'slide-store';
  private updateApiUrl = environment.apiBaseUrl + 'slide-update';
  private deleteApiUrl = environment.apiBaseUrl + 'slide-delete';

  constructor(private http: HttpClient) {}

  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('token'); // Ensure you fetch the token from localStorage
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json' // Specify content type if needed
    });
  }
  private getHeaders2(): HttpHeaders {
    const token = localStorage.getItem('token'); // Ensure you fetch the token from localStorage
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      // 'Content-Type': 'application/json' // Specify content type if needed
    });
  }

  // Get list of Acts and Plocies
  allList(limit: number, lang_code: string, currentPage: number): Observable<any> {
    const body = { limit, lang_code, currentPage};
    return this.http.post<any>(this.apiUrl, body, { headers: this.getHeaders() });
  }

  // Fetch a single event by ID
  getEvent(id: number): Observable<any> {
    return this.http.get<any>(`${this.getbyidapiUrl}/${id}`, { headers: this.getHeaders() });
  }

  // Add method to add an Acts and Plocies
  storeEvent(eventData: any): Observable<any> {
    return this.http.post<any>(this.storeApiUrl, eventData, { headers: this.getHeaders2() });
  }

  // Add method to update an Acts and Plocies
  updateEvent(id: number, eventData: any): Observable<any> {
    return this.http.post<any>(`${this.updateApiUrl}/${id}`, eventData, { headers: this.getHeaders2() });
  }

  // Add method to delete an Acts and Plocies
  deleteEvent(id: number): Observable<any> {
    return this.http.delete<any>(`${this.deleteApiUrl}/${id}`, { headers: this.getHeaders2() });
  }
}
