import { TestBed } from '@angular/core/testing';

import { QuarterlyReportOnlineService } from './quarterly-report-online.service';

describe('QuarterlyReportOnlineService', () => {
  let service: QuarterlyReportOnlineService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(QuarterlyReportOnlineService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
