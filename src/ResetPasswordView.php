<?php

namespace Mouf\Security\Password;

use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\Html\Renderer\Renderable;

class ResetPasswordView implements HtmlElementInterface
{
    use Renderable;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $passwordRules;

    /**
     * @var bool
     */
    private $displayMismatchPassword;

    /**
     * @var bool
     */
    private $displayPoorPassword;

    /**
     * ResetPasswordView constructor.
     *
     * @param $token
     * @param $passwordRules
     */
    public function __construct(string $token, string $passwordRules)
    {
        $this->token = $token;
        $this->passwordRules = $passwordRules;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param bool $displayMismatchPassword
     *
     * @return ResetPasswordView
     */
    public function withDisplayMismatchPassword(bool $displayMismatchPassword = true) : ResetPasswordView
    {
        $clone = clone $this;
        $clone->displayMismatchPassword = $displayMismatchPassword;

        return $clone;
    }

    /**
     * @param bool $displayPoorPassword
     *
     * @return ResetPasswordView
     */
    public function withDisplayPoorPassword(bool $displayPoorPassword = true) : ResetPasswordView
    {
        $clone = clone $this;
        $clone->displayPoorPassword = $displayPoorPassword;

        return $clone;
    }
}
