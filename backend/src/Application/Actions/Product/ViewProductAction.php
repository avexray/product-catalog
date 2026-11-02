<?php

declare(strict_types=1);

namespace App\Application\Actions\Product;

use App\Domain\Product\ProductAttribute;
use App\Domain\Product\ProductImage;
use Psr\Http\Message\ResponseInterface as Response;

class ViewProductAction extends ProductAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $productId = (int) $this->resolveArg('id');
        $product = $this->productRepository->findProductOfId($productId);

        $this->logger->info("Product of id `{$productId}` was viewed.");

        return $this->respondWithData([
            'id' => $product->getId(),
            'externalCode' => $product->getExternalCode(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'discount' => $product->getDiscount(),

            'attributes' => array_map(
                static fn (ProductAttribute $attribute) => [
                    'key' => $attribute->getKey(),
                    'value' => $attribute->getValue(),
                ],
                $product->getAttributes()->toArray(),
            ),

            'images' => array_map(
                static fn (ProductImage $image) => [
                    'url' => $image->getPath(),
                ],
                $product->getImages()->toArray(),
            ),
        ]);
    }
}
