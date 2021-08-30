<?php
/**
 * Resolve cleanup
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
            $c = $modx->newQuery('transport.modTransportPackage');
            $c->where(
                array(
                    'workspace' => 1,
                    "(SELECT
            `signature`
            FROM {$modx->getTableName('transport.modTransportPackage')} AS `latestPackage`
            WHERE `latestPackage`.`package_name` = `modTransportPackage`.`package_name`
            ORDER BY
                `latestPackage`.`version_major` DESC,
                `latestPackage`.`version_minor` DESC,
                `latestPackage`.`version_patch` DESC,
                IF(`release` = '' OR `release` = 'ga' OR `release` = 'pl','z',`release`) DESC,
                `latestPackage`.`release_index` DESC
                LIMIT 1,1) = `modTransportPackage`.`signature`",
                )
            );
            $c->where(
                array(
                    'modTransportPackage.signature:LIKE' => $options['namespace'] . '-%',
                    'modTransportPackage.installed:IS NOT' => null
                )
            );
            $c->limit(1);

            /** @var modTransportPackage $oldPackage */
            $oldPackage = $modx->getObject('transport.modTransportPackage', $c);

            $oldVersion = '';
            if ($oldPackage) {
                $oldVersion = $oldPackage->get('version_major') .
                    '.' . $oldPackage->get('version_minor') .
                    '.' . $oldPackage->get('version_patch') .
                    '-' . $oldPackage->get('release');
            }

            if ($oldPackage && $oldPackage->compareVersion('3.0.0-rc1', '>')) {
                // Cleanup Folders
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
                        'components/backupmodx/docs/changelog.txt',
                        'components/backupmodx/docs/license.txt',
                        'components/backupmodx/docs/readme.txt',
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

                $countFiles = 0;
                $countFolders = 0;

                foreach ($cleanup as $folder => $files) {
                    foreach ($files as $file) {
                        $legacyFile = $paths[$folder] . $file;
                        if (file_exists($legacyFile)) {
                            if (is_dir($legacyFile)) {
                                recursiveRemoveFolder($legacyFile);
                                $countFolders++;
                            } else {
                                unlink($legacyFile);
                                $countFiles++;
                            }
                        }
                    }
                }

                if ($countFolders || $countFiles) {
                    $modx->log(xPDO::LOG_LEVEL_INFO, 'Removed ' . $countFiles . ' legacy files and ' . $countFolders . ' legacy folders of BackupMODX 2.x.');
                }

                // Cleanup old widget
                /** @var modDashboardWidget $oldWidget */
                $oldWidget = $modx->getObject('modDashboardWidget', array(
                    'content' => 'BackupMODXWidget',
                    'type' => 'snippet'
                ));
                /** @var modDashboardWidget $newWidget */
                $newWidget = $modx->getObject('modDashboardWidget', array(
                    'name' => 'backupmodx.widget'
                ));

                $countPlacements = 0;

                if ($oldWidget && $newWidget) {
                    /** @var modDashboardWidgetPlacement[] $placements */
                    $placements = $modx->getIterator('modDashboardWidgetPlacement', array('widget' => $oldWidget->get('id')));
                    foreach ($placements as $placement) {
                        $newPlacement = $modx->newObject('modDashboardWidgetPlacement');
                        $newPlacement->set('dashboard', $placement->get('dashboard'));
                        $newPlacement->set('widget', $newWidget->get('id'));
                        $newPlacement->set('rank', $placement->get('rank'));
                        $newPlacement->save();
                        $countPlacements++;
                    }
                }
                if ($oldWidget) {
                    $oldWidget->remove();
                }

                if ($countPlacements) {
                    $modx->log(xPDO::LOG_LEVEL_INFO, 'Changed ' . $countPlacements . ' legacy widget placements and removed the legacy widget.');
                }

                // Coyp old settings
                $copySettings = array(
                    'excludes' => 'excludeFolders'
                );

                $countSettings = 0;

                foreach ($copySettings as $oldKey => $newKey) {
                    /** @var modSystemSetting $oldSetting */
                    $oldSetting = $modx->getObject('modSystemSetting', array(
                        'key' => $options['namespace'] . '.' . $oldKey
                    ));
                    if ($oldSetting) {
                        /** @var modSystemSetting $setting */
                        $newSetting = $modx->getObject('modSystemSetting', array(
                            'key' => $options['namespace'] . '.' . $newKey
                        ));
                        if ($newSetting) {
                            $newSetting->set('value', $oldSetting->get('value'));
                            $newSetting->save();
                            $countSettings++;
                        }
                    }
                }

                if ($countSettings) {
                    $modx->log(xPDO::LOG_LEVEL_INFO, 'Copied ' . $countSettings . ' legacy system setting values to new system settings.');
                }

                // Cleanup old settings
                $oldSettings = array(
                    'mysqlAlias', 'mysqldumpAlias', 'zipAlias', 'excludes'
                );

                $countSettings = 0;

                foreach ($oldSettings as $oldKey) {
                    /** @var modSystemSetting $oldSetting */
                    $oldSetting = $modx->getObject('modSystemSetting', array(
                        'key' => $options['namespace'] . '.' . $oldKey
                    ));
                    if ($oldSetting) {
                        $oldSetting->remove();
                        $countSettings++;
                    }
                }

                if ($countSettings) {
                    $modx->log(xPDO::LOG_LEVEL_INFO, 'Removed ' . $countSettings . ' legacy system settings.');
                }
            }

            $success = true;
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $success = true;
            break;
    }
}
return $success;
