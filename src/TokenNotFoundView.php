<?php

namespace Mouf\Security\Password;

use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\Html\Renderer\Renderable;

class TokenNotFoundView implements HtmlElementInterface
{
    use Renderable;

    /**
     * @var string
     */
    private $token;

    /**
     * EmailSentView constructor.
     *
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
