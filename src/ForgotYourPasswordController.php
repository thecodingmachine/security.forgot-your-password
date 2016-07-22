<?php


namespace Mouf\Security\Password;


use Mouf\Mvc\Splash\Annotations\URL;

class ForgotYourPasswordController
{
    private $baseUrl = 'forgot';

    /**
     * Displays the screen to enter the email.
     *
     * @URL("{$this->baseUrl}/password")
     *
     * @param string|null $email
     */
    public function index(string $email = null)
    {

    }
}