import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DivisiondatatableComponent } from './divisiondatatable.component';

describe('DivisiondatatableComponent', () => {
  let component: DivisiondatatableComponent;
  let fixture: ComponentFixture<DivisiondatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [DivisiondatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DivisiondatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
