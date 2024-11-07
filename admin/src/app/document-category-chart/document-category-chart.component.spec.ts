import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DocumentCategoryChartComponent } from './document-category-chart.component';

describe('DocumentCategoryChartComponent', () => {
  let component: DocumentCategoryChartComponent;
  let fixture: ComponentFixture<DocumentCategoryChartComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [DocumentCategoryChartComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DocumentCategoryChartComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
