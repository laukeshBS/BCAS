import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DivisionGalleryDatatableComponent } from './division-gallery-datatable.component';

describe('DivisionGalleryDatatableComponent', () => {
  let component: DivisionGalleryDatatableComponent;
  let fixture: ComponentFixture<DivisionGalleryDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [DivisionGalleryDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DivisionGalleryDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
