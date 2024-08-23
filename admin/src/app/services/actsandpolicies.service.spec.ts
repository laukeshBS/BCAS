import { TestBed } from '@angular/core/testing';

import { ActsandpoliciesService } from './actsandpolicies.service';

describe('ActsandpoliciesService', () => {
  let service: ActsandpoliciesService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(ActsandpoliciesService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
