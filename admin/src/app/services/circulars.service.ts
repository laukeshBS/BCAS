import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class CircularsService {

  private apiUrl = environment.apiBaseUrl + 'circular-list';
  private getbyidapiUrl = environment.apiBaseUrl + 'circular-list-by-id';
  private storeApiUrl = environment.apiBaseUrl + 'circular-store';
  private updateApiUrl = environment.apiBaseUrl + 'circular-update';
  private deleteApiUrl = environment.apiBaseUrl + 'circular-delete';

  constructor(private http: HttpClient) {}

  // Get list of circulars
  allList(limit: number, lang_code: string): Observable<any> {
    const body = { limit, lang_code };
    return this.http.post<any>(this.apiUrl, body);
  }

  // Fetch a single circular by ID
  getEvent(id: number): Observable<any> {
    return this.http.get<any>(`${this.getbyidapiUrl}/${id}`);
  }

  // Add method to add an circular
  storeEvent(eventData: any): Observable<any> {
    return this.http.post<any>(`${this.storeApiUrl}`, eventData);
  }

  // Add method to update an circular
  updateEvent(id: number, eventData: any): Observable<any> {
    return this.http.post<any>(`${this.updateApiUrl}/${id}`, eventData);
  }

  // Add method to delete an circular
  deleteEvent(id: number): Observable<any> {
    return this.http.delete<any>(`${this.deleteApiUrl}/${id}`);
  }
}
