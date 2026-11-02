import { createReducer, on } from '@ngrx/store';
import {
  loadProducts,
  loadProductsFailure,
  loadProductsSuccess, setProductFilters,
} from './product.actions';
import { ProductFilters, ProductPage } from '../../services/product/product.service';

export interface ProductState {
  items: ProductPage['items'];
  loading: boolean;
  error: string | null;
  totalPages: number;
  currentPage: number;
  filters: ProductFilters;
}

export const initialProductState: ProductState = {
  items: [],
  loading: false,
  error: null,
  totalPages: 0,
  currentPage: 1,
  filters: {}
};

export const productReducers = createReducer(
  initialProductState,

  on(loadProducts, (state, { page }) => ({
    ...state,
    currentPage: page,
    loading: true,
    error: null,
  })),

  on(loadProductsSuccess, (state, { result }) => ({
    ...state,
    items: result.items,
    totalPages: result.pagination.pages,
    currentPage: result.pagination.page,
    loading: false,
  })),

  on(loadProductsFailure, (state, { error }) => ({
    ...state,
    loading: false,
    error,
  })),

  on(setProductFilters, (state, { filters }) => ({
    ...state,
    filters: filters,
  })),
);
