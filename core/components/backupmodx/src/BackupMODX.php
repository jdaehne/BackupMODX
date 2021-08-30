<?php
/**
 * BackupMODX
 *
 * Copyright 2015-2021 by Jan Dähne <thomas.jakobi@partout.info>
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
    public $version = '3.0.4-pl';

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
        $this->namespace = $this->getOption('namespace', $options, $this->namespace);

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
            'targetPath' => $this->getOption('targetPath', $options, '{assets_path}backups/'),
            'excludes' => $this->getOption('excludes', $options, ''),
            'zipAlias' => $this->getOption('zipAlias', $options, 'zip'),
            'mysqldumpAlias' => $this->getOption('mysqldumpAlias', $options, 'mysqldump'),
            'debug' => (bool)$this->getOption('debug', $options, false),
            'logTarget' => json_decode($this->getOption('logTarget', $options, '{"target":"FILE","options":{"filename":"' . $this->namespace . '.log"}}'), true),
            'timelimit' => (int) $this->getOption('timelimit', $options, 120)
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
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
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
        $file = $this->getOption('targetPath') . $folder . '/' . $filename;

        if (file_exists($file)) {
            return $file;
        }

        return false;
    }

    /**
     * Get backups
     *
     * @return array
     */
    public function getBackups()
    {
        $backups = array();
        $folders = preg_grep('/^([^.])/', scandir($this->getOption('targetPath'))); // ignore hidden files: '.' and '..'

        foreach ($folders as $folder) {
            $filenames = preg_grep('/^([^.])/', scandir($this->getOption('targetPath') . $folder));

            $files = array();
            $note = '';

            foreach ($filenames as $file) {
                switch (pathinfo($file, PATHINFO_EXTENSION)) {
                    case 'zip':
                        $files['files'] = $file;
                        break;
                    case 'sql':
                        $files['database'] = $file;
                        break;
                    case 'txt':
                        $files['note'] = $file;
                        $note = file_get_contents($this->getOption('targetPath') . $folder . '/' . $file);
                        break;
                }
            }

            if (!empty($files)) {
                $backups[] = array(
                    'name' => $folder,
                    'path' => $this->getOption('targetPath'),
                    'date' => filemtime($this->getOption('targetPath') . $folder),
                    'date_format' => date($this->modx->getOption('manager_date_format') . ' ' . $this->modx->getOption('manager_time_format'), filemtime($this->getOption('targetPath') . $folder)),
                    'note' => $note,
                    'files' => $files,
                );
            }
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
            $cacheManager = $this->modx->getCacheManager();
            $cacheManager->deleteTree($path, array(
                'deleteTop' => true,
                'skipDirs' => false,
                'extensions' => array('.sql', '.zip', '.txt')
            ));

            if ($this->getOption('debug')) {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Remove backup: ' . $path, $this->getOption('logTarget'), 'BackupMODX');
            }

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
        $max = $maxFiles > $maxDatabase ? $maxFiles : $maxDatabase;

        $backups = $this->getBackups();

        // Remove old files
        foreach (array_slice($backups, $maxFiles) as $backup) {
            $file = $backup['path'] . $backup['filename'] . '/' . $backup['files']['files'];
            if (file_exists($file)) {
                unlink($file);
            }
        }

        // Remove old databases
        foreach (array_slice($backups, $maxDatabase) as $backup) {
            $file = $backup['path'] . $backup['filename'] . '/' . $backup['files']['database'];
            if (file_exists($file)) {
                unlink($file);
            }
        }

        // Remove old backup folders
        foreach (array_slice($backups, $max) as $backup) {
            $this->removeBackup($backup['filename']);
        }
    }
}
