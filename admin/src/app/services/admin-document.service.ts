import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { environment } from '../environments/environment';


@Injectable({
  providedIn: 'root'
})
export class AdminDocumentService {

  private apiUrl = environment.apiBaseUrl + 'admin-document-list';
  private showDocumentapiUrl = environment.apiBaseUrl + 'admin-document';
  private getRoleapiUrl = environment.apiBaseUrl + 'roles';
  private documentCategoryapiUrl = environment.apiBaseUrl + 'admin-document-category';
  private getbyidapiUrl = environment.apiBaseUrl + 'admin-document-by-id';
  private storeApiUrl = environment.apiBaseUrl + 'admin-document-store';
  private updateApiUrl = environment.apiBaseUrl + 'admin-document-update';
  private deleteApiUrl = environment.apiBaseUrl + 'admin-document-delete';
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

  // Get list 
  allList(limit: number, lang_code: string,currentPage:number,roleIds: number[],userRankId: number): Observable<any> {
    const body = { limit, lang_code, currentPage,roleIds: roleIds,userRankId: userRankId };
    return this.http.post<any>(this.apiUrl, body, { headers: this.getHeaders() });
  }
  // Get Document view
  showDocument(doc_id: number, role_id: number): Observable<Blob> {
    const body = { doc_id, role_id };
    return this.http.post<Blob>(this.showDocumentapiUrl, body, {
      headers: this.getHeaders(),
      responseType: 'blob' as 'json' // Set responseType to 'blob'
    }).pipe(tap(response => console.log('Document response:', response)));
  }
  
  // Get Categories
  documentCategory(): Observable<any> {
    return this.http.get<any>(`${this.documentCategoryapiUrl}`, { headers: this.getHeaders() });
  }
  // Get Rank List for dropdow
  getRankList(): Observable<any> {
    return this.http.get<any>(`${this.apiUrlRankDropdown}`, { headers: this.getHeaders() });
  }

  // Get role
  getRole(): Observable<any> {
    return this.http.get<any>(`${this.getRoleapiUrl}`, { headers: this.getHeaders() });
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
