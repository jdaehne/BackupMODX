<?php
/**
 * chunks transport file for backupmodx extra
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
/* @var xPDOObject[] $chunks */


$chunks = array();

$chunks[1] = $modx->newObject('modChunk');
$chunks[1]->fromArray(array (
  'id' => 1,
  'name' => 'backupMODXWidget',
), '', true, true);
$chunks[1]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/backupmodxwidget.chunk.html'));

return $chunks;
