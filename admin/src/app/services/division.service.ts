import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})

export class DivisionService {
  
  private apiUrl = environment.apiBaseUrl + 'division-list';
  private getbyidapiUrl = environment.apiBaseUrl + 'division-list-by-id';
  private storeApiUrl = environment.apiBaseUrl + 'division-store';
  private updateApiUrl = environment.apiBaseUrl + 'division-update';
  private deleteApiUrl = environment.apiBaseUrl + 'division-delete';

  constructor(private http: HttpClient) {}

  // Get list
  allList(limit: number, lang_code: string): Observable<any> {
    const body = { limit, lang_code };
    return this.http.post<any>(this.apiUrl, body);
  }

  // Fetch a single event by ID
  getEvent(id: number): Observable<any> {
    return this.http.get<any>(`${this.getbyidapiUrl}/${id}`);
  }

  // Add method to add
  storeEvent(eventData: any): Observable<any> {
    return this.http.post<any>(`${this.storeApiUrl}`, eventData);
  }

  // Add method to update
  updateEvent(id: number, eventData: any): Observable<any> {
    return this.http.post<any>(`${this.updateApiUrl}/${id}`, eventData);
  }

  // Add method to delete
  deleteEvent(id: number): Observable<any> {
    return this.http.delete<any>(`${this.deleteApiUrl}/${id}`);
  }
}
