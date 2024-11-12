import { TestBed } from '@angular/core/testing';

import { OpsSecurityService } from './ops-security.service';

describe('OpsSecurityService', () => {
  let service: OpsSecurityService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(OpsSecurityService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
