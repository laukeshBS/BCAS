import { ComponentFixture, TestBed } from '@angular/core/testing';

import { NoticesdatatableComponent } from './noticesdatatable.component';

describe('NoticesdatatableComponent', () => {
  let component: NoticesdatatableComponent;
  let fixture: ComponentFixture<NoticesdatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [NoticesdatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(NoticesdatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
