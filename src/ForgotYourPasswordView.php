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
     * @var bool
     */
    private $displayEmailNotFound = false;

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

    /**
     * @return bool
     */
    public function isDisplayEmailNotFound(): bool
    {
        return $this->displayEmailNotFound;
    }

    /**
     * @param bool $displayEmailNotFound
     */
    public function setDisplayEmailNotFound(bool $displayEmailNotFound)
    {
        $this->displayEmailNotFound = $displayEmailNotFound;
    }
}
