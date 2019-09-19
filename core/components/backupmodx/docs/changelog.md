Changelog for BackupMODX

BackupMODX 3.0.1
---------------------------------
+ Extend logging information
+ Catch construct errors of chumper/zipper
+ Add lexicon editing to Crowdin

BackupMODX 3.0.0
---------------------------------
+ Complete rewrite
+ Removed exec calls
+ Use ifsnop/mysqldump-php and Chumper/Zipper composer packages
+ Debug option logging the files to backup and other information in backupmodx.log
+ Completely AJAX driven dashboard widget
+ Every text displayed in the browser is translateable
+ Catch an error during php shutdown to display (i.e. maximum execution time) errors

BackupMODX 2.0.1
---------------------------------
+ Fix Ajax timeout
+ Fix download large files
+ Fix custom assets_path (Thanks to mcnickel)

BackupMODX 2.0.0
---------------------------------
+ Add cron scheduled backups
+ Add restore Database
+ Redesign Dashboard-Widget

BackupMODX 1.0.5
---------------------------------
+ Bugfix: abbility to change tar-alias for different hosting-provider
+ Bugfix: keeps cache-folder included - only excludes all files inside the cache-folder to achieve a smaller tar-archive
+ Adding an optional readme file
+ Define additional files and folders to exclude in the backup
+ Define a directory to place the backup (inside or outside the webroot)

BackupMODX 1.0.4
---------------------------------
+ Bugfix: placing the sql-file in the root of combined tar-archive
+ Bugfix: excluding cache-folder to achieve smaller files

BackupMODX 1.0.3
---------------------------------
+ placing the sql-file in the root of combined tar-archive
+ checking if shell-commands working
+ excluding cache-folder to achieve smaller files
+ adding abbility to change tar-alias for different hosting-provider

BackupMODX 1.0.2
---------------------------------
+ Ability to define groups that can see the widget. Default is only: Administartor

BackupMODX 1.0.1
---------------------------------
+ Includes "core-folder" placed outside the webroot.

BackupMODX 1.0.0
---------------------------------
Initial Version
