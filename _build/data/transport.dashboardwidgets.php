<?php
/**
 * dashboardWidgets transport file for BackupMODX extra
 *
 * Copyright 2015 by Quadro - Jan Dähne info@quadro-system.de
 * Created on 12-16-2015
 *
 * @package upgrademodx
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
/* @var xPDOObject[] $dashboardWidgets */
$dashboardWidgets = array();
$dashboardWidgets[1] = $modx->newObject('modDashboardWidget');
$dashboardWidgets[1]->fromArray(array (
  'id' => 1,
  'name' => 'Backup MODX',
  'description' => 'Backup MODX Widget',
  'type' => 'snippet',
  'content' => 'BackupMODXWidget',
  'namespace' => 'backupmodx',
  'lexicon' => 'backupmodx:default',
  'size' => 'half',
  'name_trans' => 'Backup MODX',
  'description_trans' => 'Backup MODX Widget',
), '', true, true);
return $dashboardWidgets;