<?php
/**
 * en default topic lexicon file for backupmodx extra
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
 * en default topic lexicon strings
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package backupmodx
 **/


 $_lang['setting_backupmodx.excludes'] = 'Exclude Files / Folders';
 $_lang['setting_backupmodx.excludes_desc'] = 'Files / Folders to exclude from the Backup. Comma separatet list. Assets Placeholder is available. Example: {assets_path}uploads/';

 $_lang['setting_backupmodx.targetPath'] = 'Backup Target Path';
 $_lang['setting_backupmodx.targetPath_desc'] = 'The path to the folder to store the backups. Core & Assets Placeholder is available. Example: {core_path}backups/';

 $_lang['setting_backupmodx.cronKey'] = 'Cron Security Key';
 $_lang['setting_backupmodx.cronKey_desc'] = 'Security-Key for cron scheduled Backups. Can be any string.';

 $_lang['setting_backupmodx.mysqldumpAlias'] = 'Alias mysqldump';
 $_lang['setting_backupmodx.mysqldumpAlias_desc'] = 'Alias for mysqldump.';

 $_lang['setting_backupmodx.zipAlias'] = 'Alias zip';
 $_lang['setting_backupmodx.zipAlias_desc'] = 'Alias for zip compression.';

 $_lang['setting_backupmodx.cronFiles'] = 'Backup Files';
 $_lang['setting_backupmodx.cronFiles_desc'] = 'Whether or not to include MODX Files in Cron-Backup.';

 $_lang['setting_backupmodx.cronDatabase'] = 'Backup Database';
 $_lang['setting_backupmodx.cronDatabase_desc'] = 'Whether or not to include Database in Cron-Backup.';

 $_lang['setting_backupmodx.cronNote'] = 'Note';
 $_lang['setting_backupmodx.cronNote_desc'] = 'Optional descriptional txt-file added to the Backup.';

 $_lang['setting_backupmodx.cronEnable'] = 'Enable Cron';
 $_lang['setting_backupmodx.cronEnable_desc'] = 'Enable or Disable Cron.';

 $_lang['setting_backupmodx.cronMaxDatabase'] = 'Max Databases';
 $_lang['setting_backupmodx.cronMaxDatabase_desc'] = 'Maximum stored Backups of Database.';

 $_lang['setting_backupmodx.cronMaxFiles'] = 'Max Files';
 $_lang['setting_backupmodx.cronMaxFiles_desc'] = 'Maximum stored Backups of Files.';

 $_lang['setting_backupmodx.mysqlAlias'] = 'Alias mysql';
 $_lang['setting_backupmodx.mysqlAlias_desc'] = 'Alias for mysql.';

 $_lang['setting_backupmodx.groups'] = 'Groups';
 $_lang['setting_backupmodx.groups_desc'] = 'Group or comma separated list of Groups. This Groups will have access to the widget.';
