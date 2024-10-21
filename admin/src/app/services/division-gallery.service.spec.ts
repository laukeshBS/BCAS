import { TestBed } from '@angular/core/testing';

import { DivisionGalleryService } from './division-gallery.service';

describe('DivisionGalleryService', () => {
  let service: DivisionGalleryService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(DivisionGalleryService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
