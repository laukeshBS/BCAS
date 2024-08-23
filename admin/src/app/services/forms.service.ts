import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class FormsService {

  private apiUrl = environment.apiBaseUrl + 'form-list';
  private getbyidapiUrl = environment.apiBaseUrl + 'form-list-by-id';
  private storeApiUrl = environment.apiBaseUrl + 'form-store';
  private updateApiUrl = environment.apiBaseUrl + 'form-update';
  private deleteApiUrl = environment.apiBaseUrl + 'form-delete';

  constructor(private http: HttpClient) {}

  // Get list of forms
  allList(limit: number, lang_code: string): Observable<any> {
    const body = { limit, lang_code };
    return this.http.post<any>(this.apiUrl, body);
  }

  // Fetch a single event by ID
  getEvent(id: number): Observable<any> {
    return this.http.get<any>(`${this.getbyidapiUrl}/${id}`);
  }

  // Add method to add an form
  storeEvent(eventData: any): Observable<any> {
    return this.http.post<any>(`${this.storeApiUrl}`, eventData);
  }

  // Add method to update an form
  updateEvent(id: number, eventData: any): Observable<any> {
    return this.http.post<any>(`${this.updateApiUrl}/${id}`, eventData);
  }

  // Add method to delete an form
  deleteEvent(id: number): Observable<any> {
    return this.http.delete<any>(`${this.deleteApiUrl}/${id}`);
  }
}
