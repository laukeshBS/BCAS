import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SlideDatatableComponent } from './slide-datatable.component';

describe('SlideDatatableComponent', () => {
  let component: SlideDatatableComponent;
  let fixture: ComponentFixture<SlideDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SlideDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SlideDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
