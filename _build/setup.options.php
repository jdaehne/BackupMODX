<?php
/**
 * Setup options
 *
 * @package backupmodx
 * @subpackage build
 *
 * @var array $options
 * @var modX $modx
 */

// Defaults
$defaults = array(
    'cronKey' => substr(md5(openssl_random_pseudo_bytes(20)), -12),
    'targetPath' => '{core_path}backup/',
);

$output = '';
$values = array();
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        $output .= '<h2>Install BackupMODX</h2>
        <p>BackupMODX will be installed. Please review the install options carefully.</p><br>';

        $output .= '<div id="target">
                        <label for="target_path">Backup Target Path:</label>
                        <input type="text" name="targetPath" id="target_path" width="450" value="' . $defaults['targetPath'] . '" />
                    </div>
                    <br><br>';

        $output .= '<div id="calendar">
                        <label for="cron_key">Cron Security Key:</label>
                        <input type="text" name="cronKey" id="cron_key" width="450" value="' . $defaults['cronKey'] . '" />
                    </div>
                    <br><br>';

        break;
    case xPDOTransport::ACTION_UPGRADE:
        $setting = $modx->getObject('modSystemSetting', array('key' => 'backupmodx.cronKey'));
        $values['cronKey'] = ($setting) ? $setting->get('value') : $defaults['cronKey'];
        unset($setting);

        $setting = $modx->getObject('modSystemSetting', array('key' => 'backupmodx.targetPath'));
        $values['targetPath'] = ($setting) ? $setting->get('value') : $defaults['targetPath'];
        unset($setting);

        $output .= '<h2>Upgrade BackupMODX</h2>
        <p>BackupMODX will be upgraded. Please review the install options carefully.</p><br>';

        $output .= '<div id="target">
                        <label for="target_path">Backup Target Path:</label>
                        <input type="text" name="targetPath" id="target_path" width="450" value="' . $values['targetPath'] . '" />
                    </div>
                    <br><br>';

        $output .= '<div id="calendar">
                        <label for="cron_key">Cron Security Key:</label>
                        <input type="text" name="cronKey" id="cron_key" width="450" value="' . $values['cronKey'] . '" />
                    </div>
                    <br><br>';

        break;
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

return $output;
