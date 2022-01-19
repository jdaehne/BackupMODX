<?php
/**
 * BackupMODX cron connector
 *
 * @package backupmodx
 * @subpackage connector
 *
 * @var modX $modx
 */

use BackupMODX\Backup\Backup;

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('backupmodx.core_path', null, $modx->getOption('core_path') . 'components/backupmodx/');
/** @var BackupMODX $backupmodx */
$backupmodx = $modx->getService('backupmodx', 'BackupMODX', $corePath . 'model/backupmodx/', array(
    'core_path' => $corePath
));

// Check if Cron is enabled
if ($backupmodx->getOption('cronEnable') != true) {
    $msg = 'Access Denied. Cron is disabled in MODX system-settings.';
    if (php_sapi_name() == 'cli') {
        fwrite(STDERR, $msg . "\n");
        exit(1);
    } else {
        exit($msg);
    }

}

// Check Key for permission
if (php_sapi_name() == 'cli') {
    if (!isset($_SERVER['argv'][1]) || $_SERVER['argv'][1] != $backupmodx->getOption('cronKey')) {
        fwrite(STDERR,
            'Access Denied. Cron-Security-Key is not correct.' . "\n" .
            'Usage: ' . basename($argv[0]) . ' <key>' . "\n");
        exit(1);
    }
} else {
    if (!isset($_REQUEST['key']) || $_REQUEST['key'] != $backupmodx->getOption('cronKey')) {
        exit ('Access Denied. Cron-Security-Key is not correct.');
    }
}

// System Settings
$files = $backupmodx->getOption('cronFiles');
$database = $backupmodx->getOption('cronDatabase');
$note = $backupmodx->getOption('cronNote');
$maxDatabase = $backupmodx->getOption('cronMaxDatabase', array(), 10);
$maxFiles = $backupmodx->getOption('cronMaxFiles', array(), 5);

// Backup
$backup = new Backup($modx);
$result = $backup->backup($files, $database, $note);

// Clean old Backups
$backupmodx->cleanCronBackups($maxDatabase, $maxFiles);

if (is_array($result)) {
    if (php_sapi_name() == 'cli') {
        exit(0);
    } else {
        @session_write_close();
        exit;
    }
} else {
    if (php_sapi_name() == 'cli') {
        fwrite(STDERR, $result . "\n");
        exit(1);
    } else {
        @session_write_close();
        exit($result);
    }
}

// @TODO - Optional Call to Backup-Center to send Files - Send optional E-mail
