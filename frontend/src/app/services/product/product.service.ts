import { inject, Service } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map, Observable } from 'rxjs';
import { Product } from '../../models/product.model';

export interface Pagination {
  page: number;
  per_page: number;
  total: number;
  pages: number;
}

interface ProductResponse {
  data: {
    items: Product[];
    pagination: Pagination;
  };
}

interface ProductDetailResponse {
  data: Product;
}

export interface ProductPage {
  items: Product[];
  pagination: Pagination;
}

export interface ProductFilters {
  name?: string;
  minPrice?: number | null;
  maxPrice?: number | null;
}

@Service()
export class ProductService {
  private http = inject(HttpClient);
  private apiUrl = 'http://localhost/api/products';

  getProducts(
    page = 1,
    perPage = 20,
    filters: ProductFilters = {},
  ): Observable<ProductPage> {
    const params: Record<string, string | number> = {
      page,
      per_page: perPage,
    };

    const name = filters.name?.trim();
    if (name) {
      params['name'] = name;
    }
    if (filters.minPrice != null) {
      params['min_price'] = filters.minPrice;
    }
    if (filters.maxPrice != null) {
      params['max_price'] = filters.maxPrice;
    }

    return this.http
      .get<ProductResponse>(this.apiUrl, { params })
      .pipe(map(response => response.data));
  }

  getProductById(id: number): Observable<Product> {
    return this.http
      .get<ProductDetailResponse>(`${this.apiUrl}/${id}`)
      .pipe(map(response => response.data));
  }
}
