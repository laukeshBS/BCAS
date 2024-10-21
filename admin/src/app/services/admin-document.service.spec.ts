import { TestBed } from '@angular/core/testing';

import { AdminDocumentService } from './admin-document.service';

describe('AdminDocumentService', () => {
  let service: AdminDocumentService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(AdminDocumentService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
