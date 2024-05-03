<?php

namespace App\Auth;

use App\Models\User;
use Psr\Container\ContainerInterface;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Respect\Validation\Validator as v;

class passwordHelper
{
    private $container;
    
    public $requestData;

    public $loggedUserData;

    public $errorMessage;

    protected $validator;

    protected $logger;

    public function __construct(ContainerInterface $container, $requestData)
    {
        $this->container = $container;
        $this->logger = $container->get('logger');
        $this->validator = $container->get('validator');

        $this->requestData = $requestData;
    }

    public function validatePassword()
    {

        // $this->logger->debug("passwordHelper\changePassword dump current form data: " . var_export($this->requestData['password_old'],true));

        $validationOld = $this->validator->validatePassword($this->requestData['password_old'], $this->loggedUserData['password']);

        $this->logger->debug(
            "PasswordController\postChangePassword: validationOld for user: "
                                 . var_export($this->loggedUserData['email'], true)
            . " returned: " . var_export($validationOld, true)
        );

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

    public function changePassword()
    {

        $this->loggedUserData = array(
            "email" => (string) Sentinel::getUser()->email,
            "password" => (string) Sentinel::getUser()->password
        );
        // $this->logger->debug("passwordHelper\changePassword current data: " . var_export($this->loggedUserData,true));

        $valRes = $this->validatePassword();

        if($valRes === false) {
            return false;
        }

        // update PW in DB
        $pwHash = password_hash($this->requestData['password'], PASSWORD_DEFAULT, array('cost' => 10));

        $affRows = User::where('email', $this->loggedUserData['email'])
                ->update(['password' => $pwHash]);

        if($affRows <> 1) {
            $this->errorMessage = 'Update password failed! Database error.' . "(affected rows: " . $affRows . ")";
            return false;
        } else {
            $this->logger->info("PasswordController\changePassword: password successfully changed for user " . var_export($this->loggedUserData['email'], true));
        }

        return true;
    }
}
