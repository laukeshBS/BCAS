import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';


@Injectable({
  providedIn: 'root'
})
export class AdminService {

  private apiUrl = environment.apiBaseUrl + 'admin-list';
  //private apiUrl = environment.apiBaseUrl + 'all_permissions';
  private getbyidapiUrl = environment.apiBaseUrl + 'admin-list-by-id';
  private storeApiUrl = environment.apiBaseUrl + 'admin-store';
  private updateApiUrl = environment.apiBaseUrl + 'admin-update';
  private deleteApiUrl = environment.apiBaseUrl + 'admin-delete';
  private getRoleapiUrl = environment.apiBaseUrl + 'roles';
  private apiUrlRankDropdown = environment.apiBaseUrl + 'rank-dropdown-list';  // API endpoint

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
    const body = { limit, lang_code,currentPage };
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

  // Get role
  getRoles(): Observable<any> {
    return this.http.get<any>(`${this.getRoleapiUrl}`, { headers: this.getHeaders() });
  }

  // Get Rank List for dropdow
  getRankList(): Observable<any> {
    return this.http.get<any>(`${this.apiUrlRankDropdown}`, { headers: this.getHeaders() });
  }
}

