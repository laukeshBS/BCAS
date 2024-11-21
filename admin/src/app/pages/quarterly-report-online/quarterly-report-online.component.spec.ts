import { ComponentFixture, TestBed } from '@angular/core/testing';

import { QuarterlyReportOnlineComponent } from './quarterly-report-online.component';

describe('QuarterlyReportOnlineComponent', () => {
  let component: QuarterlyReportOnlineComponent;
  let fixture: ComponentFixture<QuarterlyReportOnlineComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [QuarterlyReportOnlineComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(QuarterlyReportOnlineComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
