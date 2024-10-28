import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AdminDatatableComponent } from './admin-datatable.component';

describe('AdminDatatableComponent', () => {
  let component: AdminDatatableComponent;
  let fixture: ComponentFixture<AdminDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AdminDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AdminDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
