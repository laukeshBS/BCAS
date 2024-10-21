import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AdminDocumentCategoryDatatableComponent } from './admin-document-category-datatable.component';

describe('AdminDocumentCategoryDatatableComponent', () => {
  let component: AdminDocumentCategoryDatatableComponent;
  let fixture: ComponentFixture<AdminDocumentCategoryDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AdminDocumentCategoryDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AdminDocumentCategoryDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
