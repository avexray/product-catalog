import { Routes } from '@angular/router';
import { ProductList } from './components/product-list/product-list';
import { ImportForm } from './components/import-form/import-form';
import { ImportStatus } from './components/import-status/import-status';
import { ProductDetails } from './components/product-details/product-details';

export const routes: Routes = [
  {
    path: '',
    component: ImportForm,
  },
  {
    path: 'products',
    component: ProductList,
  },
  {
    path: 'products/:id',
    loadComponent: () => import('./components/product-details/product-details')
      .then(m => m.ProductDetails)
  },
  {
    path: 'status',
    loadComponent: () => import('./components/import-status/import-status')
      .then(m => m.ImportStatus)
  },
];
