import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OrganizationStructureDatatableComponent } from './organization-structure-datatable.component';

describe('OrganizationStructureDatatableComponent', () => {
  let component: OrganizationStructureDatatableComponent;
  let fixture: ComponentFixture<OrganizationStructureDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [OrganizationStructureDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OrganizationStructureDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
