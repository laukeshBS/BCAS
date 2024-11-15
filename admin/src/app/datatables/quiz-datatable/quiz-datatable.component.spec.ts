import { ComponentFixture, TestBed } from '@angular/core/testing';

import { QuizDatatableComponent } from './quiz-datatable.component';

describe('QuizDatatableComponent', () => {
  let component: QuizDatatableComponent;
  let fixture: ComponentFixture<QuizDatatableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [QuizDatatableComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(QuizDatatableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
