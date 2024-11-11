import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AirlinesDatatableComponent } from './airlines-datatable.component';

describe('AirlinesDatatableComponent', () => {
  let component: AirlinesDatatableComponent;
  let fixture: ComponentFixture<AirlinesDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AirlinesDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AirlinesDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
