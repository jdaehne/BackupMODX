<?php
/**
 * systemSettings transport file for backupmodx extra
 *
 * Copyright 2018 by Jan Dähne <https://www.quadro-system.de>
 * Created on 11-20-2018
 *
 * @package backupmodx
 * @subpackage build
 */

if (! function_exists('stripPhpTags')) {
    function stripPhpTags($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<' . '?' . 'php', '', $o);
        $o = str_replace('?>', '', $o);
        $o = trim($o);
        return $o;
    }
}
/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $systemSettings */


$systemSettings = array();

$systemSettings[1] = $modx->newObject('modSystemSetting');
$systemSettings[1]->fromArray(array (
  'key' => 'backupmodx.excludes',
  'value' => '',
  'xtype' => 'textfield',
  'namespace' => 'backupmodx',
  'area' => 'Admin',
  'name' => 'Exclude Files / Folders',
  'description' => 'Files / Folders to exclude from the Backup. Comma separatet list. Assets Placeholder is available. Example: {assets_path}uploads/',
), '', true, true);
$systemSettings[2] = $modx->newObject('modSystemSetting');
$systemSettings[2]->fromArray(array (
  'key' => 'backupmodx.mysqldumpAlias',
  'value' => 'mysqldump',
  'xtype' => 'textfield',
  'namespace' => 'backupmodx',
  'area' => 'Database',
  'name' => 'Alias mysqldump',
  'description' => 'Alias for mysqldump.',
), '', true, true);
$systemSettings[3] = $modx->newObject('modSystemSetting');
$systemSettings[3]->fromArray(array (
  'key' => 'backupmodx.targetPath',
  'value' => '{core_path}components/backupmodx/backups/',
  'xtype' => 'textfield',
  'namespace' => 'backupmodx',
  'area' => 'Admin',
  'name' => 'Backup Target Path',
  'description' => 'The path to the folder to store the backups. Assets Placeholder is available. Example: {core_path}backups/',
), '', true, true);
$systemSettings[4] = $modx->newObject('modSystemSetting');
$systemSettings[4]->fromArray(array (
  'key' => 'backupmodx.zipAlias',
  'value' => 'zip',
  'xtype' => 'textfield',
  'namespace' => 'backupmodx',
  'area' => 'Files',
  'name' => 'Alias zip',
  'description' => 'Alias for zip compression.',
), '', true, true);
$systemSettings[5] = $modx->newObject('modSystemSetting');
$systemSettings[5]->fromArray(array (
  'key' => 'backupmodx.cronKey',
  'value' => '',
  'xtype' => 'textfield',
  'namespace' => 'backupmodx',
  'area' => 'Cron',
  'name' => 'Cron Security Key',
  'description' => 'Security-Key for cron scheduled Backups. Can be any string.',
), '', true, true);
$systemSettings[6] = $modx->newObject('modSystemSetting');
$systemSettings[6]->fromArray(array (
  'key' => 'backupmodx.cronFiles',
  'value' => 'true',
  'xtype' => 'combo-boolean',
  'namespace' => 'backupmodx',
  'area' => 'Cron',
  'name' => 'Backup Files',
  'description' => 'Whether or not to include MODX Files in Cron-Backup.',
), '', true, true);
$systemSettings[7] = $modx->newObject('modSystemSetting');
$systemSettings[7]->fromArray(array (
  'key' => 'backupmodx.cronDatabase',
  'value' => 'true',
  'xtype' => 'combo-boolean',
  'namespace' => 'backupmodx',
  'area' => 'Cron',
  'name' => 'Backup Database',
  'description' => 'Whether or not to include Database in Cron-Backup.',
), '', true, true);
$systemSettings[8] = $modx->newObject('modSystemSetting');
$systemSettings[8]->fromArray(array (
  'key' => 'backupmodx.cronNote',
  'value' => '',
  'xtype' => 'textarea',
  'namespace' => 'backupmodx',
  'area' => 'Cron',
  'name' => 'Note',
  'description' => 'Optional descriptional txt-file added to the Backup.',
), '', true, true);
$systemSettings[9] = $modx->newObject('modSystemSetting');
$systemSettings[9]->fromArray(array (
  'key' => 'backupmodx.cronEnable',
  'value' => 'false',
  'xtype' => 'combo-boolean',
  'namespace' => 'backupmodx',
  'area' => 'Cron',
  'name' => 'Enable Cron',
  'description' => 'Enable or Disable Cron.',
), '', true, true);
$systemSettings[10] = $modx->newObject('modSystemSetting');
$systemSettings[10]->fromArray(array (
  'key' => 'backupmodx.mysqlAlias',
  'value' => 'mysql',
  'xtype' => 'textfield',
  'namespace' => 'backupmodx',
  'area' => 'Database',
  'name' => 'Alias mysql',
  'description' => 'Alias for mysql.',
), '', true, true);
$systemSettings[11] = $modx->newObject('modSystemSetting');
$systemSettings[11]->fromArray(array (
  'key' => 'backupmodx.cronMaxDatabase',
  'value' => '10',
  'xtype' => 'textfield',
  'namespace' => 'backupmodx',
  'area' => 'Cron',
  'name' => 'Max Database',
  'description' => 'Maximum stored Backups of Database.',
), '', true, true);
$systemSettings[12] = $modx->newObject('modSystemSetting');
$systemSettings[12]->fromArray(array (
  'key' => 'backupmodx.cronMaxFiles',
  'value' => '5',
  'xtype' => 'textfield',
  'namespace' => 'backupmodx',
  'area' => 'Cron',
  'name' => 'Max Files',
  'description' => 'Maximum stored Backups of Files.',
), '', true, true);
return $systemSettings;
