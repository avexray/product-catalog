import { Component, inject, output } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { setProductFilters } from '../../store/product/product.actions';
import { Store } from '@ngrx/store';

@Component({
  imports: [ReactiveFormsModule],
  selector: 'app-product-list-form',
  styleUrl: './product-list-form.css',
  templateUrl: './product-list-form.html',
})
export class ProductListForm {
  private store = inject(Store);

  submit = output<void>();

  filterForm = new FormGroup({
    name: new FormControl('', { nonNullable: true }),
    minPrice: new FormControl<number | null>(null),
    maxPrice: new FormControl<number | null>(null),
  });

  onSubmit(): void {
    if (this.filterForm.valid) {
      this.store.dispatch(setProductFilters({
        filters: this.filterForm.getRawValue()
      }));

      this.submit.emit();
    }
  }
}
