import { AsyncPipe } from '@angular/common';
import { Component, inject } from '@angular/core';
import { Store } from '@ngrx/store';
import { loadProducts } from '../../store/product/product.actions';
import {
  selectAllProducts,
  selectCurrentPage,
  selectProductsError,
  selectProductsLoading,
  selectTotalPages,
} from '../../store/product/product.selectors';
import { ProductListForm } from '../product-list-form/product-list-form';
import { RouterLink } from '@angular/router';


@Component({
  imports: [AsyncPipe, ProductListForm, RouterLink],
  selector: 'app-product-list',
  styleUrl: './product-list.css',
  templateUrl: './product-list.html',
})
export class ProductList {
  private store = inject(Store);

  products$ = this.store.select(selectAllProducts);
  loading$ = this.store.select(selectProductsLoading);
  totalPages$ = this.store.select(selectTotalPages);
  currentPage$ = this.store.select(selectCurrentPage);
  error$ = this.store.select(selectProductsError);

  constructor() {
    this.store.dispatch(loadProducts({ page: 1 }));
  }

  onSubmit(): void {
    this.store.dispatch(loadProducts({ page: 1 }));
  }

  goToPage(page: number, totalPages: number): void {
    if (page >= 1 && page <= totalPages) {
      this.store.dispatch(loadProducts({ page }));
    }
  }

  pageNumbers(totalPages: number): number[] {
    return Array.from({ length: totalPages }, (_, index) => index + 1);
  }
}
