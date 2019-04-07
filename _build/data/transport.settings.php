<?php
/**
 * systemSettings transport file for backupmodx extra
 *
 * Copyright 2018 by Jan Dähne <https://www.quadro-system.de>
 * Created on 12-29-2018
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
  'name' => 'Exclude Files / Folders',
  'description' => 'Files / Folders to exclude from the Backup. Comma separatet list. Assets Placeholder is available. Example: {assets_path}uploads/',
  'namespace' => 'backupmodx',
  'xtype' => 'textfield',
  'value' => '',
  'area' => 'Admin',
), '', true, true);
$systemSettings[2] = $modx->newObject('modSystemSetting');
$systemSettings[2]->fromArray(array (
  'key' => 'backupmodx.targetPath',
  'name' => 'Backup Target Path',
  'description' => 'The path to the folder to store the backups. Assets Placeholder is available. Example: {core_path}backups/',
  'namespace' => 'backupmodx',
  'xtype' => 'textfield',
  'value' => '{core_path}components/backupmodx/backups/',
  'area' => 'Admin',
), '', true, true);
$systemSettings[3] = $modx->newObject('modSystemSetting');
$systemSettings[3]->fromArray(array (
  'key' => 'backupmodx.cronKey',
  'name' => 'Cron Security Key',
  'description' => 'Security-Key for cron scheduled Backups. Can be any string.',
  'namespace' => 'backupmodx',
  'xtype' => 'textfield',
  'value' => '',
  'area' => 'Cron',
), '', true, true);
$systemSettings[4] = $modx->newObject('modSystemSetting');
$systemSettings[4]->fromArray(array (
  'key' => 'backupmodx.mysqldumpAlias',
  'name' => 'Alias mysqldump',
  'description' => 'Alias for mysqldump.',
  'namespace' => 'backupmodx',
  'xtype' => 'textfield',
  'value' => 'mysqldump',
  'area' => 'Database',
), '', true, true);
$systemSettings[5] = $modx->newObject('modSystemSetting');
$systemSettings[5]->fromArray(array (
  'key' => 'backupmodx.zipAlias',
  'name' => 'Alias zip',
  'description' => 'Alias for zip compression.',
  'namespace' => 'backupmodx',
  'xtype' => 'textfield',
  'value' => 'zip',
  'area' => 'Files',
), '', true, true);
$systemSettings[6] = $modx->newObject('modSystemSetting');
$systemSettings[6]->fromArray(array (
  'key' => 'backupmodx.cronFiles',
  'name' => 'Backup Files',
  'description' => 'Whether or not to include MODX Files in Cron-Backup.',
  'namespace' => 'backupmodx',
  'xtype' => 'combo-boolean',
  'value' => 'true',
  'area' => 'Cron',
), '', true, true);
$systemSettings[7] = $modx->newObject('modSystemSetting');
$systemSettings[7]->fromArray(array (
  'key' => 'backupmodx.cronDatabase',
  'name' => 'Backup Database',
  'description' => 'Whether or not to include Database in Cron-Backup.',
  'namespace' => 'backupmodx',
  'xtype' => 'combo-boolean',
  'value' => 'true',
  'area' => 'Cron',
), '', true, true);
$systemSettings[8] = $modx->newObject('modSystemSetting');
$systemSettings[8]->fromArray(array (
  'key' => 'backupmodx.cronNote',
  'name' => 'Note',
  'description' => 'Optional descriptional txt-file added to the Backup.',
  'namespace' => 'backupmodx',
  'xtype' => 'textarea',
  'value' => '',
  'area' => 'Cron',
), '', true, true);
$systemSettings[9] = $modx->newObject('modSystemSetting');
$systemSettings[9]->fromArray(array (
  'key' => 'backupmodx.cronEnable',
  'name' => 'Enable Cron',
  'description' => 'Enable or Disable Cron.',
  'namespace' => 'backupmodx',
  'xtype' => 'combo-boolean',
  'value' => 'false',
  'area' => 'Cron',
), '', true, true);
$systemSettings[10] = $modx->newObject('modSystemSetting');
$systemSettings[10]->fromArray(array (
  'key' => 'backupmodx.cronMaxDatabase',
  'name' => 'Max Databases',
  'description' => 'Maximum stored Backups of Database.',
  'namespace' => 'backupmodx',
  'xtype' => 'textfield',
  'value' => '10',
  'area' => 'Cron',
), '', true, true);
$systemSettings[11] = $modx->newObject('modSystemSetting');
$systemSettings[11]->fromArray(array (
  'key' => 'backupmodx.cronMaxFiles',
  'name' => 'Max Files',
  'description' => 'Maximum stored Backups of Files.',
  'namespace' => 'backupmodx',
  'xtype' => 'textfield',
  'value' => '5',
  'area' => 'Cron',
), '', true, true);
$systemSettings[12] = $modx->newObject('modSystemSetting');
$systemSettings[12]->fromArray(array (
  'key' => 'backupmodx.mysqlAlias',
  'name' => 'Alias mysql',
  'description' => 'Alias for mysql.',
  'namespace' => 'backupmodx',
  'xtype' => 'textfield',
  'value' => 'mysql',
  'area' => 'Database',
), '', true, true);
$systemSettings[13] = $modx->newObject('modSystemSetting');
$systemSettings[13]->fromArray(array (
  'key' => 'backupmodx.groups',
  'name' => 'Groups',
  'description' => 'Group or comma separated list of Groups. This Groups will have access to the widget.',
  'namespace' => 'backupmodx',
  'xtype' => 'textfield',
  'value' => 'Administrator',
  'area' => 'Admin',
), '', true, true);
return $systemSettings;
