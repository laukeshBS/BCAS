import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EventsdatatableComponent } from './eventsdatatable.component';

describe('EventsdatatableComponent', () => {
  let component: EventsdatatableComponent;
  let fixture: ComponentFixture<EventsdatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [EventsdatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(EventsdatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
