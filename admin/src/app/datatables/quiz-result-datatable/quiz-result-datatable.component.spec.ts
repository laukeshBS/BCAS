import { ComponentFixture, TestBed } from '@angular/core/testing';

import { QuizResultDatatableComponent } from './quiz-result-datatable.component';

describe('QuizResultDatatableComponent', () => {
  let component: QuizResultDatatableComponent;
  let fixture: ComponentFixture<QuizResultDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [QuizResultDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(QuizResultDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
