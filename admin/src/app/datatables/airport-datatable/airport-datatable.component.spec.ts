import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AirportDatatableComponent } from './airport-datatable.component';

describe('AirportDatatableComponent', () => {
  let component: AirportDatatableComponent;
  let fixture: ComponentFixture<AirportDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AirportDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AirportDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
