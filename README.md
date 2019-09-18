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
backupmodx.excludes | Files / Folders to exclude from the Backup. Comma separated list. core_path/assets_path placeholder are available. Example: {assets_path}uploads/ |
backupmodx.targetPath | The path to the folder to store the backups. core_path/assets_path placeholder are available. Example: {core_path}backups/ | {core_path}backup/
backupmodx.cronKey | Security key for cron scheduled backups. Can be any string. |
backupmodx.cronFiles | Whether or not to include MODX files in cron-backup. | Yes
backupmodx.cronDatabase | Whether or not to include the database in cron-backup. | Yes
backupmodx.cronNote | Optional descriptional txt-file added to the backup. |
backupmodx.cronEnable | Enable cron. | No
backupmodx.cronMaxDatabase | Maximum stored backups of databases. | 5
backupmodx.cronMaxFiles | Maximum stored backups of files. | 5
backupmodx.groups | Comma separated list of groups names, that will have access to the widget. | Administrator

## Translations

Translations of the package could be done on [Crowdin](https://crowdin.com/project/backupmodx)
