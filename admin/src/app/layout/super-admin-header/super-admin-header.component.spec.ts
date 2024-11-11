import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SuperAdminHeaderComponent } from './super-admin-header.component';

describe('SuperAdminHeaderComponent', () => {
  let component: SuperAdminHeaderComponent;
  let fixture: ComponentFixture<SuperAdminHeaderComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SuperAdminHeaderComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SuperAdminHeaderComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
