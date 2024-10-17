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
  private getbyidapiUrl = environment.apiBaseUrl + 'admin-document-by-id';
  private storeApiUrl = environment.apiBaseUrl + 'admin-document-store';
  private updateApiUrl = environment.apiBaseUrl + 'admin-document-update';
  private deleteApiUrl = environment.apiBaseUrl + 'admin-document-delete';

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
  allList(limit: number, lang_code: string,currentPage:number): Observable<any> {
    const body = { limit, lang_code, currentPage };
    return this.http.post<any>(this.apiUrl, body, { headers: this.getHeaders() });
  }
  // Get list of Acts and Plocies
  showDocument(doc_id: number, role_id: number): Observable<any> {
    const body = { doc_id, role_id };
    return this.http.post<any>(this.showDocumentapiUrl, body, { headers: this.getHeaders() })
      .pipe(tap((response: any) => console.log('Document response:', response)));
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
}
