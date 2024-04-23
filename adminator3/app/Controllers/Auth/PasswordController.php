<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;

// https://github.com/HavenShen/slim-born/blob/master/app/Controllers/Auth/PasswordController.php

class PasswordController extends Controller
{
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
            $this->flash->addMessage('error', 'Smt bad happen.');
            return $response->withHeader('Location', $this->router->urlFor('auth.password.change'));
        }
        else{
            $this->flash->addMessage('info', 'Your password was changed');
            return $response->withHeader('Location', $this->router->urlFor('home'));
        }
	}
}
