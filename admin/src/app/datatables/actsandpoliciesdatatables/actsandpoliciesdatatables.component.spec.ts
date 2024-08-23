import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ActsandpoliciesdatatablesComponent } from './actsandpoliciesdatatables.component';

describe('ActsandpoliciesdatatablesComponent', () => {
  let component: ActsandpoliciesdatatablesComponent;
  let fixture: ComponentFixture<ActsandpoliciesdatatablesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ActsandpoliciesdatatablesComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ActsandpoliciesdatatablesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
