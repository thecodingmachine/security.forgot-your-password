<?php

namespace Mouf\Security\Password;

use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\Html\Renderer\Renderable;

class EmailSentView implements HtmlElementInterface
{
    use Renderable;

    /**
     * @var string
     */
    private $email;

    /**
     * EmailSentView constructor.
     *
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail() : string
    {
        return $this->email;
    }
}
