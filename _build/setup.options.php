<?php
/**
 * Setup options
 *
 * @package backupmodx
 * @subpackage build
 *
 * @var modX $modx
 * @var array $options
 */

// Defaults
$defaults = array(
    'cronKey' => substr(md5(openssl_random_pseudo_bytes(20)), -12),
    'targetPath' => '{core_path}backup/',
);

$output = '<style type="text/css">
    #modx-setupoptions-panel { display: none; }
    #modx-setupoptions-form p { margin-bottom: 10px; }
    #modx-setupoptions-form h2 { margin-bottom: 15px; }
</style>';

$values = array();
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        $output .= '<h2>Install BackupMODX</h2>
        <p>BackupMODX will be installed. Please review the installation options carefully.</p><br>';

        $output .= '<div style="position: relative">
                        <label for="target_path">Backup Target Path:</label>
                        <input type="text" name="targetPath" id="target_path" width="450" value="' . $defaults['targetPath'] . '" style="font-size: 13px; padding: 5px; width: calc(100% - 10px); height: 32px; margin-bottom: 10px" />
                    </div>';
        $output .= '<div style="position: relative">
                        <label for="cron_key">Cron Security Key:</label>
                        <input type="text" name="cronKey" id="cron_key" width="450" value="' . $defaults['cronKey'] . '" style="font-size: 13px; padding: 5px; width: calc(100% - 10px); height: 32px; margin-bottom: 10px" />
                    </div>';
        break;
    case xPDOTransport::ACTION_UPGRADE:
        $setting = $modx->getObject('modSystemSetting', array('key' => 'backupmodx.cronKey'));
        $values['cronKey'] = ($setting) ? $setting->get('value') : $defaults['cronKey'];
        unset($setting);

        $setting = $modx->getObject('modSystemSetting', array('key' => 'backupmodx.targetPath'));
        $values['targetPath'] = ($setting) ? $setting->get('value') : $defaults['targetPath'];
        unset($setting);

        $output .= '<h2>Upgrade BackupMODX</h2>
        <p>BackupMODX will be upgraded. Please review the installation options carefully.</p><br>';

        $output .= '<div style="position: relative">
                        <label for="target_path">Backup Target Path:</label>
                        <input type="text" name="targetPath" id="target_path" width="450" value="' . $values['targetPath'] . '" style="font-size: 13px; padding: 5px; width: calc(100% - 10px); height: 32px; margin-bottom: 10px" />
                    </div>';
        $output .= '<div style="position: relative">
                        <label for="cron_key">Cron Security Key:</label>
                        <input type="text" name="cronKey" id="cron_key" width="450" value="' . $values['cronKey'] . '" style="font-size: 13px; padding: 5px; width: calc(100% - 10px); height: 32px; margin-bottom: 10px" />
                    </div>';
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

return $output;
