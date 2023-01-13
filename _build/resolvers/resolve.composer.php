<?php
/**
 * Resolve Composer dependencies
 *
 * @package consentfriend
 * @subpackage build
 *
 * @var array $options
 * @var xPDOObject $object
 */

use Composer\Console\HtmlOutputFormatter;
use Composer\Factory;
use Composer\Installer;
use Composer\IO\BufferIO;
use Symfony\Component\Console\Output\StreamOutput;

$success = true;
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            /** @var modX $modx */
            $modx = &$object->xpdo;

            $modx->log(modX::LOG_LEVEL_INFO, 'Installing/updating dependencies, this may take some time...');

            $path = MODX_CORE_PATH . "components/{$options['namespace']}/";

            // Set Composer environment variables
            putenv("COMPOSER={$path}composer.json");
            putenv("COMPOSER_HOME={$path}.composer");
            putenv("COMPOSER_VENDOR_DIR={$path}vendor/");

            // Change the path to the package namespace folder, to prevent autoloading other namespaces
            chdir($path);

            require "phar://{$path}composer.phar/vendor/autoload.php";

            // Run Composer without proc_open/exec
            $io = new BufferIO('', StreamOutput::VERBOSITY_NORMAL, new HtmlOutputFormatter());
            $composer = Factory::create($io);
            $install = Installer::create($io, $composer);
            $install
                ->setPreferDist(true)
                ->setDevMode(false)
                ->setOptimizeAutoloader(true)
                ->setUpdate(false)
                ->setPreferStable(true);

            try {
                $install->run();
            } catch (Exception $e) {
                $modx->log(modX::LOG_LEVEL_ERROR, get_class($e) . ' installing dependencies: ' . $e->getMessage());
                echo get_class($e) . ': ' . $e->getMessage() . "\n";
                $success = false;
            }

            $output = $io->getOutput();
            $output = nl2br(trim($output));
            $modx->log(modX::LOG_LEVEL_INFO, $output);
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}
return $success;
