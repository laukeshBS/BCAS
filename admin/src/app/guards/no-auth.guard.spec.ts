import { TestBed } from '@angular/core/testing';
import { NoAuthGuard } from './no-auth.guard';
import { AuthService } from '../services/auth.service';
import { Router } from '@angular/router';

describe('NoAuthGuard', () => {
  let authService: jasmine.SpyObj<AuthService>;
  let router: Router;
  let guard: NoAuthGuard;

  const mockRouter = {
    navigate: jasmine.createSpy('navigate'),
  };

  beforeEach(() => {
    // Create a spy object with the isAuthenticated method
    authService = jasmine.createSpyObj('AuthService', ['isAuthenticated']);

    TestBed.configureTestingModule({
      providers: [
        NoAuthGuard,
        { provide: AuthService, useValue: authService },
        { provide: Router, useValue: mockRouter },
      ],
    });

    guard = TestBed.inject(NoAuthGuard);
    router = TestBed.inject(Router);
  });

  it('should be created', () => {
    expect(guard).toBeTruthy();
  });

  it('should allow access to the login page if not authenticated', () => {
    authService.isAuthenticated.and.returnValue(false); // User is not authenticated

    const result = guard.canActivate();

    expect(result).toBe(true); // Access should be allowed
  });

  it('should redirect to the dashboard if authenticated', () => {
    authService.isAuthenticated.and.returnValue(true); // User is authenticated

    const result = guard.canActivate();

    expect(result).toBe(false); // Access should be denied
    expect(mockRouter.navigate).toHaveBeenCalledWith(['/acts-and-policies']); // Should redirect to dashboard
  });
});
