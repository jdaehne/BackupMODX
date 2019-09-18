[![Crowdin](https://badges.crowdin.net/backupmodx/localized.svg)](https://crowdin.com/project/backupmodx)

# BackupMODX

Backup Dashboard Widget for MODX CMS.

## Features

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

## Installation

MODX Package Management

## Usage

Install via package manager and add the BackupMODX widget to the dashboard.

## System Settings
Setting | Description | Default
--------|-------------|--------
backupmodx.cronDatabase | Whether or not to include the database in cron-backup. | Yes
backupmodx.cronEnable | Enable cron backup. | No
backupmodx.cronFiles | Whether or not to include MODX files in cron-backup. | Yes
backupmodx.cronKey | Security key for cron scheduled backups. Can be any string. |
backupmodx.cronMaxDatabase | Maximum stored backups of the database. | 5
backupmodx.cronMaxFiles | Maximum stored backups of files. | 5
backupmodx.cronNote | Optional descriptional txt-file added to the backup. |
backupmodx.excludeFiles | Files to be excluded from the backup. Comma separated list. Regular expressions are possible. Example: \..* for file names, that start with a dot. |
backupmodx.excludeFolders | Folders to be excluded from the backup. Comma separated list. Path placeholder are available. Example: {assets_path}uploads/ |
backupmodx.groups | Comma separated list of groups names, that will have access to the widget. | Administrator
backupmodx.targetPath | The path to the folder to store the backups. Path placeholder are available. Example: {core_path}backups/ | {core_path}backup/

## Translations

Translations of the package could be done on [Crowdin](https://crowdin.com/project/backupmodx)
