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


// Returns an empty string if user shouldn't see the widget
$groups = $modx->getOption('groups', $scriptProperties, 'Administrator', true);
if (strpos($groups, ',') !== false) {
	$groups = explode(',', $groups);
}
if (!$modx->user->isMember($groups)) {
	return '';
}


//Check if server supports shell-commands
if (!shell_exec("type type")) { return 'Your server does not support shell-commands. Backup not possible.'; }

//Get Properties
$tarAlias = $modx->getOption('tarAlias', $scriptProperties, 'tar', true); //some websites may need a different alias for tar


$config = $modx->getConfig();

if (isset($_POST['backupMODX'])) {

	set_time_limit(0);
	ini_set('max_execution_time', 0);

	if (!empty($_POST["mysql"]) or !empty($_POST["files"])){
		$dir = $config[base_path] . (!empty($_POST["folder"]) ? $_POST["folder"]."backup" : "backup");
		$url = $config[base_url] . (!empty($_POST["folder"]) ? $_POST["folder"]."backup" : "backup");
		$base_path = MODX_BASE_PATH;
		$core_path = MODX_CORE_PATH;
		$date = date("Ymd-His");
		$dbase = $modx->getOption('dbname');
		$database_server = $config[host];
		$database_user = $config[username];
		$database_password = $config[password];
		$targetSql = "$dir/{$dbase}_{$date}_mysql.sql";
		$targetTar = "$dir/{$dbase}_{$date}_files.tar";
		$targetCom = "$dir/{$dbase}_{$date}_combined.tar";
		$urlSql = "$url/{$dbase}_{$date}_mysql.sql";
		$urlTar = "$url/{$dbase}_{$date}_files.tar";
		$urlCom = "$url/{$dbase}_{$date}_combined.tar";

		//Create Folder
		system("mkdir $dir");
		
		//MySQL- Backup
		if (!empty($_POST["mysql"])){
			
			system("mysqldump --host=$database_server --user=$database_user --password=$database_password --databases $dbase --no-create-db --default-character-set=utf8 --result-file={$targetSql}");
			
			//If no mysqldump was possible try:
			if (file_exists($targetSql) or filesize($targetSql) <= 0) {
				system(sprintf('mysqldump --no-tablespaces --opt -h%s -u%s -p"%s" %s --result-file=%s', $database_server, $database_user, $database_password, $dbase, $targetSql));
			}
		}
		
		//File-Backup
		if (!empty($_POST["files"])){
			system("$tarAlias cf {$targetTar} --exclude=$dir --exclude={$core_path}cache $base_path $core_path");
		}
		
		//Combine SQL and Files in one archive
		if (file_exists($targetSql) and file_exists($targetTar) and filesize($targetSql) > 0) {
			system("cp {$targetTar} {$targetCom}"); //copy files-archive
			system("$tarAlias uf {$targetCom} -C $dir {$dbase}_{$date}_mysql.sql"); //adding sql-file in the root
		}
		
		$backup = true;

		//Filesize
		if (file_exists($targetSql) and filesize($targetSql) > 0) {
			$mysql_name = basename($targetSql);
			$mysql_size = round(filesize($targetSql) / 1000000, 2);
		}
		if (file_exists($targetTar)) {
			$files_name = basename($targetTar);
			$files_size = round(filesize($targetTar) / 1000000, 2);
		}
		if (file_exists($targetCom)) {
			$combi_name = basename($targetCom);
			$combi_size = round(filesize($targetCom) / 1000000, 2);
		}
		
	}
}


//Delete Backups
if (!empty($_POST["removeBackup"])){
	if (!empty($_POST["removeBackup"]) and !empty($_POST["dir"])) {
		$dir = $_POST["dir"];
		foreach(glob("$dir/*") as $file) {
			unlink($file);
		}
		rmdir($dir);
	}
}




if ($backup != true) {

	echo '
		<form method="post" action="">
			<p>
				Backup your MODX-Site:<br><br>
				<input type="checkbox" name="mysql" id="mysql" value="1" checked><label for="mysql"> MySQL Database</label><br />
				<input type="checkbox" name="files" id="files" value="1" checked><label for="files"> Files</label>
			</p>
			<br>
			<p>
				Folder to place Files: '.$config[base_path].' <input type="text" name="folder" value="assets/"> backup/<br>
			</p><br>
			<input class="x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" type="submit" name="backupMODX" value="Backup Site!">
		</form>';
}else {
	if ($backup == true) {
		echo'
			<form method="post" action="">
				<h3 style="color:grey">Backup Finished!</h3>
				<p>
					<span style="display: inline-block; width: 90px;">MySQL:</span>'.(!empty($mysql_name) ? '<a href="'.$urlSql.'" target="_blank" download>'.$mysql_name.'</a> ('.$mysql_size.' MB)' : 'No Backup!').'<br />
					<span style="display: inline-block; width: 90px;">Files:</span>'.(!empty($files_name) ? '<a href="'.$urlTar.'" target="_blank" download>'.$files_name.'</a> ('.$files_size.' MB)' : 'No Backup!').'
					'.(!empty($combi_name) ? '<br /><span style="display: inline-block; width: 90px;">MySQL & Files:</span><a href="'.$urlCom.'" target="_blank" download>'.$combi_name.'</a> ('.$combi_size.' MB)' : '').'
				</p><br>
				<input type="hidden" name="dir" value="'.$dir.'" />
				<input class="x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" type="submit" name="removeBackup" value="Remove Backup">
			</form>';
	}
}
