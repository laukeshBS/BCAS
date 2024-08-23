import { ComponentFixture, TestBed } from '@angular/core/testing';
import { CircularsdatatableComponent } from './circularsdatatable.component';

describe('CircularsdatatableComponent', () => {
  let component: CircularsdatatableComponent;
  let fixture: ComponentFixture<CircularsdatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CircularsdatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CircularsdatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
