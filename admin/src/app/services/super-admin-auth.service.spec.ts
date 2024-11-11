import { TestBed } from '@angular/core/testing';

import { SuperAdminAuthService } from './super-admin-auth.service';

describe('SuperAdminAuthService', () => {
  let service: SuperAdminAuthService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(SuperAdminAuthService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
