<?php

namespace Mouf\Security\Password;

use Mouf\Html\HtmlElement\HtmlBlock;
use Mouf\Html\Template\TemplateInterface;
use Mouf\Mvc\Splash\Annotations\Get;
use Mouf\Mvc\Splash\Annotations\Post;
use Mouf\Mvc\Splash\Annotations\URL;
use Mouf\Mvc\Splash\HtmlResponse;
use Mouf\Security\Password\Api\PasswordStrengthCheck;
use Mouf\Security\UserService\UserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Twig_Environment;
use Mouf\Security\Password\Api\EmailNotFoundException;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;

class ForgotYourPasswordController
{
    private $baseUrl = 'forgot';

    /**
     * The logger used by this controller.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The template used by this controller.
     *
     * @var TemplateInterface
     */
    private $template;

    /**
     * The main content block of the page.
     *
     * @var HtmlBlock
     */
    private $content;

    /**
     * The Twig environment (used to render Twig templates).
     *
     * @var Twig_Environment
     */
    private $twig;

    /**
     * The service that will actually check the tokens.
     *
     * @var ForgotYourPasswordService
     */
    private $forgotYourPasswordService;

    /**
     * The userservice to log people when a token is found.
     *
     * @var UserService
     */
    private $userService;

    /**
     * Enable this option if you want the "forgot your password screen" to NOT show the "email not found message" and always show the "an email has been sent" message.
     * This improves security because an attacker typing an email address of someone else cannot not if this user is registered or not in the application.
     *
     * At the same time, it decreases usability.
     *
     * @var bool
     */
    private $noLeak = false;

    /**
     * @var PasswordStrengthCheck
     */
    private $passwordStrengthCheck;

    /**
     * The URL to continue to when password has been changed.
     * Relative to the root path of the application.
     * Defaults to '/'.
     *
     * @var string
     */
    private $continueUrl = '/';

    /**
     * Controller's constructor.
     *
     * @param LoggerInterface   $logger   The logger
     * @param TemplateInterface $template The template used by this controller
     * @param HtmlBlock         $content  The main content block of the page
     * @param Twig_Environment  $twig     The Twig environment (used to render Twig templates)
     */
    public function __construct(LoggerInterface $logger, TemplateInterface $template, HtmlBlock $content, Twig_Environment $twig, ForgotYourPasswordService $forgotYourPasswordService, UserService $userService, PasswordStrengthCheck $passwordStrengthCheck)
    {
        $this->logger = $logger;
        $this->template = $template;
        $this->content = $content;
        $this->twig = $twig;
        $this->forgotYourPasswordService = $forgotYourPasswordService;
        $this->userService = $userService;
        $this->passwordStrengthCheck = $passwordStrengthCheck;
    }

    /**
     * Enable this option if you want the "forgot your password screen" to NOT show the "email not found message" and always show the "an email has been sent" message.
     * This improves security because an attacker typing an email address of someone else cannot not if this user is registered or not in the application.
     *
     * At the same time, it decreases usability.
     *
     * @param bool $noLeak
     */
    public function setNoLeak(bool $noLeak)
    {
        $this->noLeak = $noLeak;
    }

    /**
     * The URL to continue to when password has been changed.
     * Relative to the root path of the application.
     * Defaults to '/'.
     *
     * @param string $continueUrl
     */
    public function setContinueUrl(string $continueUrl)
    {
        $this->continueUrl = $continueUrl;
    }

    /**
     * Displays the screen to enter the email.
     *
     * @URL("{$this->baseUrl}/password")
     * @Get
     *
     * @param string|null $email
     *
     * @return ResponseInterface
     */
    public function index(string $email = null) : ResponseInterface
    {
        return $this->displayMainForm($email);
    }

    private function displayMainForm(string $email = null, bool $notFoundMessage = false) : ResponseInterface
    {
        $view = new ForgotYourPasswordView();
        if ($email) {
            $view->setEmail($email);
        }

        $view->setDisplayEmailNotFound($notFoundMessage);

        $code = $notFoundMessage ? 404 : 200;

        // Let's add the twig file to the template.
        $this->content->addHtmlElement($view, $code);

        return new HtmlResponse($this->template);
    }

    private function jsonNotFoundResponse() : ResponseInterface
    {
        return new JsonResponse([
            'error' => 'Email not found',
        ], 404);
    }

    /**
     * Displays the screen to enter the email.
     *
     * @URL("{$this->baseUrl}/password")
     * @Post
     *
     * @param string $email
     *
     * @return ResponseInterface
     */
    public function submit(string $email, ServerRequestInterface $request) : ResponseInterface
    {
        try {
            // Let's get the URL of the reset password
            $currentUrl = $request->getUri()->getPath();
            $url = substr($currentUrl, 0, strrpos($currentUrl, '/password')).'/reset';

            $this->forgotYourPasswordService->sendMail($email, $request->getUri()->withPath($url)->withQuery(''));
        } catch (EmailNotFoundException $exception) {
            if (!$this->noLeak) {
                if ($this->isJson($request)) {
                    return $this->jsonNotFoundResponse();
                } else {
                    return $this->displayMainForm($email, true);
                }
            }
        }

        if ($this->isJson($request)) {
            return $this->jsonSentResponse();
        } else {
            return new RedirectResponse(ROOT_URL.$this->baseUrl.'/mail_sent?email='.urlencode($email));
        }
    }

    private function isJson(ServerRequestInterface $request) : bool
    {
        return strpos($request->getHeaderLine('Accept'), 'json') !== false;
    }

    private function jsonSentResponse() : ResponseInterface
    {
        return new JsonResponse([
            'status' => 'ok',
            'message' => 'An email was sent to your mailbox.',
        ]);
    }

    /**
     * @URL("{$this->baseUrl}/mail_sent")
     * @Get()
     *
     * @param string $email
     *
     * @return ResponseInterface
     */
    public function displayMailSentScreen(string $email) : ResponseInterface
    {
        $this->content->addHtmlElement(new EmailSentView($email));

        return new HtmlResponse($this->template);
    }

    /**
     * @URL("{$this->baseUrl}/reset")
     * @Get()
     *
     * @param string $token
     *
     * @return ResponseInterface
     */
    public function displayResetAccessPage(string $token) : ResponseInterface
    {
        $isValid = $this->forgotYourPasswordService->checkToken($token);

        if (!$isValid) {
            $this->content->addHtmlElement(new TokenNotFoundView($token));

            return new HtmlResponse($this->template, 404);
        } else {
            $this->content->addHtmlElement(new ResetPasswordView($token, $this->passwordStrengthCheck->getPasswordRules()));

            return new HtmlResponse($this->template);
        }
    }

    /**
     * @URL("{$this->baseUrl}/reset")
     * @Post()
     *
     * @param string                 $token
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function resetAccess(string $token, string $password, string $confirmpassword, ServerRequestInterface $request) : ResponseInterface
    {
        $isValidToken = $this->forgotYourPasswordService->checkToken($token);

        if (!$isValidToken) {
            if ($this->isJson($request)) {
                return $this->jsonTokenNotFoundResponse();
            } else {
                $this->content->addHtmlElement(new TokenNotFoundView($token));

                return new HtmlResponse($this->template, 404);
            }
        }

        if ($password !== $confirmpassword) {
            if ($this->isJson($request)) {
                return new JsonResponse([
                    'error' => 'Mismatching passwords',
                ], 400);
            } else {
                $view = new ResetPasswordView($token, $this->passwordStrengthCheck->getPasswordRules());
                $this->content->addHtmlElement($view->withDisplayMismatchPassword());

                return new HtmlResponse($this->template, 400);
            }
        }

        if ($this->passwordStrengthCheck->checkPasswordStrength($password) === false) {
            if ($this->isJson($request)) {
                return new JsonResponse([
                    'error' => 'Password strength too weak',
                    'rules' => $this->passwordStrengthCheck->getPasswordRules(),
                ], 400);
            } else {
                $view = new ResetPasswordView($token, $this->passwordStrengthCheck->getPasswordRules());
                $this->content->addHtmlElement($view->withDisplayPoorPassword());

                return new HtmlResponse($this->template, 400);
            }
        }

        $this->forgotYourPasswordService->useToken($token, $password);

        if ($this->isJson($request)) {
            return new JsonResponse([
                'status' => 'ok',
            ], 200);
        } else {
            return new RedirectResponse('reset_confirm');
        }
    }

    /**
     * @URL("{$this->baseUrl}/reset_confirm")
     * @Get()
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function confirmResetAccess(ServerRequestInterface $request) : ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $path = substr($path, 0, strrpos($path, $this->baseUrl.'/reset'));
        $path .= ltrim($this->continueUrl, '/');

        $view = new ConfirmResetPasswordView($path);
        $this->content->addHtmlElement($view);

        return new HtmlResponse($this->template);
    }

    protected function jsonTokenNotFoundResponse() : ResponseInterface
    {
        return new JsonResponse([
            'error' => 'Token not found',
        ], 404);
    }
}
