# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.3] - 2021-03-11

### Fixed

- Fix creating a backup note with the widget [#64]
- Fix not hiding the backup actions after backup [#65]

## [3.0.2] - 2021-03-11

### Added

- MODX 3 compatibility
- System setting for the timelimit

## [3.0.1] - 2019-09-19

### Added

- Extend logging information
- Catch construct errors of chumper/zipper
- Add lexicon editing to Crowdin

## [3.0.0] - 2019-07-19

### Added

- Complete rewrite
- Use ifsnop/mysqldump-php and Chumper/Zipper composer packages
- Debug option logging the files to backup and other information in backupmodx.log
- Completely AJAX driven dashboard widget
- Every text displayed in the browser is translateable
- Catch an error during php shutdown to display (i.e. maximum execution time) errors

### Removed

- PHP exec calls

## [2.0.1] - 2018-12-31

### Fixed

- Ajax timeout
- Download large files
- Custom assets_path (Thanks to mcnickel)

## [2.0.0] - 2018-12-29

### Added

- Add cron scheduled backups
- Add restore Database

### Changed

- Redesign Dashboard-Widget

## [1.0.5] - 2016-02-17

### Added

- Adding an optional readme file
- Define additional files and folders to exclude in the backup
- Define a directory to place the backup (inside or outside the webroot)

### Fixed

- Ability to change tar-alias for different hosting-provider
- Keeps cache-folder included - only excludes all files inside the cache-folder to achieve a smaller tar-archive

## [1.0.4] - 2016-01-20

### Fixed

- Placing the sql-file in the root of combined tar-archive
- Excluding cache-folder to achieve smaller files

## [1.0.3] - 2016-01-18

### Added

- Placing the sql-file in the root of combined tar-archive
- Checking if shell-commands working
- Excluding cache-folder to achieve smaller files
- Adding abbility to change tar-alias for different hosting-provider

## [1.0.2] - 2016-01-13

### Added

- Ability to define groups that can see the widget. Default is only: Administartor

## [1.0.1] - 2016-01-06

### Added

- Include "core-folder" placed outside the webroot.

## [1.0.0] - 2016-01-02

### Added

- Initial Version
