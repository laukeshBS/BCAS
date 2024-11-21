import { TestBed } from '@angular/core/testing';

import { QuarterlyReportOnlineiiService } from './quarterly-report-onlineii.service';

describe('QuarterlyReportOnlineiiService', () => {
  let service: QuarterlyReportOnlineiiService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(QuarterlyReportOnlineiiService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
