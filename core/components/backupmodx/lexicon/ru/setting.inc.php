<?php
/**
 * Setting Lexicon Entries for BackupMODX
 *
 * @package backupmodx
 * @subpackage lexicon
 */
$_lang['area_cron'] = 'хрон';
$_lang['setting_backupmodx.cronDatabase'] = 'Резервирование базы данных';
$_lang['setting_backupmodx.cronDatabase_desc'] = 'Включать или нет Базу данных в резервную копию по расписанию.';
$_lang['setting_backupmodx.cronEnable'] = 'Резервное копирование по расписанию';
$_lang['setting_backupmodx.cronEnable_desc'] = 'Включить или выключить резервирование (cron).';
$_lang['setting_backupmodx.cronFiles'] = 'Резервирование файлов';
$_lang['setting_backupmodx.cronFiles_desc'] = 'Включать или нет MODX файлы в резервную копию по расписанию.';
$_lang['setting_backupmodx.cronKey'] = 'Cron секретный ключ';
$_lang['setting_backupmodx.cronKey_desc'] = 'Секретный ключ для резервного копирования по расписанию. Может быть любой строкой.';
$_lang['setting_backupmodx.cronMaxDatabase'] = 'Лимит баз данных';
$_lang['setting_backupmodx.cronMaxDatabase_desc'] = 'Максимальное количество резервных копий базы данных (для резервирования по расписанию).';
$_lang['setting_backupmodx.cronMaxFiles'] = 'Лимит файлов';
$_lang['setting_backupmodx.cronMaxFiles_desc'] = 'Максимальное количество резервных копий файлов (для резервирования по расписанию).';
$_lang['setting_backupmodx.cronNote'] = 'Примечание';
$_lang['setting_backupmodx.cronNote_desc'] = 'Текстовый файл с описанием будет добавлен к резервной копии (необязателен).';
$_lang['setting_backupmodx.debug'] = 'Отладка';
$_lang['setting_backupmodx.debug_desc'] = 'Записывать отладочную информацию в лог ошибок MODX.';
$_lang['setting_backupmodx.excludeFiles'] = 'Исключить файлы';
$_lang['setting_backupmodx.excludeFiles_desc'] = 'Файлы, которые необходимо исключить из резервного копирования. Список, разделенный запятыми. Возможны регулярные выражения. Пример: \...* для имен файлов, начинающихся с точки.';
$_lang['setting_backupmodx.excludeFolders'] = 'Исключить папки';
$_lang['setting_backupmodx.excludeFolders_desc'] = 'Папки должны быть исключены из резервного копирования. Список, разделенный запятыми. Держатель пути доступен. Пример: {путь_настройки} загрузка/';
$_lang['setting_backupmodx.groups'] = 'Группы';
$_lang['setting_backupmodx.groups_desc'] = 'Название группы пользователей или список групп, разделенный запятой. Эти группы будут иметь доступ к виджету.';
$_lang['setting_backupmodx.targetPath'] = 'Путь к резервной копии';
$_lang['setting_backupmodx.targetPath_desc'] = 'Путь к директории, где будут храниться резервные копии. Core и Assets плейсхолдеры доступны. Пример: {core_path}backups/';
$_lang['setting_backupmodx.timelimit'] = 'Ограничение времени резервного копирования';
$_lang['setting_backupmodx.timelimit_desc'] = 'Ограничение по времени для резервного копирования в секундах.';
