[![Latest Stable Version](https://poser.pugx.org/mouf/security.forgot-your-password/v/stable)](https://packagist.org/packages/mouf/security.forgot-your-password)
[![Total Downloads](https://poser.pugx.org/mouf/security.forgot-your-password/downloads)](https://packagist.org/packages/mouf/security.forgot-your-password)
[![Latest Unstable Version](https://poser.pugx.org/mouf/security.forgot-your-password/v/unstable)](https://packagist.org/packages/mouf/security.forgot-your-password)
[![License](https://poser.pugx.org/mouf/security.forgot-your-password/license)](https://packagist.org/packages/mouf/security.forgot-your-password)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thecodingmachine/security.forgot-your-password/badges/quality-score.png?b=1.0)](https://scrutinizer-ci.com/g/thecodingmachine/security.forgot-your-password/?branch=1.0)
[![Build Status](https://travis-ci.org/thecodingmachine/security.forgot-your-password.svg?branch=1.0)](https://travis-ci.org/thecodingmachine/security.forgot-your-password)
[![Coverage Status](https://coveralls.io/repos/thecodingmachine/security.forgot-your-password/badge.svg?branch=1.0&service=github)](https://coveralls.io/github/thecodingmachine/security.forgot-your-password?branch=1.0)

Forgot your password feature for Mouf
=====================================

This package contains the controllers and services necessary to implement a "forgot your password" feature in Mouf.

Installation
============

```
composer require mouf/security.forgot-your-password
```

Usage
=====

Install the package using the Mouf installer.

This package **provides**:

- A controller (`ForgotYourPasswordController`): this controller provides the default `forgot/password` route that leads to the "I forgot my password" form.
  It also provides the `forgot/reset` route that is linked to in the mail. This route allows the user to reset its password.
- A service (`ForgotYourPasswordService`): this service is in charge of generating the unique token and sending the mail.

This package **does not contain** a way to access your database to store/retrieve tokens and associated users. For this, you need to provide a package implementing the [`ForgotYourPasswordDao` interface](http://mouf-php.com/packages/mouf/security.forgot-your-password-interface).
For this, you might want to you an existing package already implementing it. If you are using TDBM, we recommend using [mouf/security.daos.tdbm](http://mouf-php.com/packages/mouf/security.daos.tdbm).

Customizing
===========

In this package, the views are based on the Bootstrap framework CSS. If your project uses another framework, you'll need to overwrite the views.

Customizing the "forgot your password" page
-------------------------------------------

The main "forgot your password" form is rendered using the `Mouf\Security\Password\ForgotYourPasswordView`.
The Twig template is available in `vendor/mouf/security.forgot-your-password/src/templates/Mouf/Security/Password/ForgotYourPasswordView.twig`.
To overwrite, copy this file to `src/templates/Mouf/Security/Password/ForgotYourPasswordView.twig` and purge your cache.

Customizing the "email sent" page
---------------------------------

Once the "forgot your password" page is filled, the user arrives on the "email sent" page.

The page is rendered using the `Mouf\Security\Password\EmailSentView`.
The Twig template is available in `vendor/mouf/security.forgot-your-password/src/templates/Mouf/Security/Password/EmailSentView.twig`.
To overwrite, copy this file to `src/templates/Mouf/Security/Password/EmailSentView.twig` and purge your cache.

Customizing the email
---------------------

The email originates from a [`SwiftTwigMailTemplate`](https://github.com/thecodingmachine/swift-twig-mail-template).

If you want to customize this email, you can either:

- change the Twig template completely (by editing the |forgotYourPasswordMailTemplate` instance in the container and modifying the `twigPath` property to your own file)
- or you can simply overwrite the i18n strings by providing your own keys for the subject and the body:
    - `forgotyourpassword.mail.subject` is the subject
    - `forgotyourpassword.mail.body` is the body of the text
    
Customizing the "token not found" page
--------------------------------------

In case the user clicks on a URL link with a token that has already been used (or that is invalid), the "token not found" page is displayed.

The page is rendered using the `Mouf\Security\Password\TokenNotFoundView`.
The Twig template is available in `vendor/mouf/security.forgot-your-password/src/templates/Mouf/Security/Password/TokenNotFoundView.twig`.
To overwrite, copy this file to `src/templates/Mouf/Security/Password/TokenNotFoundView.twig` and purge your cache.

Customizing the "reset password" page
-------------------------------------

When the user clicks on the link in the mail, he is redirected to the "reset password" page.

The page is rendered using the `Mouf\Security\Password\ResetPasswordView`.
The Twig template is available in `vendor/mouf/security.forgot-your-password/src/templates/Mouf/Security/Password/ResetPasswordView.twig`.
To overwrite, copy this file to `src/templates/Mouf/Security/Password/ResetPasswordView.twig` and purge your cache.

Customizing the "password reseted" page
---------------------------------------

This is the last page of the workflow, confirming the password was reset successfully.

The page is rendered using the `Mouf\Security\Password\ConfirmResetPasswordView`.
The Twig template is available in `vendor/mouf/security.forgot-your-password/src/templates/Mouf/Security/Password/ConfirmResetPasswordView.twig`.
To overwrite, copy this file to `src/templates/Mouf/Security/Password/ConfirmResetPasswordView.twig` and purge your cache.

Customizing password check strength
-----------------------------------

By default when you reset your password, you will be asked a password that is:

- at least 7 characters long
- that contains at least one upper case letter
- that contains at least one lower case letter
- that contains at least one number

This can be completely configured in the `Mouf\Security\Password\PasswordStrengthCheck` instance.

Also, if you have very specific needs regarding password strength (for instance: at least 2 special characters, only japanese characters allowed, etc...), then you can simply provide your own service as long as it implements `Mouf\Security\Password\Api\PasswordStrengthCheck`.
