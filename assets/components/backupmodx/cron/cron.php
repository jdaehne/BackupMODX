<?php
/**
 * BackupMODX
 *
 * Cron to backup MODX
 *
 *
 * @package backupmodx
 */

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';


// Check if Cron is enabled
if ($modx->getOption('backupmodx.cronEnable', null) != true) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Acces Denied. Cron is disabled in MODX system-settings.',
    ));
    return;
}

// Check Key for permission
if ($_REQUEST['key'] != $modx->getOption('backupmodx.cronKey', null) or empty($_REQUEST['key'])) {
    if ($_SERVER['argv'][1] != $modx->getOption('backupmodx.cronKey', null) or empty($_SERVER['argv'][1])) {
        echo json_encode(array(
            'success' => false,
            'message' => 'Acces Denied. Cron-Security-Key is not correct.',
        ));
        return;
    }
}


// Load Class
$modelPath = $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/backupmodx/model/backupmodx/';
$modx->loadClass('BackupMODX', $modelPath, true, true);

$backupMODX = new BackupMODX($modx, $config = array());

// System Settings
$files = $modx->getOption('backupmodx.cronFiles', null);
$database = $modx->getOption('backupmodx.cronDatabase', null);
$note = $modx->getOption('backupmodx.cronNote', null);
$maxDatabase = $modx->getOption('backupmodx.cronMaxDatabase', null, 10, true);
$maxFiles = $modx->getOption('backupmodx.cronMaxFiles', null, 5, true);


// Backup
$backup = $backupMODX->backupMODX($files,$database, $note);

// Clean old Backups
$backupMODX->cleanCronBackups($maxDatabase, $maxFiles);


// To Do:
// - Optional Call to Backup-Center to send Files
// - Send optional E-mail
