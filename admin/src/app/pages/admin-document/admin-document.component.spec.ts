import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AdminDocumentComponent } from './admin-document.component';

describe('AdminDocumentComponent', () => {
  let component: AdminDocumentComponent;
  let fixture: ComponentFixture<AdminDocumentComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AdminDocumentComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AdminDocumentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
