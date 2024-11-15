import { TestBed } from '@angular/core/testing';

import { OrganizationStructureService } from './organization-structure.service';

describe('OrganizationStructureService', () => {
  let service: OrganizationStructureService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(OrganizationStructureService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
