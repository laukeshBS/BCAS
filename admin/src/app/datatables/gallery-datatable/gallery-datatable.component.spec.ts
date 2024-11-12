import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GalleryDatatableComponent } from './gallery-datatable.component';

describe('GalleryDatatableComponent', () => {
  let component: GalleryDatatableComponent;
  let fixture: ComponentFixture<GalleryDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [GalleryDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(GalleryDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
