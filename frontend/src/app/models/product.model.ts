import { ProductAttribute } from './product-attribute.model';
import { ProductImage } from './product-image.model';

export interface Product {
  id: number;
  external_code: string;
  name: string;
  description: string;
  price: number;
  discount: number;

  attributes: ProductAttribute[];
  images: ProductImage[];
}

