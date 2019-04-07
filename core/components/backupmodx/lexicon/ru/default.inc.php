
<?php
/**
 * ru default topic lexicon file for backupmodx extra
 *
 * Created on 1, April 2019 by @himurovich https://github.com/himurovich
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
 * ru default topic lexicon strings
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package backupmodx
 **/


 $_lang['setting_backupmodx.excludes'] = 'Исключить файлы / директории';
 $_lang['setting_backupmodx.excludes_desc'] = 'Файлы / директории для исключения из резервной копии, список через запятую. Assets плейсхолдер доступен.Пример: {assets_path}uploads/';

 $_lang['setting_backupmodx.targetPath'] = 'Путь к резервной копии';
 $_lang['setting_backupmodx.targetPath_desc'] = 'Путь к директории, где будут храниться резервные копии. Core и Assets плейсхолдеры доступны. Пример: {core_path}backups/';

 $_lang['setting_backupmodx.cronKey'] = 'Cron секретный ключ';
 $_lang['setting_backupmodx.cronKey_desc'] = 'Секретный ключ для резервного копирования по расписанию. Может быть любой строкой.';

 $_lang['setting_backupmodx.mysqldumpAlias'] = 'mysqldump псевдоним';
 $_lang['setting_backupmodx.mysqldumpAlias_desc'] = 'Псевдоним для mysqldump.';

 $_lang['setting_backupmodx.zipAlias'] = 'ZIP псевдоним';
 $_lang['setting_backupmodx.zipAlias_desc'] = 'Псевдоним для zip файла.';

 $_lang['setting_backupmodx.cronFiles'] = 'Резервирование файлов';
 $_lang['setting_backupmodx.cronFiles_desc'] = 'Включать или нет MODX файлы в резервную копию по расписанию';

 $_lang['setting_backupmodx.cronDatabase'] = 'Резервирование базы данных';
 $_lang['setting_backupmodx.cronDatabase_desc'] = 'Включать или нет базу данных в резервную копию по расписанию';

 $_lang['setting_backupmodx.cronNote'] = 'Примечание';
 $_lang['setting_backupmodx.cronNote_desc'] = 'Текстовый файл с описанием будет добавлен к резервной копии (необязателен).';

 $_lang['setting_backupmodx.cronEnable'] = 'Резервное копирование по расписанию';
 $_lang['setting_backupmodx.cronEnable_desc'] = 'Включить или выключить резервирование (cron)';

 $_lang['setting_backupmodx.cronMaxDatabase'] = 'Лимит баз данных';
 $_lang['setting_backupmodx.cronMaxDatabase_desc'] = 'Максимальное количество резервных копий базы данных (для резервирования по расписанию).';

 $_lang['setting_backupmodx.cronMaxFiles'] = 'Лимит файлов';
 $_lang['setting_backupmodx.cronMaxFiles_desc'] = 'Максимальное количество резервных копий файлов (для резервирования по расписанию).';

 $_lang['setting_backupmodx.mysqlAlias'] = 'MySQL псевдоним';
 $_lang['setting_backupmodx.mysqlAlias_desc'] = 'Псевдоним для MySQL.';

 $_lang['setting_backupmodx.groups'] = 'Группы';
 $_lang['setting_backupmodx.groups_desc'] = 'Название группы пользователей или список групп, разделенный запятой. Эти группы будут иметь доступ к виджету.';
