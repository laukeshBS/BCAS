import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AvsecTrainingCalenderComponent } from './avsec-training-calender.component';

describe('AvsecTrainingCalenderComponent', () => {
  let component: AvsecTrainingCalenderComponent;
  let fixture: ComponentFixture<AvsecTrainingCalenderComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AvsecTrainingCalenderComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AvsecTrainingCalenderComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
