import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DivisionGalleryComponent } from './division-gallery.component';

describe('DivisionGalleryComponent', () => {
  let component: DivisionGalleryComponent;
  let fixture: ComponentFixture<DivisionGalleryComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [DivisionGalleryComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DivisionGalleryComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
