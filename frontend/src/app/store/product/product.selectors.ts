import { createFeatureSelector, createSelector } from '@ngrx/store';
import { ProductState } from './product.reducers';

export const selectProductState =
  createFeatureSelector<ProductState>('products');

export const selectAllProducts = createSelector(
  selectProductState,
  (state) => state.items,
);

export const selectProductsLoading = createSelector(
  selectProductState,
  (state) => state.loading,
);

export const selectTotalPages = createSelector(
  selectProductState,
  (state) => state.totalPages,
);

export const selectCurrentPage = createSelector(
  selectProductState,
  (state) => state.currentPage,
);

export const selectProductFilters = createSelector(
  selectProductState,
  (state) => state.filters,
)

export const selectProductsError = createSelector(
  selectProductState,
  (state) => state.error,
);
