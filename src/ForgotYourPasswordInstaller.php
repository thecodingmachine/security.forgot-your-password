<?php
namespace Mouf\Security\Password;

use Mouf\Html\Renderer\RendererUtils;
use Mouf\Installer\PackageInstallerInterface;
use Mouf\MoufManager;

class ForgotYourPasswordInstaller implements PackageInstallerInterface {

    /**
     * (non-PHPdoc)
     * @see \Mouf\Installer\PackageInstallerInterface::install()
     */
    public static function install(MoufManager $moufManager) {
        // Let's create the renderer
        RendererUtils::createPackageRenderer($moufManager, "mouf/security.forgot-your-password");

        // Let's rewrite the MoufComponents.php file to save the component
        $moufManager->rewriteMouf();
    }
}
