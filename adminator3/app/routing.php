<?php

// routes

// use App\Middleware\GuestMiddleware;
// use App\Middleware\AuthMiddleware;
use \Slim\Http\Request as SlimHttpRequest;
use \Slim\Http\Response as SlimHttpResponse;

$app->group('/auth', function() {
	$this->get('/notAuthenticated', function (SlimHttpRequest $request, SlimHttpResponse $response, $args) {
        global $app;
// 		return $response
// 		->withStatus(401)
// 		->withHeader('Content-Type', 'text/html;charset=utf-8')
// 		->write('You are not authenticated.');
        $route = $app->getContainer()->get('router')->getNamedRoute('login');
		//redirect:
		// $route = \Slim\App::object()->getContainer()->get('router')->getNamedRoute('login');
		$route->setArgument("message" , "You are not authenticated" );
		$route->run($request, $response );
	})->setName("notAuthenticated")->allow([Acl::GUEST]);
	
	// Route::get('/notAuthorized', function (SlimHttpRequest $request, SlimHttpResponse $response, $args) {
	// 	return $response
	// 	->withStatus(403)
	// 	->withHeader('Content-Type', 'text/html;charset=utf-8')
	// 	->write('You are not authorized to this resource.');
	// })->setName("notAuthorized");
});

$app->map(['GET','POST'], '/login', function (SlimHttpRequest $request, SlimHttpResponse $response, $args) {
    $username = null;
    global $app;
    /*
     * require: slim/flash
     * don't know if slim/flash is not stable or I'm a fool
     */
//     $app->getContainer()["flash"]->addMessage('error', 'testando novo');
//     var_dump( $app->getContainer()["flash"]->storage["slimFlash"]["error"] );
//     var_dump( $app->getContainer()["flash"]->getMessages()["error"] );
	$message = array_key_exists("message", $args) ? $args["message"] : null;
    if ($request->isPost()) {
        $username = $request->getParsedBody()['slimUsername'];
        $password = $request->getParsedBody()['slimPassword']; //(new PasswordValidator())->rehash($request->getParsedBody()['slimPassword']);
        $result = $app->getContainer()["authenticator"]->authenticate($username, $password);

        if ($result->isValid()) {
   			return $app->getContainer()->view->render($response, 'home.html');
        } else {
        	$messages = $result->getMessages();
            $message = $messages[0]; //message to presentation layer
//             $app->getContainer()["flash"]->addMessage('error', $messages[0]);
			$logger = $app->getContainer()["logger"];
        	foreach ($messages as $i => $msg) {
					$messages[$i] = str_replace("\n", "\n  ", $msg);
			}
			
			$logger->addWarning("Authentication failure for $username .", $messages);
            
        }
    }
    return $app->getContainer()->view->render($response, 'auth\signin.twig', array('username' => @$username, "message" => $message));
})->setName('login');

$app->group('', function () {
	$this->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup')->allow([Acl::GUEST]);
	$this->post('/auth/signup', 'AuthController:postSignUp')->allow([Acl::GUEST]);
	$this->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin')->allow([Acl::GUEST]);
	$this->post('/auth/signin', 'AuthController:postSignIn')->allow([Acl::GUEST]);
});

$app->group('', function () {
    // $this->get('/', function ($req, $res, $args) {
    //     return $res->withStatus(302)->withHeader('Location', '/home');
    // });
    // $this->get('/', HomeController::class . ':index')->setName('home');
	$this->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout')->allow([Acl::GUEST]);
	$this->get('/auth/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change')->allow([Acl::GUEST]);
	$this->post('/auth/password/change', 'PasswordController:postChangePassword')->allow([Acl::GUEST]);
});

$app->group('', function () {
    $this->map(['GET', 'POST'],'/home', HomeController::class . ':home')->setName('home')->allow([Acl::MEMBER]);

    // $this->map(['GET', 'POST'],'/about', \aboutController::class . ':about');
    // $this->map(['GET', 'POST'], '/about/changes-old', \aboutController::class . ':changesOld');
    // $this->map(['GET', 'POST'], '/about/changes', \aboutController::class . ':changes');
    
    // $this->map(['GET', 'POST'],'/admin', \adminController::class . ':admin');
    // $this->map(['GET', 'POST'],'/admin/admin', \adminController::class . ':adminMain');
    // $this->map(['GET', 'POST'],'/admin/level-list', \adminController::class . ':adminLevelList');
    // $this->map(['GET', 'POST'],'/admin/level-list/json', \adminController::class . ':adminLevelListJson');
    // $this->map(['GET', 'POST'],'/admin/level-action', \adminController::class . ':adminLevelAction');

    // $this->map(['GET', 'POST'],'/admin/tarify', \adminController::class . ':adminTarify');

    // $this->map(['GET', 'POST'],'/archiv-zmen', \archivZmenController::class . ':archivZmenList');
    // $this->map(['GET', 'POST'],'/archiv-zmen/cat', \archivZmenController::class . ':archivZmenCat');
    // $this->map(['GET', 'POST'],'/archiv-zmen/work', \archivZmenController::class . ':archivZmenWork');
    // $this->map(['GET', 'POST'],'/archiv-zmen/ucetni', \archivZmenController::class . ':archivZmenUcetni');

    // $this->map(['GET', 'POST'],'/others', \othersController::class . ':others');
    // $this->map(['GET', 'POST'],'/others/board', \othersController::class . ':board');

    // $this->map(['GET', 'POST'],'/objekty/cat', \objektyController::class . ':cat');
    // $this->map(['GET', 'POST'],'/objekty/stb', \objektyController::class . ':stb');
    // $this->map(['GET', 'POST'],'/objekty/stb/action', \objektyController::class . ':stbAction');

    // $this->map(['GET', 'POST'],'/platby/cat', \platbyController::class . ':cat');
    // $this->map(['GET', 'POST'],'/platby/fn', \platbyController::class . ':fn');
    // $this->map(['GET', 'POST'],'/platby/fn-kontrola-omezeni', \platbyController::class . ':fnKontrolaOmezeni');

    // $this->map(['GET', 'POST'],'/vlastnici/cat', \vlastniciController::class . ':cat');
    // $this->map(['GET', 'POST'],'/vlastnici2', \vlastniciController::class . ':vlastnici2');

    // $this->map(['GET', 'POST'],'/topology', \topologyController::class . ':nodeList');
    // $this->map(['GET', 'POST'],'/topology/node-list', \topologyController::class . ':nodeList');
    // $this->map(['GET', 'POST'],'/topology/router-list', \topologyController::class . ':routerList');

    // $this->map(['GET', 'POST'],'/work', \workController::class . ':work');

});

// $app->map(['GET'],'/others/img/{name}', function ($request, $response, array $args) {
//     $name = $args['name'];
//     $response = $response->withStatus(301);
//     return $response->withHeader('Location', "/img2/" . $name);
// });
