<?php
/**
 * BackupMODX
 *
 * Download backup files
 *
 *
 * @package backupmodx
 */


// Disable Core-Protection
// define('MODX_REQP', false);

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';


// Load Class
$modelPath = $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/backupmodx/model/backupmodx/';
$modx->loadClass('BackupMODX', $modelPath, true, true);

$backupMODX = new BackupMODX($modx, $config = array());

// Get file infos
$filename = $_GET["file"];
$folder = $_GET["folder"];

$file = $backupMODX->getFile($folder, $filename);


$mimeType = mime_content_type($file);

// send the right headers
header("Content-disposition: attachment; filename=" . basename($file));
header("Content-Type: " . $mimeType);
header("Content-Length: " . filesize($file));

// dump the file and stop the script
$chunksize = 1*(1024*1024); // how many bytes per chunk
$buffer = '';
$handle = fopen($file, 'rb');

if ($handle === false) {
    return false;
}
while (!feof($handle)) {
    $buffer = fread($handle, $chunksize);
    print $buffer;
}

return fclose($handle);
