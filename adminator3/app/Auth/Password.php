<?php

namespace App\Auth;

use App\Models\User;
use Psr\Container\ContainerInterface;

class passwordHelper
{
    var $requestData;

    var $loggedUserData;

	function __construct(ContainerInterface $container, $requestData) {
        $this->container = $container;
		$this->logger = $container->logger;
        $this->validator = $container->validator;

        $this->requestData = $requestData;
	}

    function validatePassword(){

        // $this->logger->debug("passwordHelper\changePassword dump current form data: " . var_export($this->requestData['password_old'],true));

        $validationOld = $this->validator->validatePassword($this->requestData['password_old'], $this->loggedUserData['passwordHash']);

        $this->logger->debug("PasswordController\postChangePassword: validationOld for user: " . var_export($this->loggedUserData['userName'], true) . " returned: " . var_export($validationOld,true));

        $validationNew = $this->validator->validate($input_data, [
            'Popis objektu#popis' => v::noWhitespace()->notEmpty()->alnum("-")->length(3,20),
            'IP adresa#ip' => v::noWhitespace()->notEmpty()->ip(),
            'Přípojný bod#id_nodu' => v::number()->greaterThan(0),
            'MAC adresa#mac' => v::notEmpty()->macAddress(),
            // 'puk' => v::number(),
            // 'pin1' => v::number(),
            // 'pin2' => v::number(),
            'Číslo portu (ve switchi)#port_id' => v::number(),
            'Tarif#id_tarifu' => v::number()->greaterThan(0),
        ]);

		// if ($validationOld === false) {
        //     $this->flash->addMessage('error', 'Wrong current password.');
		// 	return $response->withHeader('Location', $this->router->urlFor('auth.password.change'));
		// }

        return false;
    }

    function changePassword(){
        
        $auth_identity = $this->container->auth->getIdentity();

        $loggedUser = User::where('username', $auth_identity['username'])
                        ->get(['userName', 'passwordHash']);

        list($loggedUserData) = $loggedUser->toArray();
        $this->loggedUserData = $loggedUserData;

        //$this->logger->debug("passwordHelper\changePassword dump current DB data: " . var_export($loggedUser['passwordHash'],true));

        $valRes = $this->validatePassword();

        // if($valRes === true){
        //     $this->logger->info('');
        // }

        return false;
    }
}