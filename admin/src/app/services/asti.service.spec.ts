import { TestBed } from '@angular/core/testing';

import { AstiService } from './asti.service';

describe('AstiService', () => {
  let service: AstiService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(AstiService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
