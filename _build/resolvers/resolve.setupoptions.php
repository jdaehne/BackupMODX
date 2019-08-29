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
            /** @var modSystemSetting $setting */
            $setting = $modx->getObject('modSystemSetting', array(
                'key' => 'backupmodx.targetPath'
            ));
            if ($setting != null) {
                $setting->set('value', $modx->getOption('targetPath', $options, '{core_path}backup/'));
                $setting->save();
            } else {
                $modx->log(xPDO::LOG_LEVEL_ERROR, 'The backupmodx.targetPath system setting was not found and can\'t be updated.');
            }

            $setting = $modx->getObject('modSystemSetting', array(
                'key' => 'backupmodx.cronKey'
            ));
            if ($setting != null) {
                $setting->set('value', $modx->getOption('cronKey', $options, substr(md5(openssl_random_pseudo_bytes(20)), -12)));
                $setting->save();
            } else {
                $modx->log(xPDO::LOG_LEVEL_ERROR, 'The backupmodx.cronKey system setting was not found and can\'t be updated.');
            }

            $success = true;
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $success = true;
            break;
    }
}
return $success;
