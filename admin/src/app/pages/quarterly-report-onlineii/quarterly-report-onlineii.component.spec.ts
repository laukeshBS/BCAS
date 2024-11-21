import { ComponentFixture, TestBed } from '@angular/core/testing';

import { QuarterlyReportOnlineiiComponent } from './quarterly-report-onlineii.component';

describe('QuarterlyReportOnlineiiComponent', () => {
  let component: QuarterlyReportOnlineiiComponent;
  let fixture: ComponentFixture<QuarterlyReportOnlineiiComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [QuarterlyReportOnlineiiComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(QuarterlyReportOnlineiiComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
