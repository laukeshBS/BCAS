import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OpsSecurityComponent } from './ops-security.component';

describe('OpsSecurityComponent', () => {
  let component: OpsSecurityComponent;
  let fixture: ComponentFixture<OpsSecurityComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [OpsSecurityComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OpsSecurityComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
