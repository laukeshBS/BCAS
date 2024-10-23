import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ContactDatatableComponent } from './contact-datatable.component';

describe('ContactDatatableComponent', () => {
  let component: ContactDatatableComponent;
  let fixture: ComponentFixture<ContactDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ContactDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ContactDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
