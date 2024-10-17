import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment'

@Injectable({
  providedIn: 'root'
})
export class RolesService {
  private apiUrl = environment.apiBaseUrl + 'roles-list';
  private all_permissions = environment.apiBaseUrl + 'all_permissions';
  private getbyidapiUrl = environment.apiBaseUrl + 'roles-list-by-id';
  private storeApiUrl = environment.apiBaseUrl + 'roles-store';
  private updateApiUrl = environment.apiBaseUrl + 'roles-update';
  private deleteApiUrl = environment.apiBaseUrl + 'roles-delete';

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

  // Get list
  allList(limit: number, lang_code: string): Observable<any> {
    const body = { limit, lang_code };
    return this.http.post<any>(this.apiUrl, body, { headers: this.getHeaders() });
  }

  // Fetch a single event by ID
  getEvent(id: number): Observable<any> {
    return this.http.get<any>(`${this.getbyidapiUrl}/${id}`, { headers: this.getHeaders() });
  }

  // Add method to add
  storeEvent(eventData: any): Observable<any> {
    return this.http.post<any>(`${this.storeApiUrl}`, eventData, { headers: this.getHeaders2() });
  }

  // Add method to update
  updateEvent(id: number, eventData: any): Observable<any> {
    return this.http.post<any>(`${this.updateApiUrl}/${id}`, eventData, { headers: this.getHeaders2() });
  }

  // Add method to delete
  deleteEvent(id: number): Observable<any> {
    return this.http.delete<any>(`${this.deleteApiUrl}/${id}`, { headers: this.getHeaders2() });
  }
  getAllPermissions(): Observable<any> {
   // return this.http.post(`${this.all_permissions}`, { headers: this.getHeaders() });
    return this.http.post<any>(this.all_permissions,{ limit: 10, lang_code: 'en' }, { headers: this.getHeaders() });
  }

  createRole(roleData: any): Observable<any> {
    return this.http.post(this.storeApiUrl, roleData, { headers: this.getHeaders() });
  }
}
