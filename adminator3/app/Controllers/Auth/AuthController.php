<?php

namespace App\Controllers\Auth;

use Psr\Container\ContainerInterface;
// use App\Models\User;
use App\Controllers\Controller;
// use Respect\Validation\Validator as v;

class AuthController extends Controller
{
	var $conn_mysql;
    var $smarty;
    var $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
		// $this->conn_mysql = $conn_mysql;
        // $this->smarty = $smarty;
        // $this->logger = $logger;
        $this->logger = $container->logger;

        $this->logger->addInfo("authController\__construct called");
	}

	public function signin($request, $response, array $args)
	{
        $username = null;
        // global $app;
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
            $password = $request->getParsedBody()['slimPassword'];
            $result = $this->container->authenticator->authenticate($username, $password);
    
            if ($result->isValid()) {
                $url = $this->router->pathFor('home');
                return $response->withStatus(302)->withHeader('Location', $url);
            } else {
                $messages = $result->getMessages();
                $message = $messages[0]; //message to presentation layer
                $this->container->flash->addMessage('error', $messages[0]);
                foreach ($messages as $i => $msg) {
                        $messages[$i] = str_replace("\n", "\n  ", $msg);
                }
                $this->logger->addWarning("Authentication failure for $username .", $messages);
                $this->logger->addWarning("Authentication failure error: ".var_export($messages[0], true));
    
            }
        }
        return $this->container->view->render($response, 'auth\signin.twig', array('username' => @$username, "message" => $message));
	}

	public function signout($request, $response, array $args)
	{
        $this->logger->addInfo("route /logout called");
        $this->logger->addInfo("route /logout: dump auth->hasIdentity: ".var_export($this->container->auth->hasIdentity(), true));
        $this->logger->addInfo("route /logout: before: dump auth->getStorage()->isEmpty(): ".var_export($this->container->auth->getStorage()->isEmpty(), true));
    
        if ($this->container->auth->hasIdentity()) {
            $this->container->auth->clearIdentity();
        }
    
        $this->logger->addInfo("route /logout: dump auth->getStorage()->isEmpty(): ".var_export($this->container->auth->getStorage()->isEmpty(), true));
    
        //redirect:
        $url = $this->container->router->pathFor('home');
        return $response->withStatus(302)->withHeader('Location', $url);
	}

	// public function getSignOut($request, $response)
	// {
	// 	$this->auth->logout();
	// 	return $response->withRedirect($this->router->pathFor('home'));
	// }

	// public function getSignIn($request, $response)
	// {
	// 	return $this->view->render($response, 'auth/signin.twig');
	// }

	// public function postSignIn($request, $response)
	// {
	// 	$auth = $this->auth->attempt(
	// 		$request->getParam('email'),
	// 		$request->getParam('password')
	// 	);

	// 	if (! $auth) {
	// 		$this->flash->addMessage('error', 'Could not sign you in with those details');
	// 		return $response->withRedirect($this->router->pathFor('auth.signin'));
	// 	}

	// 	return $response->withRedirect($this->router->pathFor('home'));
	// }

	// public function getSignUp($request, $response)
	// {
	// 	return $this->view->render($response, 'auth/signup.twig');
	// }

	// public function postSignUp($request, $response)
	// {

	// 	$validation = $this->validator->validate($request, [
	// 		'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
	// 		'name' => v::noWhitespace()->notEmpty()->alpha(),
	// 		'password' => v::noWhitespace()->notEmpty(),
	// 	]);

	// 	if ($validation->failed()) {
	// 		return $response->withRedirect($this->router->pathFor('auth.signup'));
	// 	}

	// 	$user = User::create([
	// 		'email' => $request->getParam('email'),
	// 		'name' => $request->getParam('name'),
	// 		'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
	// 	]);

	// 	$this->flash->addMessage('info', 'You have been signed up');

	// 	$this->auth->attempt($user->email,$request->getParam('password'));

	// 	return $response->withRedirect($this->router->pathFor('home'));
	// }
}