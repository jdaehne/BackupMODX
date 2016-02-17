<?php
/**
 * Properties file for BackupMODXWidget snippet
 *
 * Copyright 2015 by Quadro - Jan Dähne info@quadro-system.de
 * Created on 12-16-2015
 *
 * @package backupmodx
 * @subpackage build
 */




$properties = array (
  'excludes' => 
  array (
    'name' => 'excludes',
    'desc' => 'file/folder, or comma-separated list of files/folders who will be excluded of the backup. Using file-path from the root. Example: /html/assets,/html/manager',
    'type' => 'textarea',
    'options' => 
    array (
    ),
    'value' => '',
    'lexicon' => NULL,
    'area' => '',
  ),
  'groups' => 
  array (
    'name' => 'groups',
    'desc' => 'group, or comma-separated list of groups, who will see the widget',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'Administrator',
    'lexicon' => 'backupmodx:properties',
    'area' => '',
  ),
  'tarAlias' => 
  array (
    'name' => 'tarAlias',
    'desc' => 'some server need an alias for tar like "/bin/pktar"',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => 'tar',
    'lexicon' => NULL,
    'area' => '',
  ),
  'targetPath' => 
  array (
    'name' => 'targetPath',
    'desc' => 'Directory of the backup files. No trailing slash! You can use {assets_path} as a placeholder for the assets folder. Example: {assets_path}folder',
    'type' => 'textfield',
    'options' => 
    array (
    ),
    'value' => '{assets_path}',
    'lexicon' => NULL,
    'area' => '',
  ),
);

return $properties;

