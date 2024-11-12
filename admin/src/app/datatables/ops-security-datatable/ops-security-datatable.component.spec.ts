import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OpsSecurityDatatableComponent } from './ops-security-datatable.component';

describe('OpsSecurityDatatableComponent', () => {
  let component: OpsSecurityDatatableComponent;
  let fixture: ComponentFixture<OpsSecurityDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [OpsSecurityDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OpsSecurityDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
