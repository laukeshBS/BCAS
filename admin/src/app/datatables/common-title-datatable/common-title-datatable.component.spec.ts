import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CommonTitleDatatableComponent } from './common-title-datatable.component';

describe('CommonTitleDatatableComponent', () => {
  let component: CommonTitleDatatableComponent;
  let fixture: ComponentFixture<CommonTitleDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CommonTitleDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CommonTitleDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
