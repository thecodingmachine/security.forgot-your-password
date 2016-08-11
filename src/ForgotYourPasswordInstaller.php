<?php

namespace Mouf\Security\Password;

use Mouf\Actions\InstallUtils;
use Mouf\Html\Renderer\RendererUtils;
use Mouf\Installer\PackageInstallerInterface;
use Mouf\MoufManager;

class ForgotYourPasswordInstaller implements PackageInstallerInterface
{
    /**
     * (non-PHPdoc).
     *
     * @see \Mouf\Installer\PackageInstallerInterface::install()
     */
    public static function install(MoufManager $moufManager)
    {
        // Let's create the renderer
        RendererUtils::createPackageRenderer($moufManager, 'mouf/security.forgot-your-password');

        $configManager = $moufManager->getConfigManager();

        $constants = $configManager->getMergedConstants();

        if (!isset($constants['MAIL_FROM'])) {
            $configManager->registerConstant('MAIL_FROM', 'string', 'ro-reply@localhost', "The 'from' value used when sending mails.");
        }

        $configPhpConstants = $configManager->getDefinedConstants();
        $configPhpConstants['MAIL_FROM'] = 'ro-reply@localhost';
        $configManager->setDefinedConstants($configPhpConstants);

        // These instances are expected to exist when the installer is run.
        $defaultTranslationService = $moufManager->getInstanceDescriptor('defaultTranslationService');
        if ($moufManager->has('Mouf\\Security\\DAO\\SecurityUserDao')) {
            $Mouf_Security_DAO_SecurityUserDao = $moufManager->getInstanceDescriptor('Mouf\\Security\\DAO\\SecurityUserDao');
        } else {
            $Mouf_Security_DAO_SecurityUserDao = null;
        }
        $swiftMailer = $moufManager->getInstanceDescriptor('swiftMailer');
        $userService = $moufManager->getInstanceDescriptor('userService');
        $psr_errorLogLogger = $moufManager->getInstanceDescriptor('psr.errorLogLogger');
        $bootstrapTemplate = $moufManager->getInstanceDescriptor('bootstrapTemplate');
        $block_content = $moufManager->getInstanceDescriptor('block.content');
        $twigEnvironment = $moufManager->getInstanceDescriptor('twigEnvironment');
        $cascadingLanguageDetection = $moufManager->getInstanceDescriptor('cascadingLanguageDetection');

        // Let's create the instances.
        $Mouf_Security_Password_PasswordStrengthCheck = InstallUtils::getOrCreateInstance('Mouf\\Security\\Password\\PasswordStrengthCheck', 'Mouf\\Security\\Password\\PasswordStrengthCheck', $moufManager);
        $Mouf_Security_Password_ForgotYourPasswordService = InstallUtils::getOrCreateInstance('Mouf\\Security\\Password\\ForgotYourPasswordService', 'Mouf\\Security\\Password\\ForgotYourPasswordService', $moufManager);
        $Mouf_Security_Password_ForgotYourPasswordController = InstallUtils::getOrCreateInstance('Mouf\\Security\\Password\\ForgotYourPasswordController', 'Mouf\\Security\\Password\\ForgotYourPasswordController', $moufManager);
        $forgotYourPasswordMailTemplate = InstallUtils::getOrCreateInstance('forgotYourPasswordMailTemplate', 'TheCodingMachine\\Mail\\Template\\SwiftTwigMailTemplate', $moufManager);

        // Let's bind instances together.
        if (!$Mouf_Security_Password_PasswordStrengthCheck->getConstructorArgumentProperty('translationService')->isValueSet()) {
            $Mouf_Security_Password_PasswordStrengthCheck->getConstructorArgumentProperty('translationService')->setValue($defaultTranslationService);
        }
        if (!$Mouf_Security_Password_ForgotYourPasswordService->getConstructorArgumentProperty('forgetYourPasswordDao')->isValueSet()) {
            $Mouf_Security_Password_ForgotYourPasswordService->getConstructorArgumentProperty('forgetYourPasswordDao')->setValue($Mouf_Security_DAO_SecurityUserDao);
        }
        if (!$Mouf_Security_Password_ForgotYourPasswordService->getConstructorArgumentProperty('swiftMailer')->isValueSet()) {
            $Mouf_Security_Password_ForgotYourPasswordService->getConstructorArgumentProperty('swiftMailer')->setValue($swiftMailer);
        }
        if (!$Mouf_Security_Password_ForgotYourPasswordService->getConstructorArgumentProperty('mailTemplate')->isValueSet()) {
            $Mouf_Security_Password_ForgotYourPasswordService->getConstructorArgumentProperty('mailTemplate')->setValue($forgotYourPasswordMailTemplate);
        }
        if (!$Mouf_Security_Password_ForgotYourPasswordService->getConstructorArgumentProperty('userService')->isValueSet()) {
            $Mouf_Security_Password_ForgotYourPasswordService->getConstructorArgumentProperty('userService')->setValue($userService);
        }
        if (!$Mouf_Security_Password_ForgotYourPasswordController->getConstructorArgumentProperty('logger')->isValueSet()) {
            $Mouf_Security_Password_ForgotYourPasswordController->getConstructorArgumentProperty('logger')->setValue($psr_errorLogLogger);
        }
        if (!$Mouf_Security_Password_ForgotYourPasswordController->getConstructorArgumentProperty('template')->isValueSet()) {
            $Mouf_Security_Password_ForgotYourPasswordController->getConstructorArgumentProperty('template')->setValue($bootstrapTemplate);
        }
        if (!$Mouf_Security_Password_ForgotYourPasswordController->getConstructorArgumentProperty('content')->isValueSet()) {
            $Mouf_Security_Password_ForgotYourPasswordController->getConstructorArgumentProperty('content')->setValue($block_content);
        }
        if (!$Mouf_Security_Password_ForgotYourPasswordController->getConstructorArgumentProperty('twig')->isValueSet()) {
            $Mouf_Security_Password_ForgotYourPasswordController->getConstructorArgumentProperty('twig')->setValue($twigEnvironment);
        }
        if (!$Mouf_Security_Password_ForgotYourPasswordController->getConstructorArgumentProperty('forgotYourPasswordService')->isValueSet()) {
            $Mouf_Security_Password_ForgotYourPasswordController->getConstructorArgumentProperty('forgotYourPasswordService')->setValue($Mouf_Security_Password_ForgotYourPasswordService);
        }
        if (!$Mouf_Security_Password_ForgotYourPasswordController->getConstructorArgumentProperty('userService')->isValueSet()) {
            $Mouf_Security_Password_ForgotYourPasswordController->getConstructorArgumentProperty('userService')->setValue($userService);
        }
        if (!$Mouf_Security_Password_ForgotYourPasswordController->getConstructorArgumentProperty('passwordStrengthCheck')->isValueSet()) {
            $Mouf_Security_Password_ForgotYourPasswordController->getConstructorArgumentProperty('passwordStrengthCheck')->setValue($Mouf_Security_Password_PasswordStrengthCheck);
        }
        if (!$forgotYourPasswordMailTemplate->getConstructorArgumentProperty('twig_Environment')->isValueSet()) {
            $forgotYourPasswordMailTemplate->getConstructorArgumentProperty('twig_Environment')->setValue($twigEnvironment);
        }
        if (!$forgotYourPasswordMailTemplate->getConstructorArgumentProperty('twigPath')->isValueSet()) {
            $forgotYourPasswordMailTemplate->getConstructorArgumentProperty('twigPath')->setValue('vendor/mouf/security.forgot-your-password/src/templates/forgotyourpasswordmail.twig');
        }
        if (!$forgotYourPasswordMailTemplate->getSetterProperty('setFromAddresses')->isValueSet()) {
            $forgotYourPasswordMailTemplate->getSetterProperty('setFromAddresses')->setValue('MAIL_FROM');
            $forgotYourPasswordMailTemplate->getSetterProperty('setFromAddresses')->setOrigin('config');
        }

        if (!$moufManager->has('forgotYourPasswordTranslator')) {
            $forgotYourPasswordTranslator = InstallUtils::getOrCreateInstance('forgotYourPasswordTranslator', 'Mouf\\Utils\\I18n\\Fine\\Translator\\FileTranslator', $moufManager);

            if (!$forgotYourPasswordTranslator->getConstructorArgumentProperty('i18nMessagePath')->isValueSet()) {
                $forgotYourPasswordTranslator->getConstructorArgumentProperty('i18nMessagePath')->setValue('vendor/mouf/security.forgot-your-password/ressources/');
            }
            if (!$forgotYourPasswordTranslator->getConstructorArgumentProperty('languageDetection')->isValueSet()) {
                $forgotYourPasswordTranslator->getConstructorArgumentProperty('languageDetection')->setValue($cascadingLanguageDetection);
            }

            $translators = $defaultTranslationService->getProperty('translators')->getValue();
            $translators[] = $forgotYourPasswordTranslator;
            $defaultTranslationService->getProperty('translators')->setValue($translators);
        }

        // Let's rewrite the MoufComponents.php file to save the component
        $moufManager->rewriteMouf();
    }
}
