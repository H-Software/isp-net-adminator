<?php

namespace App\Controllers\Auth;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
// use App\Models\User;
use App\Controllers\Controller;
// use Respect\Validation\Validator as v;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Exception;

class AuthController extends Controller
{
	var $conn_mysql;
    var $smarty;
    var $logger;

    //  /**
    //  * @var Twig
    //  */
    // protected Twig $view;

    public function __construct(
        ContainerInterface $container,
        )
    {
        $this->container = $container;
		// $this->conn_mysql = $conn_mysql;
        // $this->smarty = $smarty;
        // $this->logger = $logger;
        $this->logger = $container->get('logger');
        $this->view = $container->get('view');

        $this->logger->info("authController\__construct called");
	}

	public function signin(ServerRequestInterface $request, ResponseInterface $response, array $args)
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

        if ($request->getMethod() == "POST") 
        {
            $username = $request->getParsedBody()['slimUsername'];
            $password = $request->getParsedBody()['slimPassword'];

            // try {
            //     if (
            //         !Sentinel::authenticate(array_clean($data, [
            //             'email',
            //             'password',
            //         ]), isset($data['persist']))
            //     ) {
            //         throw new AuthException('Incorrect email or password.');
            //     }
            // } catch (Exception $e) {
            //     $this->flash->addMessage('status', $e->getMessage());
            //     $this->logger->error($e->getMessage(), array_clean($data, ['email', 'persist', 'csrf_name', 'csrf_value']));
    
            //     return $response->withHeader('Location', $this->routeParser->urlFor('auth.signin'));
            // }


            // $result = $this->container->authenticator->authenticate($username, $password);
    
            // if ($result->isValid()) {
            //     $url = $this->router->pathFor('home');
            //     return $response->withStatus(302)->withHeader('Location', $url);
            // } else {
            //     $messages = $result->getMessages();
            //     $message = $messages[0]; //message to presentation layer
            //     $this->container->flash->addMessage('error', $messages[0]);
            //     foreach ($messages as $i => $msg) {
            //             $messages[$i] = str_replace("\n", "\n  ", $msg);
            //     }
            //     $this->logger->warning("Authentication failure for $username .", $messages);
            //     $this->logger->warning("Authentication failure error: ".var_export($messages[0], true));
    
            // }
        }

        return $this->view->render($response, 'auth\signin.twig', array('username' => @$username, "message" => $message));
	}

	public function signout($request, $response, array $args)
	{
        $this->logger->info("route/logout called");
        $this->logger->info("route/logout: dump auth->hasIdentity: ".var_export($this->container->auth->hasIdentity(), true));
        $this->logger->info("route/logout: before: dump auth->getStorage()->isEmpty(): ".var_export($this->container->auth->getStorage()->isEmpty(), true));
    
        if ($this->container->auth->hasIdentity()) {
            $this->container->auth->clearIdentity();
        }
    
        $this->logger->info("route/logout: dump auth->getStorage()->isEmpty(): ".var_export($this->container->auth->getStorage()->isEmpty(), true));
    
        //redirect:
        $url = $this->container->router->pathFor('home');
        return $response->withStatus(302)->withHeader('Location', $url);
	}

}