<?php

namespace App\Controllers\Auth;

use Psr\Container\ContainerInterface;
use App\Controllers\Controller;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;

// https://github.com/HavenShen/slim-born/blob/master/app/Controllers/Auth/PasswordController.php

class PasswordController extends Controller
{

    var $logger;

    /**
     * @var RouteParserInterface
     */
    protected RouteParserInterface $routeParser;

     /**
     * @var Twig
     */
    protected Twig $view;

    public function __construct(
        ContainerInterface $container,
        RouteParserInterface $routeParser,
        )
    {
        $this->container = $container;
        $this->routeParser = $routeParser;

        $this->logger = $container->get('logger');
        $this->view = $container->get('view');

        $this->logger->info("PasswordController\__construct called");
	}

	public function getChangePassword($request, $response)
	{
		return $this->view->render($response, 'auth/password/change.twig');
	}

	public function postChangePassword($request, $response)
	{
        $logger = $this->container->get('logger');
        $logger->info("PasswordController\postChangePassword called");

        $requestData = $request->getParsedBody();

        $passwordHelper = new \App\Auth\passwordHelper($this->container, $requestData);
        $rs = $passwordHelper->changePassword();

        if($rs === false){
            $this->flash->addMessage('error', $passwordHelper->errorMessage);
            return $response->withHeader('Location', $this->router->urlFor('auth.password.change'));
        }
        else{
            $this->flash->addMessage('info', 'Your password was changed');
            return $response->withHeader('Location', $this->router->urlFor('home'));
        }
	}
}
