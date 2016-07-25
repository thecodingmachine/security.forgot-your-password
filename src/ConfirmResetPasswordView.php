<?php

namespace Mouf\Security\Password;

use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\Html\Renderer\Renderable;

class ConfirmResetPasswordView implements HtmlElementInterface
{
    use Renderable;

    /**
     * @var string
     */
    private $continueUrl;

    /**
     * ConfirmResetPasswordView constructor.
     *
     * @param string $continueUrl
     */
    public function __construct($continueUrl)
    {
        $this->continueUrl = $continueUrl;
    }
}
