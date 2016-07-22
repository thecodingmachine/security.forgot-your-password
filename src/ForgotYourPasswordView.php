<?php


namespace Mouf\Security\Password;


use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\Html\Renderer\Renderable;

class ForgotYourPasswordView implements HtmlElementInterface
{
    use Renderable;

    /**
     * @var string
     */
    private $email;

    /**
     * @return string
     */
    public function getEmail() : string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }
}
