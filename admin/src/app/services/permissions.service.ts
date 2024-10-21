import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { environment } from '../environments/environment';
import { map, catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class PermissionsService {
  private apiUrl = environment.apiBaseUrl + 'all_permissions';
  private userPermissions: string[] = [];

  constructor(private http: HttpClient) {}

  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('token');
    return new HttpHeaders({
      'Authorization': `Bearer ${token || ''}`,
      'Content-Type': 'application/json',
    });
  }

  // Consolidated fetchPermissions method
  fetchPermissions(limit: number, langCode: string): Observable<any> {
    const body = { limit, lang_code: langCode };
    
    return this.http.post<any>(this.apiUrl, body, { headers: this.getHeaders() })
      .pipe(
        map(response => {
          this.userPermissions = response.all_permissions ? response.all_permissions.map((perm: any) => perm.name) : [];
          return response;
        }),
        catchError(error => {
          console.error('Error fetching permissions:', error); // Log the error for debugging
          return throwError(() => new Error('Error fetching permissions'));
        })
      );
  }

  // Check if a user has a specific permission
  hasPermission(permission: string): boolean {
    return this.userPermissions.includes(permission);
  }

  // Check if a user has any of the provided permissions
  hasAnyPermission(permissions: string[]): boolean {
    return permissions.some(permission => this.hasPermission(permission));
  }

  // CRUD operations for permissions
  getPermissionById(id: number): Observable<any> {
    return this.http.get<any>(`${environment.apiBaseUrl}permissions-list-by-id/${id}`, { headers: this.getHeaders() });
  }

  storePermission(permissionData: any): Observable<any> {
    return this.http.post<any>(environment.apiBaseUrl + 'permissions-store', permissionData, { headers: this.getHeaders() });
  }

  updatePermission(id: number, permissionData: any): Observable<any> {
    return this.http.put<any>(`${environment.apiBaseUrl}permissions-update/${id}`, permissionData, { headers: this.getHeaders() });
  }

  deletePermission(id: number): Observable<any> {
    return this.http.delete<any>(`${environment.apiBaseUrl}permissions-delete/${id}`, { headers: this.getHeaders() });
  }
}
