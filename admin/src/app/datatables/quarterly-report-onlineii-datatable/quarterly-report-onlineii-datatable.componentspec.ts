import { ComponentFixture, TestBed } from '@angular/core/testing';
import { QuarterlyReportOnlineiiDatatableComponent } from './quarterly-report-onlineii-datatable.component';


describe('QuarterlyReportOnlineiiComponent', () => {
  let component: QuarterlyReportOnlineiiDatatableComponent;
  let fixture: ComponentFixture<QuarterlyReportOnlineiiDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [QuarterlyReportOnlineiiDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(QuarterlyReportOnlineiiDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
