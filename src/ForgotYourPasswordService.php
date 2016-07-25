<?php

namespace Mouf\Security\Password;

use Mouf\Security\Password\Api\ForgotYourPasswordDao;
use Mouf\Security\Password\Api\TokenNotFoundException;
use Mouf\Security\UserService\UserService;
use Mouf\Utils\Value\ValueInterface;
use Psr\Http\Message\UriInterface;
use Ramsey\Uuid\Uuid;
use TheCodingMachine\Mail\Template\SwiftTwigMailTemplate;

class ForgotYourPasswordService
{
    /**
     * @var ForgotYourPasswordDao
     */
    private $forgetYourPasswordDao;

    /**
     * @var \Swift_Mailer
     */
    private $swiftMailer;

    /**
     * @var SwiftTwigMailTemplate
     */
    private $mailTemplate;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * ForgotYourPasswordService constructor.
     *
     * @param ForgotYourPasswordDao $forgetYourPasswordDao
     * @param \Swift_Mailer         $swiftMailer
     * @param ValueInterface|string $from
     */
    public function __construct(ForgotYourPasswordDao $forgetYourPasswordDao, \Swift_Mailer $swiftMailer, SwiftTwigMailTemplate $mailTemplate, UserService $userService)
    {
        $this->forgetYourPasswordDao = $forgetYourPasswordDao;
        $this->swiftMailer = $swiftMailer;
        $this->mailTemplate = $mailTemplate;
        $this->userService = $userService;
    }

    /**
     * Generates and sends via mail a token for user whose mail is $email, stores the token in database and returns the token.
     * Throws an EmailNotFoundException if the email is not part of the database.
     *
     * @param string       $email
     * @param UriInterface $resetPasswordUrl The URL to reset the password.
     *
     * @throws \Mouf\Security\Password\Api\EmailNotFoundException
     */
    public function sendMail(string $email, UriInterface $resetPasswordUrl)
    {
        // Let's generate a new token
        $token = Uuid::uuid4()->toString();

        // Let's store this new token
        $this->forgetYourPasswordDao->setToken($email, $token);

        $user = $this->forgetYourPasswordDao->getUserByToken($token);

        $resetPasswordUrl = $resetPasswordUrl->withQuery('token='.urlencode($token));

        $mail = $this->mailTemplate->renderMail([
            'url' => (string) $resetPasswordUrl,
            'website' => $resetPasswordUrl->getHost(),
            'user' => $user->getLogin(),
        ]);

        $mail->setTo($email);

        $this->swiftMailer->send($mail);
    }

    /**
     * Returns true if a token is valid, false otherwise.
     */
    /**
     * @param string $token
     *
     * @return bool
     */
    public function checkToken(string $token) : bool
    {
        try {
            $this->forgetYourPasswordDao->getUserByToken($token);
        } catch (TokenNotFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     * Uses the $token to replace the $password.
     * The token will no longer be usable.
     * Just after this action, the user is logged (using the UserService).
     *
     * Throws an TokenNotFoundException if the token is not part of the database.
     *
     * @param string $token
     * @param string $password
     *
     * @throws TokenNotFoundException
     */
    public function useToken(string $token, string $password)
    {
        $user = $this->forgetYourPasswordDao->getUserByToken($token);

        $this->forgetYourPasswordDao->setPasswordAndDiscardToken($token, $password);

        $this->userService->loginWithoutPassword($user->getLogin());
    }
}
