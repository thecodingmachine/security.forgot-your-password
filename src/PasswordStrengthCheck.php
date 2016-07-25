<?php

namespace Mouf\Security\Password;

use Mouf\Utils\I18n\Fine\TranslatorInterface;

class PasswordStrengthCheck implements \Mouf\Security\Password\Api\PasswordStrengthCheck
{
    private $minimumLength = 7;

    private $mustHaveUpperCase = true;

    private $mustHaveLowerCase = true;

    private $mustHaveNumber = true;

    /**
     * @var TranslatorInterface
     */
    private $translationService;

    /**
     * PasswordStrengthCheck constructor.
     *
     * @param TranslatorInterface $translationService
     */
    public function __construct(TranslatorInterface $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * @return int
     */
    public function getMinimumLength(): int
    {
        return $this->minimumLength;
    }

    /**
     * @param int|null $minimumLength
     */
    public function setMinimumLength(int $minimumLength = null)
    {
        $this->minimumLength = $minimumLength;
    }

    /**
     * @return bool
     */
    public function isMustHaveUpperCase(): bool
    {
        return $this->mustHaveUpperCase;
    }

    /**
     * @param bool $mustHaveUpperCase
     */
    public function setMustHaveUpperCase(bool $mustHaveUpperCase)
    {
        $this->mustHaveUpperCase = $mustHaveUpperCase;
    }

    /**
     * @return bool
     */
    public function isMustHaveLowerCase(): bool
    {
        return $this->mustHaveLowerCase;
    }

    /**
     * @param bool $mustHaveLowerCase
     */
    public function setMustHaveLowerCase(bool $mustHaveLowerCase)
    {
        $this->mustHaveLowerCase = $mustHaveLowerCase;
    }

    /**
     * @return bool
     */
    public function isMustHaveNumber(): bool
    {
        return $this->mustHaveNumber;
    }

    /**
     * @param bool $mustHaveNumber
     */
    public function setMustHaveNumber(bool $mustHaveNumber)
    {
        $this->mustHaveNumber = $mustHaveNumber;
    }

    /**
     * Returns true if the password is strong enough, false otherwise.
     *
     * @param string $password
     *
     * @return bool
     */
    public function checkPasswordStrength(string $password) : bool
    {
        if ($this->minimumLength !== null && strlen($password) < $this->minimumLength) {
            return false;
        }

        if ($this->mustHaveLowerCase === true && !preg_match('#[a-z]+#', $password)) {
            return false;
        }

        if ($this->mustHaveUpperCase === true && !preg_match('#[A-Z]+#', $password)) {
            return false;
        }

        if ($this->mustHaveUpperCase === true && !preg_match('#[0-9]+#', $password)) {
            return false;
        }

        return true;
    }

    /**
     * Returns a list of rules that password must fullfil (in text).
     *
     * @return string
     */
    public function getPasswordRules() : string
    {
        if ($this->minimumLength === null && !$this->mustHaveUpperCase
            && !$this->mustHaveLowerCase && !$this->mustHaveNumber) {
            return '';
        }

        $base = $this->translationService->getTranslation('passwordservice.base_error_message');
        $base .= '<ul>';

        if ($this->minimumLength !== null) {
            $base .= '<li>'.$this->translationService->getTranslation('passwordservice.minimum_length', ['length' => $this->minimumLength]).'</li>';
        }
        if ($this->mustHaveLowerCase === true) {
            $base .= '<li>'.$this->translationService->getTranslation('passwordservice.upper_case').'</li>';
        }
        if ($this->mustHaveLowerCase === true) {
            $base .= '<li>'.$this->translationService->getTranslation('passwordservice.lower_case').'</li>';
        }
        if ($this->mustHaveNumber === true) {
            $base .= '<li>'.$this->translationService->getTranslation('passwordservice.number_case').'</li>';
        }

        $base .= '</ul>';

        return $base;
    }
}
