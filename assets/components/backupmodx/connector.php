<?php
/**
 * BackupMODX connector
 *
 * @package backupmodx
 * @subpackage connector
 *
 * @var modX $modx
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('backupmodx.core_path', null, $modx->getOption('core_path') . 'components/backupmodx/');
/** @var BackupMODX $backupmodx */
$backupmodx = $modx->getService('backupmodx', 'BackupMODX', $corePath . 'model/backupmodx/', array(
    'core_path' => $corePath
));

// Handle request
$modx->request->handleRequest(array(
    'processors_path' => $backupmodx->getOption('processorsPath'),
    'location' => ''
));
