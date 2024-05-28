<?php

namespace App\Controllers\Auth;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Controllers\Controller;
// use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Cartalyst\Sentinel\Sentinel;
use Exception;
use Slim\Interfaces\RouteParserInterface;
use Slim\Flash\Messages;
use Slim\Views\Twig;

class AuthController extends Controller
{
    public \Monolog\Logger $logger;

    /**
     * @var Messages
     */
    protected Messages $flash;

    /**
     * @var RouteParserInterface
     */
    protected RouteParserInterface $routeParser;

    /**
     * @var Twig
     */
    protected Twig $view;

    protected Sentinel $sentinel;

    public function __construct(
        ContainerInterface $container,
        RouteParserInterface $routeParser,
    ) {
        $this->container = $container;
        $this->routeParser = $routeParser;
        $this->flash = $container->get('flash');
        $this->logger = $container->get('logger');
        $this->view = $container->get('view');

        $this->sentinel = $container->get('sentinel');

        $this->logger->info("authController\__construct called");
    }

    public function signin(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $flashEnabled = true;
        if (array_key_exists('flashEnabled', $args)) {
            $flashEnabled = $args['flashEnabled'];
        }

        $redirect = $request->getQueryParams()['redirect'];

        if ($request->getMethod() == "POST") {

            $redirect = $request->getParsedBody()['redirect'];
            $data = array(
                'email' => $request->getParsedBody()['slimUsername'],
                'password' => $request->getParsedBody()['slimPassword'],
            );

            try {
                if (!$this->sentinel->authenticate(
                    $this->array_clean(
                        $data,
                        [
                            'email',
                            'password',
                        ]
                    ),
                    true
                )
                ) {
                    throw new Exception('Incorrect email or password.');
                } else {
                    // login OK
                    $this->logger->info(
                        "authController\signin: authentication was successful, email: "
                                        . var_export($this->sentinel->getUser()->email, true)
                                        . ", redirect URL: "
                                        . var_export($redirect, true)
                    );

                    $url = $this->routeParser->urlFor('home');
                    return $response->withStatus(302)->withHeader('Location', $redirect ?: $url);
                }
            } catch (Exception $e) {
                $this->flash->addMessageNow('error', $e->getMessage());
                $this->logger->error("authController\signin " . $e->getMessage(), $this->array_clean($data, ['email', 'persist', 'csrf_name', 'csrf_value']));
            }
        }

        if (isset($this->flash->getMessages()["oldNow"][0]['slimUsername'])) {
            $username = $this->flash->getMessages()["oldNow"][0]['slimUsername'];
        } elseif(isset($this->flash->getMessages()["old"][0]['slimUsername'])) {
            $username = $this->flash->getMessages()["old"][0]['slimUsername'];
        } else {
            $username = null;
        }

        // echo "<pre>END: ERROR: " . var_export($this->flash->getMessages()["error"], true) . "</pre>";
        // echo "<pre>END OLD: " . var_export($this->flash->getMessages()["old"], true) . "</pre>";
        // echo "<pre>END OLD NOW: " . var_export($this->flash->getMessages()["oldNow"], true) . "</pre>";

        $this->logger->debug("AuthController/signin: redirect url: " . var_export($redirect, true));

        return $this->view->render(
            $response,
            'auth\signin.twig',
            array(
                'flashEnabled' => boolval($flashEnabled),
                'username' => @$username,
                'redirect' => $redirect
            )
        );
    }

    public function signout($request, $response, array $args)
    {
        $this->logger->info("AuthController/signout called");
        $this->logger->debug("AuthController/signout: dump user identity: ".var_export($this->sentinel->getUser()->email, true));

        if (!$this->sentinel->guest()) {
            $rs = $this->sentinel->logout();
            $this->logger->info("AuthController/signout: signout action result: " . var_export($rs, true));
        } else {
            $this->logger->info("AuthController/signout: user is not logged, redirecting to home");
        }

        //redirect
        $url = $this->routeParser->urlFor('home');
        return $response->withStatus(302)->withHeader('Location', $url);
    }

    /**
     * @param array $array The array
     * @param array $keys  The keys
     *
     * @return array
     */
    public function array_clean(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

}
