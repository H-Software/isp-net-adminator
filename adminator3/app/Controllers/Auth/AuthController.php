<?php

namespace App\Controllers\Auth;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
// use App\Models\User;
use App\Controllers\Controller;
// use Respect\Validation\Validator as v;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Exception;
use Slim\Interfaces\RouteParserInterface;
use Slim\Flash\Messages;

class AuthController extends Controller
{
	var $conn_mysql;
    var $smarty;
    var $logger;

    /**
     * @var Messages
     */
    protected Messages $flash;

    /**
     * @var RouteParserInterface
     */
    protected RouteParserInterface $routeParser;

    //  /**
    //  * @var Twig
    //  */
    // protected Twig $view;

    public function __construct(
        ContainerInterface $container,
        Messages $flash,
        RouteParserInterface $routeParser,
        )
    {
        $this->container = $container;
        $this->routeParser = $routeParser;
        $this->flash = $container->get('flash');

        $this->logger = $container->get('logger');
        $this->view = $container->get('view');

        $this->logger->info("authController\__construct called");
	}

	public function signin(ServerRequestInterface $request, ResponseInterface $response, array $args)
	{
        if ($request->getMethod() == "POST") 
        {
            $data = $request->getParsedBody();

            try {
                if (
                    !Sentinel::authenticate($this->array_clean($data, [
                        'email',
                        'password',
                    ]), isset($data['persist']))
                ) {
                    throw new Exception('Incorrect email or password.');
                }
                else 
                {
                    $url = $this->routeParser->urlFor('home');
                    return $response->withStatus(302)->withHeader('Location', $url);
                }
            } catch (Exception $e) {
                $this->flash->addMessageNow('error', $e->getMessage());
                $this->logger->error("authController\signin " . $e->getMessage(), $this->array_clean($data, ['email', 'persist', 'csrf_name', 'csrf_value']));
            }
        }

        if (isset($this->flash->getMessages()["oldNow"][0]['slimUsername'])){
            $username = $this->flash->getMessages()["oldNow"][0]['slimUsername'];
        }
        elseif(isset($this->flash->getMessages()["old"][0]['slimUsername']) ){
            $username = $this->flash->getMessages()["old"][0]['slimUsername'];
        }
        else{
            $username = null;
        }

        // echo "<pre>END: ERROR: " . var_export($this->flash->getMessages()["error"], true) . "</pre>";
        // echo "<pre>END OLD: " . var_export($this->flash->getMessages()["old"], true) . "</pre>";
        // echo "<pre>END OLD NOW: " . var_export($this->flash->getMessages()["oldNow"], true) . "</pre>";

        return $this->view->render($response, 'auth\signin.twig', array('username' => @$username));
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

    /**
     * @param array $array The array
     * @param array $keys  The keys
     *
     * @return array
     */
    function array_clean(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

}