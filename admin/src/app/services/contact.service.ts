import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';


@Injectable({
  providedIn: 'root'
})
export class ContactService {

  private apiUrl = environment.apiBaseUrl + 'contacts-list';
  private getbyidapiUrl = environment.apiBaseUrl + 'contact-list-by-id';
  private storeApiUrl = environment.apiBaseUrl + 'contact-store';
  private updateApiUrl = environment.apiBaseUrl + 'contact-update';
  private deleteApiUrl = environment.apiBaseUrl + 'contact-delete';
  private fetchDivisionsapiUrl = environment.apiBaseUrl + 'division-dropdown-list';
  private fetchRegionsapiUrl = environment.apiBaseUrl + 'region-dropdown-list';

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
  // Fetch Division list
  fetchDivisions(lang_code: string): Observable<any> {
    return this.http.get<any>(`${this.fetchDivisionsapiUrl}/${lang_code}`, { headers: this.getHeaders() });
  }
  // Fetch Region list
  fetchRegions(lang_code: string): Observable<any> {
    return this.http.get<any>(`${this.fetchRegionsapiUrl}/${lang_code}`, { headers: this.getHeaders() });
  }
}
