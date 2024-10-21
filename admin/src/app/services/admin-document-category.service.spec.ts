import { TestBed } from '@angular/core/testing';

import { AdminDocumentCategoryService } from './admin-document-category.service';

describe('AdminDocumentCategoryService', () => {
  let service: AdminDocumentCategoryService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(AdminDocumentCategoryService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
