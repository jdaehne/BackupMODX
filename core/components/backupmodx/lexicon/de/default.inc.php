<?php
/**
 * de default topic lexicon file for backupmodx extra
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
 * de default topic lexicon strings
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package backupmodx
 **/


$_lang['setting_backupmodx.excludes'] = 'Dateien & Verzeichnisse ausschließen';
$_lang['setting_backupmodx.excludes_desc'] = 'Datein und Verzeichnisse die vom Backup ausgeschlossen werden sollen. Durch Komma separierte Liste. Assets Platzhalter ist möglich. Beispiel: {assets_path}uploads/';

$_lang['setting_backupmodx.targetPath'] = 'Backup Ziel Pfad';
$_lang['setting_backupmodx.targetPath_desc'] = 'Pfad zum speichern des Backups.  Core- & Assetsplatzhalter is möglich. Beispiel: {core_path}components/backupmodx/backups/';

$_lang['setting_backupmodx.cronKey'] = 'Cron Sicherheitsschlüssel';
$_lang['setting_backupmodx.cronKey_desc'] = 'Sicherheitsschlüssel für Cron gesteuerte Backups. (Sting)';

$_lang['setting_backupmodx.mysqldumpAlias'] = 'Alias mysqldump';
$_lang['setting_backupmodx.mysqldumpAlias_desc'] = 'Alias für mysqldump.';

$_lang['setting_backupmodx.zipAlias'] = 'Alias zip';
$_lang['setting_backupmodx.zipAlias_desc'] = 'Alias für zip Komprimierung.';

$_lang['setting_backupmodx.cronFiles'] = 'Backup Dateien';
$_lang['setting_backupmodx.cronFiles_desc'] = 'Dateien bei Cron-Backup berücksichtigen.';

$_lang['setting_backupmodx.cronDatabase'] = 'Backup Datenbank';
$_lang['setting_backupmodx.cronDatabase_desc'] = 'Datenbank bei Cron-Backup berücksichtigen.';

$_lang['setting_backupmodx.cronNote'] = 'Notiz';
$_lang['setting_backupmodx.cronNote_desc'] = 'Optionale Notiz zum Cron-Backup als txt-Datei.';

$_lang['setting_backupmodx.cronEnable'] = 'Cron aktivieren';
$_lang['setting_backupmodx.cronEnable_desc'] = 'Cron-Backup aktivieren oder deaktivieren.';

$_lang['setting_backupmodx.cronMaxDatabase'] = 'Max Datenbanken';
$_lang['setting_backupmodx.cronMaxDatabase_desc'] = 'Maximale Anzahl von Datenbanken die als Backup vorgehalten werden sollen.';

$_lang['setting_backupmodx.cronMaxFiles'] = 'Max Dateien';
$_lang['setting_backupmodx.cronMaxFiles_desc'] = 'Maximale Anzahl von Datei-Archiven die als Backup vorgehalten werden sollen.';

$_lang['setting_backupmodx.mysqlAlias'] = 'Alias mysql';
$_lang['setting_backupmodx.mysqlAlias_desc'] = 'Alias für mysql.';

$_lang['setting_backupmodx.groups'] = 'Gruppen';
$_lang['setting_backupmodx.groups_desc'] = 'Gruppenname oder Komma separierte Liste von Gruppennamen, welche das Widget verwenden dürfen.';
