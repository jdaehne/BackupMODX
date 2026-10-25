<?php
/**
 * BackupMODX
 *
 * Copyright 2015-2026 by Jan Dähne <info@quadro-system.de>
 *
 * @package backupmodx
 * @subpackage classfile
 */

namespace BackupMODX;

use modX;
use xPDO;

/**
 * Class BackupMODX
 */
class BackupMODX
{
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;

    /**
     * The namespace
     * @var string $namespace
     */
    public $namespace = 'backupmodx';

    /**
     * The package name
     * @var string $packageName
     */
    public $packageName = 'BackupMODX';

    /**
     * The version
     * @var string $version
     */
    public $version = '3.1.3';

    /**
     * The class options
     * @var array $options
     */
    public $options = array();

    /**
     * BackupMODX constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    public function __construct(modX &$modx, $options = array())
    {
        $this->modx =& $modx;

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path') . 'components/' . $this->namespace . '/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path') . 'components/' . $this->namespace . '/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url') . 'components/' . $this->namespace . '/');

        // Load some default paths for easier management
        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'version' => $this->version,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'vendorPath' => $corePath . 'vendor/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'pagesPath' => $corePath . 'elements/pages/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'pluginsPath' => $corePath . 'elements/plugins/',
            'controllersPath' => $corePath . 'controllers/',
            'processorsPath' => $corePath . 'processors/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php'
        ), $options);

        // Add default options
        $this->options = array_merge($this->options, array(
            'targetPath' => $this->translatePath($this->getOption('targetPath', $options, '{core_path}backup/')),
            'debug' => (bool)$this->getOption('debug', $options, false),
            'logTarget' => json_decode($this->getOption('logTarget', $options, '{"target":"FILE","options":{"filename":"' . $this->namespace . '.log"}}'), true),
            'timelimit' => (int)$this->getOption('timelimit', $options, 120)
        ));

        $lexicon = $this->modx->getService('lexicon', 'modLexicon');
        $lexicon->load($this->namespace . ':default');
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = array(), $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("$this->namespace.$key", $this->modx->config)) {
                $option = $this->modx->getOption("$this->namespace.$key");
            }
        }

        return $option;
    }

    /**
     * @param string $path
     * @return string
     */
    public function translatePath($path)
    {
        return str_replace(array(
            '{base_path}',
            '{core_path}',
            '{assets_path}',
        ), array(
            $this->modx->getOption('base_path', null, MODX_BASE_PATH),
            $this->modx->getOption('core_path', null, MODX_CORE_PATH),
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH),
        ), $path);
    }

    /**
     * Get backup file
     *
     * @param string $folder
     * @param string $filename
     * @return bool|string
     */
    public function getFile($folder, $filename)
    {
        if (!$this->isValidName($folder) || !$this->isValidName($filename)) {
            return false;
        }

        $file = $this->getOption('targetPath') . $folder . '/' . $filename;

        if (file_exists($file)) {
            return $file;
        }

        return false;
    }

    /**
     * Check if a backup folder or file name is a plain name without any path
     * information, to keep the file access inside the backup target path
     *
     * @param string $name
     * @return bool
     */
    public function isValidName($name)
    {
        return is_string($name) && $name !== '' && $name !== '.' && $name !== '..' && strpbrk($name, "/\\\0") === false;
    }

    /**
     * Get backups
     *
     * @return array
     */
    public function getBackups()
    {
        $targetPath = $this->getOption('targetPath');
        $dateFormat = $this->modx->getOption('manager_date_format') . ' ' . $this->modx->getOption('manager_time_format');

        $backups = array();
        foreach ($this->scanFolder($targetPath) as $folder) {
            $folderPath = $targetPath . $folder . '/';
            $date = filemtime($folderPath);
            $backup = array(
                'name' => $folder,
                'path' => $targetPath,
                'date' => $date,
                'date_format' => date($dateFormat, $date),
            );

            $files = array();
            $note = '';
            foreach ($this->scanFolder($folderPath) as $file) {
                switch (pathinfo($file, PATHINFO_EXTENSION)) {
                    case 'zip':
                        $files['files'] = $file;
                        break;
                    case 'sql':
                        $files['database'] = $file;
                        break;
                    case 'txt':
                        $files['note'] = $file;
                        $note = file_get_contents($folderPath . $file);
                        break;
                }
            }

            if (!empty($files)) {
                $backup['note'] = $note;
                $backup['files'] = $files;
            }
            $backups[] = $backup;
        }

        return array_reverse($backups);
    }

    /**
     * Remove backup
     *
     * @param string $backup
     * @return bool
     */
    public function removeBackup($backup)
    {
        $path = $this->getOption('targetPath') . $backup;

        if (file_exists($path)) {
            $this->deleteFolder($path);
            $this->debug('Remove backup: ' . $path);

            return true;
        }

        return false;
    }

    /**
     * Clean old cron backups
     *
     * @param int $maxDatabase
     * @param int $maxFiles
     */
    public function cleanCronBackups($maxDatabase = 10, $maxFiles = 5)
    {
        $backups = $this->getBackups();

        // Remove old files
        foreach (array_slice($backups, $maxFiles) as $backup) {
            $this->removeBackupFile($backup, 'files');
        }

        // Remove old databases
        foreach (array_slice($backups, $maxDatabase) as $backup) {
            $this->removeBackupFile($backup, 'database');
        }

        // Remove old backup folders
        foreach (array_slice($backups, max($maxFiles, $maxDatabase)) as $backup) {
            $this->removeBackup($backup['name']);
        }
    }

    /**
     * Remove a single file of a backup
     *
     * @param array $backup A backup entry returned by getBackups
     * @param string $type The file type key (files, database or note)
     */
    private function removeBackupFile($backup, $type)
    {
        if (isset($backup['files'][$type])) {
            $file = $backup['path'] . $backup['name'] . '/' . $backup['files'][$type];
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Delete a backup folder including its backup files
     *
     * @param string $path
     */
    protected function deleteFolder($path)
    {
        $this->modx->getCacheManager()->deleteTree($path, array(
            'deleteTop' => true,
            'skipDirs' => false,
            'extensions' => array('.sql', '.zip', '.txt')
        ));
    }

    /**
     * List the non-hidden entries of a folder
     *
     * @param string $path
     * @return array
     */
    private function scanFolder($path)
    {
        return preg_grep('/^([^.])/', scandir($path)); // ignore hidden files: '.' and '..'
    }

    /**
     * Log a message to the BackupMODX log target
     *
     * @param string $message
     */
    protected function log($message)
    {
        $this->modx->log(xPDO::LOG_LEVEL_ERROR, $message, $this->getOption('logTarget'), 'BackupMODX');
    }

    /**
     * Log a message to the BackupMODX log target, if debug mode is enabled
     *
     * @param string $message
     */
    protected function debug($message)
    {
        if ($this->getOption('debug')) {
            $this->log($message);
        }
    }
}
