<?php
/**
 * BackupMODXWidget snippet for backupmodx extra
 *
 * Copyright 2015 by Quadro - Jan Dähne info@quadro-system.de
 * Created on 12-16-2015
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
 * Description
 * -----------
 * Backup MODX Dashboard widget
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package backupmodx
 **/
 
 
$groups = $modx->getOption('groups', $scriptProperties, 'Administrator', true);
if (strpos($groups, ',') !== false) {
    $groups = explode(',', $groups);
}
if (!$modx->user->isMember($groups)) {
    return '';
} 
 

echo 'Backup your MODX-Site:<br><br>';

if (isset($_POST['backupMODX'])) {
    
    $fp = @fopen(MODX_BASE_PATH . 'backup.php', 'w');
    if ($fp) {
        //File was created
        
        //Getting the script
        $fileContent = $modx->getChunk('BackupMODXSnippetScriptSource', array(
            "managerURL" => MODX_MANAGER_URL,
        ));
        fwrite($fp, $fileContent);
        fclose($fp);
        
        $modx->sendRedirect(MODX_BASE_URL . 'backup.php');
        
    }else {
        echo 'File Could not be created.';
    }
}

echo '<form method="post" action="">
           <input class="x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" type="submit" name="backupMODX" value="Backup Site!">
        </form>';