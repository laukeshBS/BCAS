import { TestBed } from '@angular/core/testing';

import { OpsiSecurityService } from './opsi-security.service';

describe('OpsiSecurityService', () => {
  let service: OpsiSecurityService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(OpsiSecurityService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
