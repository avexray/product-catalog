import { inject, Injectable } from '@angular/core';
import { Actions, createEffect, ofType } from '@ngrx/effects';
import { concatLatestFrom } from '@ngrx/operators';
import { Store } from '@ngrx/store';
import { catchError, map, of, switchMap } from 'rxjs';
import { ProductService } from '../../services/product/product.service';
import { selectProductFilters } from './product.selectors';
import {
  loadProducts,
  loadProductsFailure,
  loadProductsSuccess,
} from './product.actions';

@Injectable()
export class ProductEffects {
  private actions$ = inject(Actions);
  private productService = inject(ProductService);
  private store = inject(Store);

  loadProducts$ = createEffect(() =>
    this.actions$.pipe(
      ofType(loadProducts),
      concatLatestFrom(() => this.store.select(selectProductFilters)),
      switchMap(([{ page }, filters]) =>
        this.productService.getProducts(page, 20, filters).pipe(
          map((result) => loadProductsSuccess({ result })),
          catchError((error: unknown) =>
            of(
              loadProductsFailure({
                error:
                  error instanceof Error
                    ? error.message
                    : 'Не удалось загрузить товары',
              }),
            ),
          ),
        ),
      ),
    ),
  );
}
