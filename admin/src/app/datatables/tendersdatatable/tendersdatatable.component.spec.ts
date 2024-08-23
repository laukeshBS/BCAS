import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TendersdatatableComponent } from './tendersdatatable.component';

describe('TendersdatatableComponent', () => {
  let component: TendersdatatableComponent;
  let fixture: ComponentFixture<TendersdatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TendersdatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(TendersdatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
