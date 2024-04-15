<?php

// routes
use App\Middleware\GuestMiddleware;
use App\Middleware\AuthMiddleware;

$app->group('', function () {
	$this->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');
	$this->post('/auth/signup', 'AuthController:postSignUp');
	$this->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
	$this->post('/auth/signin', 'AuthController:postSignIn');
})->add(new GuestMiddleware($container));

$app->group('', function () {
    // $this->get('/', function ($req, $res, $args) {
    //     return $res->withStatus(302)->withHeader('Location', '/home');
    // });
    // $this->get('/', HomeController::class . ':index')->setName('home');
	$this->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout');
	$this->get('/auth/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
	$this->post('/auth/password/change', 'PasswordController:postChangePassword');
})->add(new AuthMiddleware($container));

$app->group('', function () {
    $this->map(['GET', 'POST'],'/home', HomeController::class . ':home')->setName('home');;

    $this->map(['GET', 'POST'],'/about', \aboutController::class . ':about');
    $this->map(['GET', 'POST'], '/about/changes-old', \aboutController::class . ':changesOld');
    $this->map(['GET', 'POST'], '/about/changes', \aboutController::class . ':changes');
    
    $this->map(['GET', 'POST'],'/admin', \adminController::class . ':admin');
    $this->map(['GET', 'POST'],'/admin/admin', \adminController::class . ':adminMain');
    $this->map(['GET', 'POST'],'/admin/level-list', \adminController::class . ':adminLevelList');
    $this->map(['GET', 'POST'],'/admin/level-list/json', \adminController::class . ':adminLevelListJson');
    $this->map(['GET', 'POST'],'/admin/level-action', \adminController::class . ':adminLevelAction');

    $this->map(['GET', 'POST'],'/admin/tarify', \adminController::class . ':adminTarify');

    $this->map(['GET', 'POST'],'/archiv-zmen/cat', \archivZmenController::class . ':archivZmenCat');
    $this->map(['GET', 'POST'],'/archiv-zmen/work', \archivZmenController::class . ':archivZmenWork');

    $this->map(['GET', 'POST'],'/archiv-zmen/ucetni', \archivZmenController::class . ':archivZmenUcetni');

    $this->map(['GET', 'POST'],'/others', \othersController::class . ':others');
    $this->map(['GET', 'POST'],'/others/board', \othersController::class . ':board');

    $this->map(['GET', 'POST'],'/vlastnici/cat', \vlastniciController::class . ':cat');
    $this->map(['GET', 'POST'],'/vlastnici2', \vlastniciController::class . ':vlastnici2');

    $this->map(['GET', 'POST'],'/objekty/cat', \objektyController::class . ':cat');
    $this->map(['GET', 'POST'],'/objekty/stb', \objektyController::class . ':stb');
    $this->map(['GET', 'POST'],'/objekty/stb/action', \objektyController::class . ':stbAction');

    $this->map(['GET', 'POST'],'/platby/cat', \platbyController::class . ':cat');
    $this->map(['GET', 'POST'],'/platby/fn', \platbyController::class . ':fn');
    $this->map(['GET', 'POST'],'/platby/fn-kontrola-omezeni', \platbyController::class . ':fnKontrolaOmezeni');

    $this->map(['GET', 'POST'],'/work', \workController::class . ':work');

})->add(new AuthMiddleware($container));

// $app->map(['GET'],'/others/img/{name}', function ($request, $response, array $args) {
//     $name = $args['name'];
//     $response = $response->withStatus(301);
//     return $response->withHeader('Location', "/img2/" . $name);
// });
