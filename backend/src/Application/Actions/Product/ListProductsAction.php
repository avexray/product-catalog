<?php

declare(strict_types=1);

namespace App\Application\Actions\Product;

use App\Application\Actions\User\UserAction;
use Psr\Http\Message\ResponseInterface as Response;

class ListProductsAction extends ProductAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $query = $this->request->getQueryParams();

        $page = max(1, (int) ($query['page'] ?? 1));
        $perPage = min(100, max(1, (int) ($query['per_page'] ?? 20)));

        $name = isset($query['name']) ? trim((string) $query['name']) : null;
        $minPrice = isset($query['min_price']) ? (float) $query['min_price'] : null;
        $maxPrice = isset($query['max_price']) ? (float) $query['max_price'] : null;

        $products = $this->productRepository->findAll(
            $page,
            $perPage,
            $name,
            $minPrice,
            $maxPrice
        );

        $total = $this->productRepository->countAll(
            $name,
            $minPrice,
            $maxPrice
        );

        $this->logger->info('Product list was viewed.');

        return $this->respondWithData([
            'items' => $products,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'pages' => (int) ceil($total / $perPage),
            ],
        ]);
    }
}
