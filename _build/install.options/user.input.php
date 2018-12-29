<?php

/**
 * Script to interact with user during backupmodx package install
 *
 * Copyright 2018 by Jan Dähne <https://www.quadro-system.de>
 * Created on 10-19-2018
 *
 * backupmodx is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * backupmodx is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * backupmodx; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package backupmodx
 */

/**
 * Description: Script to interact with user during backupmodx package install
 * @package backupmodx
 * @subpackage build
 */

/* The return value from this script should be an HTML form (minus the
 * <form> tags and submit button) in a single string.
 *
 * The form will be shown to the user during install
 *
 * This example presents an HTML form to the user with two input fields
 * (you can have as many as you like).
 *
 * The user's entries in the form's input field(s) will be available
 * in any php resolvers with $modx->getOption('field_name', $options, 'default_value').
 *
 * You can use the value(s) to set system settings, snippet properties,
 * chunk content, etc. based on the user's preferences.
 *
 * One common use is to use a checkbox and ask the
 * user if they would like to install a resource for your
 * component (usually used only on install, not upgrade).
 */

/* This is an example. Modify it to meet your needs.
 * The user's input would be available in a resolver like this:
 *
 * $changeSiteName = (! empty($modx->getOption('change_sitename', $options, ''));
 * $siteName = $modx->getOption('sitename', $options, '').
 *
 * */

 $setting = $modx->getObject('modSystemSetting',array('key' => 'backupmodx.cronKey'));
 if ($setting != null) {
     $values['cronKey'] = $setting->get('value');
 }else {
     $values['cronKey'] = substr(md5(openssl_random_pseudo_bytes(20)),-12);
 }
 unset($setting);

 $setting = $modx->getObject('modSystemSetting',array('key' => 'backupmodx.targetPath'));
 if ($setting != null) {
     $values['targetPath'] = $setting->get('value');
 }else {
     $values['targetPath'] = '{core_path}components/backupmodx/backups/';
 }
 unset($setting);


  $output = '<style>.field_desc { color: #A0A0A0; font-size: 11px; font-style: italic; }</style>
  <div style="padding-bottom: 1rem;">
     <label for="apikey-server">Backup Target Path:</label>
     <input type="text" name="targetPath" id="targetpath" value="'.$values['targetPath'].'" align="left" size="40" maxlength="60" />
     <div class="field_desc">The path to the folder to store the backups. "{core_path} & {assets_path}" Placeholders are available.</div>
  </div>
  <div style="padding-bottom: 1rem;">
     <label for="apikey-server">Cron Security Key:</label>
     <input type="text" name="cronKey" id="cronkey" value="'.$values['cronKey'].'" align="left" size="40" />
     <div class="field_desc">Security-Key for cron scheduled Backups. Can be any string.</div>
  </div>';


 return $output;
