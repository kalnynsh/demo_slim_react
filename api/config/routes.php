<?php

declare(strict_types=1);

use App\Http\Action;
use App\Router\StaticRouterGroup as Group;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app): void {
    $app->get('/', Action\HomeAction::class);

    $app->map(['GET', 'POST'], '/authorize', Action\AuthorizeAction::class);
    $app->post('/token', Action\TokenAction::class);

    $app->group('/v1', new Group(static function (RouteCollectorProxy $group): void {
        $group->group('/auth', new Group(static function (RouteCollectorProxy $group): void {
            $group->post('/join', Action\V1\Auth\Join\RequestAction::class);
            $group->post('/join/confirm', Action\V1\Auth\Join\ConfirmationAction::class);
        }));
    }));
};
