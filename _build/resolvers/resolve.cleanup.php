<?php
/**
 * Resolve setup options
 *
 * @package backupmodx
 * @subpackage build
 *
 * @var array $options
 * @var xPDOObject $object
 */
$success = false;

if ($object->xpdo) {
    /** @var xPDO $modx */
    $modx =& $object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:

            $paths = array(
                'assets' => $modx->getOption('assets_path', null, MODX_ASSETS_PATH),
                'core' => $modx->getOption('core_path', null, MODX_CORE_PATH),
            );

            $cleanup = array(
                'assets' => array(
                    'components/backupmodx/css/widget.css',
                    'components/backupmodx/cron',
                    'components/backupmodx/download'
                ),
                'core' => array(
                    'components/backupmodx/docs/tutorial.html',
                    'components/backupmodx/elements/chunks',
                    'components/backupmodx/elements/snippets'
                )
            );

            if (!function_exists('recursiveRemoveFolder')) {
                function recursiveRemoveFolder($dir)
                {
                    $files = array_diff(scandir($dir), array('.', '..'));
                    foreach ($files as $file) {
                        (is_dir("$dir/$file")) ? recursiveRemoveFolder($dir . '/' . $file) : unlink($dir . '/' . $file);
                    }
                    return rmdir($dir);
                }
            }


            $removedFiles = array();
            $removedFolders = array();

            foreach ($cleanup as $folder => $files) {
                foreach ($files as $file) {
                    $legacyFile = $paths[$folder].$file;
                    if (file_exists($legacyFile)) {
                        if (is_dir($legacyFile)) {
                            recursiveRemoveFolder($legacyFile);
                            $removedFolders++;
                        } else {
                            unlink($legacyFile);
                            $removedFiles++;
                        }
                    }
                }
            }

            if ($removedFolders || $removedFiles) {
                $modx->log(xPDO::LOG_LEVEL_ERROR, 'Removed ' . $removedFiles . ' legacy files and ' . $removedFolders . ' legacy folders of BackupMODX 2.x.');
            }

            $success = true;
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $success = true;
            break;
    }
}
return $success;
