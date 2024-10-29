import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AuditDatatableComponent } from './audit-datatable.component';

describe('AuditDatatableComponent', () => {
  let component: AuditDatatableComponent;
  let fixture: ComponentFixture<AuditDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AuditDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AuditDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
