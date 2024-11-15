import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AstiComponent } from './asti.component';

describe('AstiComponent', () => {
  let component: AstiComponent;
  let fixture: ComponentFixture<AstiComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AstiComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AstiComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
