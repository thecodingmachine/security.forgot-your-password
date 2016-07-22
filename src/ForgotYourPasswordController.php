<?php


namespace Mouf\Security\Password;


use Mouf\Html\HtmlElement\HtmlBlock;
use Mouf\Html\Renderer\Twig\TwigTemplate;
use Mouf\Html\Template\TemplateInterface;
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
     *
     * @param string|null $email
     */
    public function index(string $email = null)
    {
        // Let's add the twig file to the template.
        $this->content->addHtmlElement(new TwigTemplate($this->twig, 'vendor/mouf/security.forgot-your-password/views/forgotYourPassword/index.twig', array("email"=>$email)));

        return new HtmlResponse($this->template);
    }
}