import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ActsAndPoliciesComponent } from './acts-and-policies.component';

describe('ActsAndPoliciesComponent', () => {
  let component: ActsAndPoliciesComponent;
  let fixture: ComponentFixture<ActsAndPoliciesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ActsAndPoliciesComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ActsAndPoliciesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
