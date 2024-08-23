import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class ActsandpoliciesService {

  private apiUrl = environment.apiBaseUrl + 'acts-and-policies-list';
  private getbyidapiUrl = environment.apiBaseUrl + 'acts-and-policies-list-by-id';
  private storeApiUrl = environment.apiBaseUrl + 'acts-and-policies-store';
  private updateApiUrl = environment.apiBaseUrl + 'acts-and-policies-update';
  private deleteApiUrl = environment.apiBaseUrl + 'acts-and-policies-delete';

  constructor(private http: HttpClient) {}

  // Get list of Acts and Plocies
  allList(limit: number, lang_code: string): Observable<any> {
    const body = { limit, lang_code };
    return this.http.post<any>(this.apiUrl, body);
  }

  // Fetch a single event by ID
  getEvent(id: number): Observable<any> {
    return this.http.get<any>(`${this.getbyidapiUrl}/${id}`);
  }

  // Add method to add an Acts and Plocies
  storeEvent(eventData: any): Observable<any> {
    return this.http.post<any>(`${this.storeApiUrl}`, eventData);
  }

  // Add method to update an Acts and Plocies
  updateEvent(id: number, eventData: any): Observable<any> {
    return this.http.post<any>(`${this.updateApiUrl}/${id}`, eventData);
  }

  // Add method to delete an Acts and Plocies
  deleteEvent(id: number): Observable<any> {
    return this.http.delete<any>(`${this.deleteApiUrl}/${id}`);
  }
}
