import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AstiDatatableComponent } from './asti-datatable.component';

describe('AstiDatatableComponent', () => {
  let component: AstiDatatableComponent;
  let fixture: ComponentFixture<AstiDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AstiDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AstiDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
