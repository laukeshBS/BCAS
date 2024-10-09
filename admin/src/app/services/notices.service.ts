import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class NoticesService {

  private apiUrl = environment.apiBaseUrl + 'notice-list';
  private getbyidapiUrl = environment.apiBaseUrl + 'notice-list-by-id';
  private storeApiUrl = environment.apiBaseUrl + 'notice-store';
  private updateApiUrl = environment.apiBaseUrl + 'notice-update';
  private deleteApiUrl = environment.apiBaseUrl + 'notice-delete';

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

  // Get list of notice
  allList(limit: number, lang_code: string): Observable<any> {
    const body = { limit, lang_code };
    return this.http.post<any>(this.apiUrl, body, { headers: this.getHeaders() });
  }

  // Fetch a single event by ID
  getEvent(id: number): Observable<any> {
    return this.http.get<any>(`${this.getbyidapiUrl}/${id}`, { headers: this.getHeaders() });
  }

  // Add method to add an notice
  storeEvent(eventData: any): Observable<any> {
    return this.http.post<any>(`${this.storeApiUrl}`, eventData, { headers: this.getHeaders2() });
  }

  // Add method to update an notice
  updateEvent(id: number, eventData: any): Observable<any> {
    return this.http.post<any>(`${this.updateApiUrl}/${id}`, eventData, { headers: this.getHeaders2() });
  }

  // Add method to delete an notice
  deleteEvent(id: number): Observable<any> {
    return this.http.delete<any>(`${this.deleteApiUrl}/${id}`, { headers: this.getHeaders2() });
  }
}
