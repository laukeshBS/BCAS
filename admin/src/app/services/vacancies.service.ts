import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class VacanciesService {

  private apiUrl = environment.apiBaseUrl + 'vacancy-list';
  private getbyidapiUrl = environment.apiBaseUrl + 'vacancy-list-by-id';
  private storeApiUrl = environment.apiBaseUrl + 'vacancy-store';
  private updateApiUrl = environment.apiBaseUrl + 'vacancy-update';
  private deleteApiUrl = environment.apiBaseUrl + 'vacancy-delete';

  constructor(private http: HttpClient) {}

  // Get list of vacancy
  allList(limit: number, lang_code: string): Observable<any> {
    const body = { limit, lang_code };
    return this.http.post<any>(this.apiUrl, body);
  }

  // Fetch a single event by ID
  getEvent(id: number): Observable<any> {
    return this.http.get<any>(`${this.getbyidapiUrl}/${id}`);
  }

  // Add method to add an vacancy
  storeEvent(eventData: any): Observable<any> {
    return this.http.post<any>(`${this.storeApiUrl}`, eventData);
  }

  // Add method to update an vacancy
  updateEvent(id: number, eventData: any): Observable<any> {
    return this.http.post<any>(`${this.updateApiUrl}/${id}`, eventData);
  }

  // Add method to delete an vacancy
  deleteEvent(id: number): Observable<any> {
    return this.http.delete<any>(`${this.deleteApiUrl}/${id}`);
  }
}
