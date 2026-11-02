import { Component, inject, signal } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ProductService } from '../../services/product/product.service';
import { map, switchMap, tap } from 'rxjs';
import { AsyncPipe, CurrencyPipe } from '@angular/common';

@Component({
  imports: [AsyncPipe, CurrencyPipe],
  selector: 'app-product-details',
  styleUrl: './product-details.css',
  templateUrl: './product-details.html',
})
export class ProductDetails {
  private readonly route = inject(ActivatedRoute);
  private readonly productService = inject(ProductService);

  readonly selectedImage = signal<string | null>(null);

  readonly product$ = this.route.paramMap.pipe(
    map(params => Number(params.get('id'))),
    switchMap(id => this.productService.getProductById(id)),
    tap(product => this.selectedImage.set(product.images[0]?.url ?? null)),
  );

  selectImage(url: string): void {
    this.selectedImage.set(url);
  }
}
