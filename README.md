# BackupMODX
Backup Dashboard Widget for MODX CMS.

The BackupMODX extra is a tiny dashboard widget. Just place the widget into your
dashboard and click the "Backup" button whenever you need a quick backup of your
site. You can select either to backup your database, files or both. After the
backup is finished you can download the files as a zip-archive. Just click the
"Remove Backup" button after your download is finished to remove the backup.

The extra will backup all files and folders inside your MODX installation and
will also include the core-folder if placed outside the web-root.

The widget is tested in different hosting environments and it works for our
needs. If you have any problems/errors while using this extra - please let us
know. If you also know how to enhance the extra/code - please feel free to
contribute to the GitHub Repository.

## New in 2.x
- Cron scheduled Backups
- Database restore option

## New in 3.x
- Removed exec calls
- Debug option logging the files to backup and other information in backupmodx.log
- Completely AJAX driven dashboard widget

## System Settings
| Setting | Description |
| --- | --- |
| backupmodx.excludes | Files / Folders to exclude from the Backup. Comma separatet list. Assets Placeholder is available. Example: {assets_path}uploads/ |
| backupmodx.targetPath | The path to the folder to store the backups. Assets Placeholder is available. Example: {core_path}backups/ |
| backupmodx.cronKey | Security-Key for cron scheduled Backups. Can be any string. |
| backupmodx.cronFiles | Whether or not to include MODX Files in Cron-Backup. |
| backupmodx.cronDatabase | Whether or not to include Database in Cron-Backup. |
| backupmodx.cronNote | Optional descriptional txt-file added to the Backup. |
| backupmodx.cronEnable | Enable or Disable Cron. |
| backupmodx.cronMaxDatabase | Maximum stored Backups of Databas. |
| backupmodx.cronMaxFiles | Maximum stored Backups of Files. |
| backupmodx.groups | Group or comma separated list of Groups. This Groups will have access to the widget. |
