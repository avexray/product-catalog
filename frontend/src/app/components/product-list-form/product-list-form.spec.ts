import { ComponentFixture, TestBed } from '@angular/core/testing';
import { ProductListForm } from './product-list-form';

describe('ProductListForm', () => {
  let component: ProductListForm;
  let fixture: ComponentFixture<ProductListForm>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ProductListForm],
    }).compileComponents();

    fixture = TestBed.createComponent(ProductListForm);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
