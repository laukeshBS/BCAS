import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class TendersService {

  private apiUrl = environment.apiBaseUrl + 'tender-list';
  private getbyidapiUrl = environment.apiBaseUrl + 'tender-list-by-id';
  private storeApiUrl = environment.apiBaseUrl + 'tender-store';
  private updateApiUrl = environment.apiBaseUrl + 'tender-update';
  private deleteApiUrl = environment.apiBaseUrl + 'tender-delete';

  constructor(private http: HttpClient) {}

  // Get list of tenders
  allList(limit: number, lang_code: string): Observable<any> {
    const body = { limit, lang_code };
    return this.http.post<any>(this.apiUrl, body);
  }

  // Fetch a single event by ID
  getEvent(id: number): Observable<any> {
    return this.http.get<any>(`${this.getbyidapiUrl}/${id}`);
  }

  // Add method to add an tender
  storeEvent(eventData: any): Observable<any> {
    return this.http.post<any>(`${this.storeApiUrl}`, eventData);
  }

  // Add method to update an tender
  updateEvent(id: number, eventData: any): Observable<any> {
    return this.http.post<any>(`${this.updateApiUrl}/${id}`, eventData);
  }

  // Add method to delete an tender
  deleteEvent(id: number): Observable<any> {
    return this.http.delete<any>(`${this.deleteApiUrl}/${id}`);
  }}
