<?php

namespace Mouf\Security\Password\Api;

interface PasswordStrengthCheck
{
    /**
     * Returns true if the password is strong enough, false otherwise.
     *
     * @param string $password
     *
     * @return bool
     */
    public function checkPasswordStrength(string $password) : bool;

    /**
     * Returns a list of rules that password must fullfil (in text).
     *
     * @return string
     */
    public function getPasswordRules() : string;
}
