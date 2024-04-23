<?php

namespace App\Auth;

use App\Models\User;
use Psr\Container\ContainerInterface;

use Respect\Validation\Validator as v;

class passwordHelper
{
    var $requestData;

    var $loggedUserData;

    var $errorMessage;

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

        if ($validationOld === false) {
            $this->errorMessage = 'Wrong current password.';
            return false;
		}

        // https://respect-validation.readthedocs.io/en/2.3/08-list-of-rules-by-category/
        $validationNew = $this->validator->validate(
                                    array('password' => $this->requestData['password']), 
                                    [
                                        'password' => v::noWhitespace()->notEmpty()->length(7, null),
                                    ],
                                    " ",
                                    " "
                            );

        if ($validationNew->failed()) {
            $valResults = $validationNew->getErrors();
            foreach ($valResults as $valField => $valError) {
                $this->errorMessage .= $valError;
            }

            return false;
        }
                                    
        return true;
    }

    function changePassword(){
        
        $auth_identity = $this->container->auth->getIdentity();

        $loggedUser = User::where('username', $auth_identity['username'])
                        ->get(['userName', 'passwordHash']);

        list($loggedUserData) = $loggedUser->toArray();
        $this->loggedUserData = $loggedUserData;

        //$this->logger->debug("passwordHelper\changePassword dump current DB data: " . var_export($loggedUser['passwordHash'],true));

        $valRes = $this->validatePassword();

        if($valRes === false){
            return false;
        }

        // update PW in DB
        $pwHash = password_hash($this->requestData['password'], PASSWORD_DEFAULT, array('cost' => 10));

        $affRows = User::where('username', $auth_identity['username'])
                ->update(['passwordHash' => $pwHash]);

        if($affRows <> 1){
            $this->errorMessage = 'Update password failed! Database error.' . "(affected rows: " . $affRows . ")";
            return false;
        }

        return true;
    }
}