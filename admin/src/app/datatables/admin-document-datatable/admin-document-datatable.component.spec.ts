import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AdminDocumentDatatableComponent } from './admin-document-datatable.component';

describe('AdminDocumentDatatableComponent', () => {
  let component: AdminDocumentDatatableComponent;
  let fixture: ComponentFixture<AdminDocumentDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AdminDocumentDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AdminDocumentDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
