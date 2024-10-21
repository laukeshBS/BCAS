import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class AdminDocumentCategoryService {

  private apiUrl = environment.apiBaseUrl + 'admin-document-category-list';
  private documentCategoryapiUrl = environment.apiBaseUrl + 'admin-document-category';
  private getbyidapiUrl = environment.apiBaseUrl + 'admin-document-category-by-id';
  private storeApiUrl = environment.apiBaseUrl + 'admin-document-category-store';
  private updateApiUrl = environment.apiBaseUrl + 'admin-document-category-update';
  private deleteApiUrl = environment.apiBaseUrl + 'admin-document-category-delete';

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
  allList(limit: number, lang_code: string,currentPage:number): Observable<any> {
    const body = { limit, lang_code, currentPage };
    return this.http.post<any>(this.apiUrl, body, { headers: this.getHeaders() });
  }
  
  // Get Categories
  documentCategory(): Observable<any> {
    return this.http.get<any>(`${this.documentCategoryapiUrl}`, { headers: this.getHeaders() });
  }

  // Fetch a single event by ID
  getEvent(id: number): Observable<any> {
    return this.http.get<any>(`${this.getbyidapiUrl}/${id}`, { headers: this.getHeaders() });
  }

  // Add method to add
  storeEvent(eventData: any): Observable<any> {
    return this.http.post<any>(this.storeApiUrl, eventData, { headers: this.getHeaders2() });
  }

  // Add method to update 
  updateEvent(id: number, eventData: any): Observable<any> {
    return this.http.post<any>(`${this.updateApiUrl}/${id}`, eventData, { headers: this.getHeaders2() });
  }

  // Add method to delete 
  deleteEvent(id: number): Observable<any> {
    return this.http.delete<any>(`${this.deleteApiUrl}/${id}`, { headers: this.getHeaders2() });
  }
}
