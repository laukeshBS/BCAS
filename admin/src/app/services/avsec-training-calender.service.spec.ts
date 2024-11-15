import { TestBed } from '@angular/core/testing';

import { AvsecTrainingCalenderService } from './avsec-training-calender.service';

describe('AvsecTrainingCalenderService', () => {
  let service: AvsecTrainingCalenderService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(AvsecTrainingCalenderService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
