import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AdminDocumentCategoryComponent } from './admin-document-category.component';

describe('AdminDocumentCategoryComponent', () => {
  let component: AdminDocumentCategoryComponent;
  let fixture: ComponentFixture<AdminDocumentCategoryComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AdminDocumentCategoryComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AdminDocumentCategoryComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
