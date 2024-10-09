import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SliderDatatableComponent } from './slider-datatable.component';

describe('SliderDatatableComponent', () => {
  let component: SliderDatatableComponent;
  let fixture: ComponentFixture<SliderDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SliderDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SliderDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
