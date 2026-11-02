<?php

declare(strict_types=1);

use App\Application\Actions\Import\ImportSheetAction;
use App\Application\Actions\Import\ViewImportAction;
use App\Application\Actions\Product\ListProductsAction;
use App\Application\Actions\Product\ViewProductAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->group('/api', function (Group $group) {
        $group->options('/{routes:.*}', function (Request $request, Response $response) {
            return $response;
        });

        $group->group('/products', function (Group $group) {
            $group->get('', ListProductsAction::class);
            $group->post('/import', ImportSheetAction::class);
            $group->get('/status', ViewImportAction::class);
            $group->get('/{id}', ViewProductAction::class);
        });
    });
};
