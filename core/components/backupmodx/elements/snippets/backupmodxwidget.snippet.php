<?php
/**
 * BackupMODX
 *
 * MODX Backup Dashboard Widget
 *
 *
 * @package backupmodx
 */
 

// Returns an empty string if user shouldn't see the widget
$groups = $modx->getOption('groups', $scriptProperties, 'Administrator', true);
if (strpos($groups, ',') !== false) {
	$groups = explode(',', $groups);
}
if (!$modx->user->isMember($groups)) {
	return '';
} 


// Load Class
$modelPath = $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/backupmodx/model/backupmodx/';
$assetsUrl = $modx->getOption('assets_url', null, MODX_ASSETS_URL);
$assetsUrl = substr($assetsUrl, 1);
$modx->loadClass('BackupMODX', $modelPath, true, true);


$backupMODX = new BackupMODX($modx, $config = array());


if (isset($_POST['removebackupmodx'])) {

    $backup = $_SESSION['tmpActiveBackup'];
    $backupMODX->removeBackup($backup['filename']);
    
    unset($_SESSION['tmpActiveBackup']);
}

if (isset($_POST['restorebackupmodx'])) {

    $database = $_POST['database'];
    $restorebackup = $backupMODX->restoreBackup($database);
    
    if ($restorebackup === true) {
        echo 'Backup was restored.';
        return;
    }
}

return $modx->getChunk('backupMODXWidget', array(
    'backup' => is_array($_SESSION['tmpActiveBackup'])? json_encode($_SESSION['tmpActiveBackup']) : '',
    'assetsurl' => $assetsUrl,
));
