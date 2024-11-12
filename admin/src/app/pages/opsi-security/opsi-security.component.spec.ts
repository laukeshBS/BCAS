import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OpsiSecurityComponent } from './opsi-security.component';

describe('OpsiSecurityComponent', () => {
  let component: OpsiSecurityComponent;
  let fixture: ComponentFixture<OpsiSecurityComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [OpsiSecurityComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OpsiSecurityComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
