import { TestBed } from '@angular/core/testing';

import { CommonTitleService } from './common-title.service';

describe('CommonTitleService', () => {
  let service: CommonTitleService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(CommonTitleService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
