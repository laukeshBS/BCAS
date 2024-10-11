import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class LanguageService {
  private apiUrl = environment.apiBaseUrl + 'langlist';
  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('token'); // Ensure you fetch the token from localStorage
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json' // Specify content type if needed
    });
  }

  constructor(private http: HttpClient) {}
  languagesList(): Observable<any[]> {
    const headers = new HttpHeaders({
     // 'Content-Type': 'application/json',
    //  'APP_KEY_API': this.API_KEY,
    });
    return this.http.post<any[]>(`${this.apiUrl}`, {headers});
  }
}
