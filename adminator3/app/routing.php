<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Routing\RouteCollectorProxy;

use App\Middleware\RedirectIfNotAuthenticated;
use App\Controllers\HomeController;
use App\Controllers\Auth\AuthController;
use App\Controllers\Auth\PasswordController;
use App\Controllers\partnerController;
use App\Controllers\platbyController;
use App\Controllers\runtimeController;
use App\Controllers\printController;
use App\Controllers\othersController;
use App\Controllers\vlastniciController;
use App\Controllers\topologyController;

// routes

$app->get(
    '/',
    function (ServerRequestInterface $request, ResponseInterface $response, $args) use ($app) {
        $routeParser = $app->getRouteCollector()->getRouteParser();
        $url = $routeParser->urlFor('home');
        return $response->withStatus(302)->withHeader('Location', $url);
    }
)->setName('index');

$app->group(
    '/auth',
    function (RouteCollectorProxy $group) {
        $group->map(['GET','POST'], '/signin', AuthController::class . ':signin')->setName('auth.signin');

        $group->get('/signout', AuthController::class . ':signout')->setName('logout');
    }
);

$app->group(
    '',
    function (RouteCollectorProxy $group) use ($app) {
        $app->get('/auth/password/change', PasswordController::class . ':getChangePassword')->setName('auth.password.change');
        $app->post('/auth/password/change', PasswordController::class . ':postChangePassword');
    }
)->add(RedirectIfNotAuthenticated::class);

// disabled middlware, because of logout of adminator2
$app->group(
    '',
    function (RouteCollectorProxy $group) {
        $group->map(['GET', 'POST'], '/vlastnici/cross', vlastniciController::class . ':cross');
    }
);

$app->group(
    '',
    function (RouteCollectorProxy $group) {
        $group->map(['GET', 'POST'], '/home', HomeController::class . ':home')->setName('home');

        $group->map(['GET'], '/runtime/opcache-gui', runtimeController::class . ':opcacheGui');

        $group->map(['GET', 'POST'], '/about', \aboutController::class . ':about');
        $group->map(['GET', 'POST'], '/about/changes-old', \aboutController::class . ':changesOld');
        $group->map(['GET', 'POST'], '/about/changes', \aboutController::class . ':changes');

        $group->map(['GET', 'POST'], '/admin', \adminController::class . ':admin');
        $group->map(['GET', 'POST'], '/admin/admin', \adminController::class . ':adminMain');
        $group->map(['GET', 'POST'], '/admin/level-list', \adminController::class . ':adminLevelList');
        $group->map(['GET', 'POST'], '/admin/level-list/json', \adminController::class . ':adminLevelListJson');
        $group->map(['GET', 'POST'], '/admin/level-action', \adminController::class . ':adminLevelAction');

        $group->map(['GET', 'POST'], '/admin/tarify', \adminController::class . ':adminTarify');
        $group->map(['GET', 'POST'], '/admin/tarify/action', \adminController::class . ':adminTarifyAction');

        $group->map(['GET', 'POST'], '/archiv-zmen', \archivZmenController::class . ':archivZmenList');
        $group->map(['GET', 'POST'], '/archiv-zmen/cat', \archivZmenController::class . ':archivZmenCat');
        $group->map(['GET', 'POST'], '/archiv-zmen/work', \archivZmenController::class . ':archivZmenWork');
        $group->map(['GET', 'POST'], '/archiv-zmen/ucetni', \archivZmenController::class . ':archivZmenUcetni');

        $group->map(['GET', 'POST'], '/others', othersController::class . ':others');
        $group->map(['GET', 'POST'], '/others/company-web', othersController::class . ':companyWeb');

        $group->map(['GET', 'POST'], '/others/board', othersController::class . ':board');
        $group->map(['GET'], '/board/rss', othersController::class . ':boardRss');

        $group->map(['GET', 'POST'], '/objekty/cat', \objektyController::class . ':cat');
        $group->map(['GET', 'POST'], '/objekty', \objektyController::class . ':objekty');
        $group->map(['GET', 'POST'], '/objekty/action', \objektyController::class . ':objektyAction');
        $group->map(['GET', 'POST'], '/objekty/stb', \objektyController::class . ':stb');
        $group->map(['GET', 'POST'], '/objekty/stb/action', \objektyController::class . ':stbAction');

        $group->map(['GET', 'POST'], '/partner', partnerController::class . ':cat');
        $group->map(['GET', 'POST'], '/partner/cat', partnerController::class . ':cat');
        $group->map(['GET', 'POST'], '/partner/order', partnerController::class . ':orderCat');
        $group->map(['GET', 'POST'], '/partner/order/cat', partnerController::class . ':orderCat');

        $group->map(['GET', 'POST'], '/partner/order/add', partnerController::class . ':orderAdd');
        $group->map(['GET', 'POST'], '/partner/order/list', partnerController::class . ':orderList');
        $group->map(['GET', 'POST'], '/partner/order/accept', partnerController::class . ':orderAccept');
        $group->map(['GET', 'POST'], '/partner/order/change-status', partnerController::class . ':orderChangeStatus');
        $group->map(['GET', 'POST'], '/partner/order/change-desc', partnerController::class . ':orderChangeDesc');

        $group->map(['GET', 'POST'], '/platby/cat', platbyController::class . ':cat');
        $group->map(['GET', 'POST'], '/platby/fn', platbyController::class . ':fn');
        $group->map(['GET', 'POST'], '/platby/fn-kontrola-omezeni', platbyController::class . ':fnKontrolaOmezeni');

        $group->map(['GET', ], '/print', printController::class . ':printListAll');
        $group->map(['GET', 'POST'], '/print/redirect', printController::class . ':printRedirect');
        $group->map(['GET', 'POST'], '/print/reg-form-2012-05', printController::class . ':printRegForm201205Old');
        $group->map(['GET', 'POST'], '/print/smlouva-2012-05', printController::class . ':printSmlouva201205');
        $group->map(['GET', 'POST'], '/print/smlouva', printController::class . ':printSmlouva');
        $group->map(['GET', 'POST'], '/print/reg-form', printController::class . ':printRegForm');

        $group->map(['GET', 'POST'], '/vlastnici/cat', vlastniciController::class . ':cat');
        $group->map(['GET', 'POST'], '/vlastnici/archiv', vlastniciController::class . ':archiv');
        $group->map(['GET', 'POST'], '/vlastnici', vlastniciController::class . ':vlastnici');

        $group->map(['GET', 'POST'], '/vlastnici2', vlastniciController::class . ':vlastnici2');
        $group->map(['GET', 'POST'], '/vlastnici2/fakturacni-skupiny', vlastniciController::class . ':fakturacniSkupiny');
        $group->map(['GET', 'POST'], '/vlastnici2/fakturacni-skupiny/action', vlastniciController::class . ':fakturacniSkupinyAction');

        $group->map(['GET', 'POST'], '/topology', topologyController::class . ':nodeList');
        $group->map(['GET', 'POST'], '/topology/node-list', topologyController::class . ':nodeList');
        $group->map(['GET', 'POST'], '/topology/router-list', topologyController::class . ':routerList');

        $group->map(['GET', 'POST'], '/work', \workController::class . ':work');

    }
)->add(RedirectIfNotAuthenticated::class);

// $app->map(['GET'],'/others/img/{name}', function ($request, $response, array $args) {
//     $name = $args['name'];
//     $response = $response->withStatus(301);
//     return $response->withHeader('Location', "/img2/" . $name);
// });
