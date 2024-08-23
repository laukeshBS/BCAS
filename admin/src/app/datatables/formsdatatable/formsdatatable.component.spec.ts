import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormsdatatableComponent } from './formsdatatable.component';

describe('FormsdatatableComponent', () => {
  let component: FormsdatatableComponent;
  let fixture: ComponentFixture<FormsdatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormsdatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FormsdatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
