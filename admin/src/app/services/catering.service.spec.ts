import { TestBed } from '@angular/core/testing';

import { CateringService } from './catering.service';

describe('CateringService', () => {
  let service: CateringService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(CateringService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
