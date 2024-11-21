import { ComponentFixture, TestBed } from '@angular/core/testing';

import { QuarterlyReportOnlineDatatableComponent } from './quarterly-report-online-datatable.component';

describe('QuarterlyReportOnlineComponent', () => {
  let component: QuarterlyReportOnlineDatatableComponent;
  let fixture: ComponentFixture<QuarterlyReportOnlineDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [QuarterlyReportOnlineDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(QuarterlyReportOnlineDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
