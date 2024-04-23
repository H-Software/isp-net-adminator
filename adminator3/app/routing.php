<?php

// routes

use \Slim\Http\Request as SlimHttpRequest;
use \Slim\Http\Response as SlimHttpResponse;

$app->group('/auth', function() use ($app) {
	$app->get('/notAuthenticated', function (SlimHttpRequest $request, SlimHttpResponse $response, $args) use ($app) {
      
        $app->getContainer()["flash"]->addMessage('info', "You are not authenticated");

        $route = $app->getContainer()->get('router')->getNamedRoute('login');
		//redirect:
		// $route = \Slim\App::object()->getContainer()->get('router')->getNamedRoute('login');
		$route->setArgument("message" , "You are not authenticated" );
		$route->run($request, $response );
	})->setName("notAuthenticated");
	
	$app->get('/notAuthorized', function (SlimHttpRequest $request, SlimHttpResponse $response, $args) {
		return $response
		->withStatus(403)
		->withHeader('Content-Type', 'text/html;charset=utf-8')
		->write('You are not authorized to this resource.');
	})->setName("notAuthorized");

    $app->map(['GET','POST'], '/signin', AuthController::class . ':signin')->setName('login');

    $app->get('/signout', AuthController::class . ':signout')->setName('logout');
});

$app->group('', function () use ($app) {
    $this->get('/', function ($req, $res, $args) {
        return $res->withStatus(302)->withHeader('Location', '/home');
    });

    // TODO: fix password/change routing
	$this->map(['GET', 'POST'], '/auth/password/change', function ($req, $res, $args) use ($app) {
        // 'PasswordController:getChangePassword'
        return $app->getContainer()['view']->render($res, 'auth\password\change.twig');
    })->setName('auth.password.change');
	// $this->post('/auth/password/change', 'PasswordController:postChangePassword');
});

$app->group('', function () {
    $this->map(['GET', 'POST'],'/home', HomeController::class . ':home')->setName('home');

    $this->map(['GET', 'POST'],'/about', \aboutController::class . ':about');
    $this->map(['GET', 'POST'], '/about/changes-old', \aboutController::class . ':changesOld');
    $this->map(['GET', 'POST'], '/about/changes', \aboutController::class . ':changes');
    
    $this->map(['GET', 'POST'],'/admin', \adminController::class . ':admin');
    $this->map(['GET', 'POST'],'/admin/admin', \adminController::class . ':adminMain');
    $this->map(['GET', 'POST'],'/admin/level-list', \adminController::class . ':adminLevelList');
    $this->map(['GET', 'POST'],'/admin/level-list/json', \adminController::class . ':adminLevelListJson');
    $this->map(['GET', 'POST'],'/admin/level-action', \adminController::class . ':adminLevelAction');

    $this->map(['GET', 'POST'],'/admin/tarify', \adminController::class . ':adminTarify');

    $this->map(['GET', 'POST'],'/archiv-zmen', \archivZmenController::class . ':archivZmenList');
    $this->map(['GET', 'POST'],'/archiv-zmen/cat', \archivZmenController::class . ':archivZmenCat');
    $this->map(['GET', 'POST'],'/archiv-zmen/work', \archivZmenController::class . ':archivZmenWork');
    $this->map(['GET', 'POST'],'/archiv-zmen/ucetni', \archivZmenController::class . ':archivZmenUcetni');

    $this->map(['GET', 'POST'],'/others', \othersController::class . ':others');
    $this->map(['GET', 'POST'],'/others/board', \othersController::class . ':board');

    $this->map(['GET', 'POST'],'/objekty/cat', \objektyController::class . ':cat');
    $this->map(['GET', 'POST'],'/objekty', \objektyController::class . ':objekty');
    $this->map(['GET', 'POST'],'/objekty/stb', \objektyController::class . ':stb');
    $this->map(['GET', 'POST'],'/objekty/stb/action', \objektyController::class . ':stbAction');

    $this->map(['GET', 'POST'],'/platby/cat', \platbyController::class . ':cat');
    $this->map(['GET', 'POST'],'/platby/fn', \platbyController::class . ':fn');
    $this->map(['GET', 'POST'],'/platby/fn-kontrola-omezeni', \platbyController::class . ':fnKontrolaOmezeni');

    $this->map(['GET', 'POST'],'/vlastnici/cat', \vlastniciController::class . ':cat');
    $this->map(['GET', 'POST'],'/vlastnici2', \vlastniciController::class . ':vlastnici2');
    $this->map(['GET', 'POST'],'/vlastnici2/fakturacni-skupiny', \vlastniciController::class . ':fakturacniSkupiny');
    $this->map(['GET', 'POST'],'/vlastnici2/fakturacni-skupiny/action', \vlastniciController::class . ':fakturacniSkupinyAction');

    $this->map(['GET', 'POST'],'/topology', \topologyController::class . ':nodeList');
    $this->map(['GET', 'POST'],'/topology/node-list', \topologyController::class . ':nodeList');
    $this->map(['GET', 'POST'],'/topology/router-list', \topologyController::class . ':routerList');

    $this->map(['GET', 'POST'],'/work', \workController::class . ':work');

});

// $app->map(['GET'],'/others/img/{name}', function ($request, $response, array $args) {
//     $name = $args['name'];
//     $response = $response->withStatus(301);
//     return $response->withHeader('Location', "/img2/" . $name);
// });
