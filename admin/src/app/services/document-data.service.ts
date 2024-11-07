import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';  // Import HttpClient
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class DocumentDataService {

  private apiUrl = environment.apiBaseUrl + 'dashboard-data';  // API endpoint
  private apiUrlCount = environment.apiBaseUrl + 'dashboard-count-data';  // API endpoint

  constructor(private http: HttpClient) { }

  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('token'); // Ensure you fetch the token from localStorage
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json' // Specify content type if needed
    });
  }

  // Fetch data from the API
  getDocumentData(): Observable<any[]> {
    const headers = this.getHeaders();  // Fetch headers with token
    return this.http.get<any[]>(this.apiUrl, { headers });  // Pass headers in the HTTP request options
  }
  // Fetch data from the API
  getDocumentCountData(): Observable<any[]> {
    const headers = this.getHeaders();  // Fetch headers with token
    return this.http.get<any[]>(this.apiUrlCount, { headers });  // Pass headers in the HTTP request options
  }
}
