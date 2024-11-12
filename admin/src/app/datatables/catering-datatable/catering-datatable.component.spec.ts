import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CateringDatatableComponent } from './catering-datatable.component';

describe('CateringDatatableComponent', () => {
  let component: CateringDatatableComponent;
  let fixture: ComponentFixture<CateringDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CateringDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CateringDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
