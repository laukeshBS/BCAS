import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { environment } from '../environments/environment';
import { map, catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class PermissionsService {
  //private apiUrl = `${environment.apiBaseUrl}all_permissions`;
  private apiUrl = environment.apiBaseUrl + 'all_permissions';
  private getbyidapiUrl = environment.apiBaseUrl + 'permissions-list-by-id';
  private storeApiUrl = environment.apiBaseUrl + 'permissions-store';
  private updateApiUrl = environment.apiBaseUrl + 'permissions-update';
  private deleteApiUrl = environment.apiBaseUrl + 'permissions-delete';

  private userPermissions: string[] = [];

  constructor(private http: HttpClient) {}

  private getHeaders(): HttpHeaders {
 
    const token = localStorage.getItem('token'); 
   // Ensure you fetch the token from localStorage
    return new HttpHeaders({
      'Authorization': `Bearer ${token || ''}`, // Use empty string if token is missing
      'Content-Type': 'application/json',
     
    });
  }
  fetchPermissions(limit: number, lang_code: string): Observable<any> {
    const body = { limit, lang_code }; // Use an object for the body as per your API requirements
   
  
    return this.http.post<any>(this.apiUrl,{ limit: 10, lang_code: 'en' }, { headers: this.getHeaders() }) // Send the body and headers
      .pipe(
        map((response: any) => {
          // Store permissions locally
          this.userPermissions = response.all_permissions ? response.all_permissions.map((perm: any) => perm.name) : [];
          
          // Log userPermissions after setting them
       //   console.log('User Permissions:', this.userPermissions);
          return response;
        }),
        catchError(error => {
          console.error('Error fetching permissions:', error); // Log the error
          return throwError(() => new Error('Error fetching permissions')); // Rethrow the error for further handling if needed
        })
      );
  }

  // Fetch all permissions from the API and store them in the service
 

  // Check if a user has a specific permission
  hasPermission(permission: string): boolean {
    //console.log('userPermissions permission for:', this.userPermissions);
    return this.userPermissions.includes(permission);
  }

  // Check if a user has at least one of the provided permissions
  hasAnyPermission(permissions: string[]): boolean {
    //console.log('Checking any permission for:', permissions);
    return permissions.some(permission => this.hasPermission(permission));
  }

  // Example methods for handling CRUD operations with permissions (if needed)
  getPermissionById(id: number): Observable<any> {
    return this.http.get<any>(`${this.getbyidapiUrl}/${id}`, { headers: this.getHeaders() });
  }

  storePermission(permissionData: any): Observable<any> {
    return this.http.post<any>(this.storeApiUrl, permissionData, { headers: this.getHeaders() });
  }

  updatePermission(id: number, permissionData: any): Observable<any> {
    return this.http.put<any>(`${this.updateApiUrl}/${id}`, permissionData, { headers: this.getHeaders() });
  }

  deletePermission(id: number): Observable<any> {
    return this.http.delete<any>(`${this.deleteApiUrl}/${id}`, { headers: this.getHeaders() });
  }
}
