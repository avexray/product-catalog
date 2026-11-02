import { createAction, props } from '@ngrx/store';
import { ProductFilters, ProductPage } from '../../services/product/product.service';

export const loadProducts = createAction(
  '[Products] Load Products',
  props<{ page: number }>(),
);

export const loadProductsSuccess = createAction(
  '[Products] Load Products Success',
  props<{ result: ProductPage }>(),
);

export const loadProductsFailure = createAction(
  '[Products] Load Products Failure',
  props<{ error: string }>(),
);

export const setProductFilters = createAction(
  '[Products] Set Filters',
  props<{ filters: ProductFilters }>(),
);
