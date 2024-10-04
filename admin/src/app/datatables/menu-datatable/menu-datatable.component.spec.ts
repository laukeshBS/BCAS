import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MenuDatatableComponent } from './menu-datatable.component';

describe('MenuDatatableComponent', () => {
  let component: MenuDatatableComponent;
  let fixture: ComponentFixture<MenuDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [MenuDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(MenuDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
