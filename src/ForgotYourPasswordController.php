<?php


namespace Mouf\Security\Password;


use Mouf\Html\HtmlElement\HtmlBlock;
use Mouf\Html\Template\TemplateInterface;
use Mouf\Mvc\Splash\Annotations\Get;
use Mouf\Mvc\Splash\Annotations\Post;
use Mouf\Mvc\Splash\Annotations\URL;
use Mouf\Mvc\Splash\HtmlResponse;
use Psr\Log\LoggerInterface;
use Twig_Environment;

class ForgotYourPasswordController
{
    private $baseUrl = 'forgot';

    /**
     * The logger used by this controller.
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The template used by this controller.
     * @var TemplateInterface
     */
    private $template;

    /**
     * The main content block of the page.
     * @var HtmlBlock
     */
    private $content;

    /**
     * The Twig environment (used to render Twig templates).
     * @var Twig_Environment
     */
    private $twig;

    /**
     * Controller's constructor.
     * @param LoggerInterface $logger The logger
     * @param TemplateInterface $template The template used by this controller
     * @param HtmlBlock $content The main content block of the page
     * @param Twig_Environment $twig The Twig environment (used to render Twig templates)
     */
    public function __construct(LoggerInterface $logger, TemplateInterface $template, HtmlBlock $content, Twig_Environment $twig) {
        $this->logger = $logger;
        $this->template = $template;
        $this->content = $content;
        $this->twig = $twig;
    }

    /**
     * Displays the screen to enter the email.
     *
     * @URL("{$this->baseUrl}/password")
     * @Get
     *
     * @param string|null $email
     */
    public function index(string $email = null)
    {
        $view = new ForgotYourPasswordView();
        if ($email) {
            $view->setEmail($email);
        }

        // Let's add the twig file to the template.
        $this->content->addHtmlElement($view);

        return new HtmlResponse($this->template);
    }

    /**
     * Displays the screen to enter the email.
     *
     * @URL("{$this->baseUrl}/password")
     * @Post
     *
     * @param string $email
     */
    public function submit(string $email)
    {
        // Let's add the twig file to the template.
        $this->content->addHtmlElement(new ForgotYourPasswordView($email));

        return new HtmlResponse($this->template);
    }
}
