<?php
// Contents of assets/components/tutorial/connector.php

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

// Load backupMODX Class
$modelPath = $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/backupmodx/model/backupmodx/';
$modx->loadClass('BackupMODX', $modelPath, true, true);

// timeouts
set_time_limit(0);
ini_set('max_execution_time', 0);

define('AJAX_PATH', $modx->getOption('core_path').'components/backupmodx/processors/');

$_SERVER['HTTP_MODAUTH'] = $modx->user->getUserToken('mgr');

/* handle request */
$modx->request->handleRequest(array(
    'processors_path' => AJAX_PATH,
    'location' => '',
));
