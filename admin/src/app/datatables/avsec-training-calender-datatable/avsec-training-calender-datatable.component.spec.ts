import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AvsecTrainingCalenderDatatableComponent } from './avsec-training-calender-datatable.component';

describe('AvsecTrainingCalenderDatatableComponent', () => {
  let component: AvsecTrainingCalenderDatatableComponent;
  let fixture: ComponentFixture<AvsecTrainingCalenderDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AvsecTrainingCalenderDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AvsecTrainingCalenderDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
