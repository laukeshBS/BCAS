import { HttpClient } from '@angular/common/http';
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

  // Get list of notice
  allList(limit: number, lang_code: string): Observable<any> {
    const body = { limit, lang_code };
    return this.http.post<any>(this.apiUrl, body);
  }

  // Fetch a single event by ID
  getEvent(id: number): Observable<any> {
    return this.http.get<any>(`${this.getbyidapiUrl}/${id}`);
  }

  // Add method to add an notice
  storeEvent(eventData: any): Observable<any> {
    return this.http.post<any>(`${this.storeApiUrl}`, eventData);
  }

  // Add method to update an notice
  updateEvent(id: number, eventData: any): Observable<any> {
    return this.http.post<any>(`${this.updateApiUrl}/${id}`, eventData);
  }

  // Add method to delete an notice
  deleteEvent(id: number): Observable<any> {
    return this.http.delete<any>(`${this.deleteApiUrl}/${id}`);
  }
}
