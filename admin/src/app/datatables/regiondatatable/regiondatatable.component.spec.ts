import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RegiondatatableComponent } from './regiondatatable.component';

describe('RegiondatatableComponent', () => {
  let component: RegiondatatableComponent;
  let fixture: ComponentFixture<RegiondatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RegiondatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(RegiondatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
