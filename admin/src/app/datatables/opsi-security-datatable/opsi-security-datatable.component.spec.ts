import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OpsiSecurityDatatableComponent } from './opsi-security-datatable.component';

describe('OpsiSecurityDatatableComponent', () => {
  let component: OpsiSecurityDatatableComponent;
  let fixture: ComponentFixture<OpsiSecurityDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [OpsiSecurityDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OpsiSecurityDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
