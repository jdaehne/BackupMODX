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
);

return $properties;

