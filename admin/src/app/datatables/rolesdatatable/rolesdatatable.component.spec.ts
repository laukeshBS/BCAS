import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RolesdatatableComponent } from './rolesdatatable.component';

describe('RolesdatatableComponent', () => {
  let component: RolesdatatableComponent;
  let fixture: ComponentFixture<RolesdatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RolesdatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(RolesdatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
