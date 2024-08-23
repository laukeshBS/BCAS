import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VacanciesdatatableComponent } from './vacanciesdatatable.component';

describe('VacanciesdatatableComponent', () => {
  let component: VacanciesdatatableComponent;
  let fixture: ComponentFixture<VacanciesdatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [VacanciesdatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(VacanciesdatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
