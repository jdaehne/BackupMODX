<?php
/**
 * chunks transport file for backupmodx extra
 *
 * Copyright 2015 by Quadro - Jan Dähne info@quadro-system.de
 * Created on 12-16-2015
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
/* @var xPDOObject[] $chunks */


$chunks = array();

$chunks[1] = $modx->newObject('modChunk');
$chunks[1]->fromArray(array (
  'id' => 1,
  'property_preprocess' => false,
  'name' => 'BackupMODXSnippetScriptSource',
  'description' => 'Source for MODX Backup script',
  'properties' => 
  array (
  ),
), '', true, true);
$chunks[1]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/backupmodxsnippetscriptsource.chunk.html'));

return $chunks;
