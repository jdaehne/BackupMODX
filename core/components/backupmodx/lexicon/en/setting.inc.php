<?php
/**
 * Setting Lexicon Entries for BackupMODX
 *
 * @package backupmodx
 * @subpackage lexicon
 */
$_lang['area_cron'] = 'Cron';
$_lang['setting_backupmodx.cronDatabase'] = 'Backup Database';
$_lang['setting_backupmodx.cronDatabase_desc'] = 'Whether or not to include the database in cron backup.';
$_lang['setting_backupmodx.cronEnable'] = 'Enable Cron';
$_lang['setting_backupmodx.cronEnable_desc'] = 'Enable cron backup.';
$_lang['setting_backupmodx.cronFiles'] = 'Backup Files';
$_lang['setting_backupmodx.cronFiles_desc'] = 'Whether or not to include files in cron backup.';
$_lang['setting_backupmodx.cronKey'] = 'Cron Security Key';
$_lang['setting_backupmodx.cronKey_desc'] = 'Security key for cron scheduled backups. Can be any string.';
$_lang['setting_backupmodx.cronMaxDatabase'] = 'Max Databases';
$_lang['setting_backupmodx.cronMaxDatabase_desc'] = 'Maximum stored backups of the database.';
$_lang['setting_backupmodx.cronMaxFiles'] = 'Max Files';
$_lang['setting_backupmodx.cronMaxFiles_desc'] = 'Maximum stored backups of files.';
$_lang['setting_backupmodx.cronNote'] = 'Note';
$_lang['setting_backupmodx.cronNote_desc'] = 'Optional description text file added to the backup.';
$_lang['setting_backupmodx.debug'] = 'Debug';
$_lang['setting_backupmodx.debug_desc'] = 'Log debug information in the BackupMODX log file (core/cache/logs/backupmodx.log).';
$_lang['setting_backupmodx.excludeFiles'] = 'Exclude Files';
$_lang['setting_backupmodx.excludeFiles_desc'] = 'Comma-separated list of files to be excluded from the backup. Regular expressions are possible. Example: \..* for file names, that start with a dot.';
$_lang['setting_backupmodx.excludeFolders'] = 'Exclude Folders';
$_lang['setting_backupmodx.excludeFolders_desc'] = 'Comma-separated list of folders to be excluded from the backup. Path placeholders are available. Example: {assets_path}uploads/';
$_lang['setting_backupmodx.groups'] = 'Groups';
$_lang['setting_backupmodx.groups_desc'] = 'Comma-separated list of user group names, that will have access to the widget.';
$_lang['setting_backupmodx.targetPath'] = 'Backup Target Path';
$_lang['setting_backupmodx.targetPath_desc'] = 'The path to the folder to store the backups. Path placeholders are available. Example: {core_path}backup/';
$_lang['setting_backupmodx.timelimit'] = 'Backup Timelimit';
$_lang['setting_backupmodx.timelimit_desc'] = 'Timelimit for the backup in seconds.';
