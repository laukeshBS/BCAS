import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CommonTitleComponent } from './common-title.component';

describe('CommonTitleComponent', () => {
  let component: CommonTitleComponent;
  let fixture: ComponentFixture<CommonTitleComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CommonTitleComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CommonTitleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
