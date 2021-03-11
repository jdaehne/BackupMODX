<?php
/**
 * Backup
 *
 * @package backupmodx
 * @subpackage classfile
 */

namespace BackupMODX\Backup;

use BackupMODX;
use Chumper\Zipper\Zipper;
use Exception;
use Ifsnop\Mysqldump as IMysqldump;
use modX;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use xPDO;

/**
 * Class Backup
 */
class Backup extends BackupMODX
{
    /**
     * Remove constructor
     *
     * @param modX $modx
     * @param array $options
     */
    public function __construct(modX &$modx, $options = array())
    {
        parent::__construct($modx, $options);
    }

    /**
     * @var array $excludeFolders Default excluded folders
     */
    public $excludeFolders = array(
        '{core_path}cache',
        '{core_path}packages',
    );

    /**
     * @var array $excludeFiles Default excluded files
     */
    public $excludeFiles = array(
        '\.DS_Store'
    );

    /**
     * Backup the files
     *
     * @param string $filename
     * @return array|string
     */
    public function backupFiles($filename = null)
    {
        $excludeFolders = array_merge($this->excludeFolders, array($this->getOption('targetPath')));
        if ($this->getOption('excludeFolders') != '') {
            $excludeFolders = array_merge($excludeFolders, explode(',', $this->getOption('excludeFolders')));
        }
        $excludeFolders = array_map(array($this, 'translatePath'), $excludeFolders);
        $excludeFiles = $this->excludeFiles;
        if ($this->getOption('excludeFiles') != '') {
            $excludeFiles = array_merge($excludeFiles, explode(',', $this->getOption('excludeFiles')));
        }

        if ($this->getOption('debug')) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Excluded ' . count($excludeFolders) . ' folders.', $this->getOption('logTarget'), 'BackupMODX');
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Folder list:' . "\n" . print_r($excludeFolders, true), $this->getOption('logTarget'), 'BackupMODX');
        }

        $stripPrefix = self::stripPrefix();

        // Collect all files to backup in the different MODX folders
        $files = $this->getFiles(MODX_BASE_PATH, array_merge(array(
            MODX_ASSETS_PATH,
            MODX_CONNECTORS_PATH,
            MODX_CORE_PATH,
            MODX_MANAGER_PATH,
        ), $excludeFolders), $excludeFiles, $stripPrefix);
        $files = array_merge($files, $this->getFiles(MODX_ASSETS_PATH, $excludeFolders, $excludeFiles, $stripPrefix));
        $files = array_merge($files, $this->getFiles(MODX_CORE_PATH, $excludeFolders, $excludeFiles, $stripPrefix));
        $files = array_merge($files, $this->getFiles(MODX_CONNECTORS_PATH, $excludeFolders, $excludeFiles, $stripPrefix));
        $files = array_merge($files, $this->getFiles(MODX_MANAGER_PATH, $excludeFolders, $excludeFiles, $stripPrefix));

        if ($this->getOption('debug')) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'File backup ' . count($files) . ' files.', $this->getOption('logTarget'), 'BackupMODX');
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'File backup files list:' . "\n" . print_r($files, true), $this->getOption('logTarget'), 'BackupMODX');
        }

        $filename = $filename ? $filename : $this->createFilename();
        $target = $this->targetPath($filename) . $filename . '.zip';

        if ($this->getOption('debug')) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'File backup target: ' . $target, $this->getOption('logTarget'), 'BackupMODX');
        }

        try {
            $zipper = new Zipper();
            $zipper->make($target);
            foreach ($files as $path => $name) {
                $zipper->add($path, $name);
            }
            $zipper->close();
            return array(
                'name' => $filename,
                'filename' => basename($target),
                'path' => $target,
                'size' => $this->humanFilesize(filesize($target)),
            );
        } catch (Exception $e) {
            $msg = 'Zip Error: ' . $e->getMessage();
            if ($this->getOption('debug')) {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, $msg, $this->getOption('logTarget'), 'BackupMODX');
            }
            return $msg;
        }
    }

    /**
     * Backup the database
     *
     * @param string $filename
     * @return array|string
     */
    public function backupDatabase($filename = null)
    {
        $host = $this->modx->getOption('host');
        $username = $this->modx->getOption('username');
        $password = $this->modx->getOption('password');
        $database = $this->modx->getOption('dbname');

        $filename = $filename ? $filename : $this->createFilename();
        $target = $this->targetPath($filename) . $filename . '.sql';

        try {
            $dump = new IMysqldump\Mysqldump('mysql:host=' . $host . ';dbname=' . $database, $username, $password, array(
                'add-drop-table' => true,
                'exclude-tables' => array(
                    $this->modx->getTableName('modSession')
                )
            ));
            $dump->start($target);

            if ($this->getOption('debug')) {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Database backup.', $this->getOption('logTarget'), 'BackupMODX');
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Database backup size: ' . $this->humanFilesize(filesize($target)), $this->getOption('logTarget'), 'BackupMODX');
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Database backup target: ' . $target, $this->getOption('logTarget'), 'BackupMODX');
            }

            return array(
                'name' => $filename,
                'filename' => basename($target),
                'path' => $target,
                'size' => $this->humanFilesize(filesize($target)),
            );
        } catch (Exception $e) {
            $msg = 'mysqldump Error: ' . $e->getMessage();
            if ($this->getOption('debug')) {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, $msg, $this->getOption('logTarget'), 'BackupMODX');
            }
            return $msg;
        }
    }

    /**
     * Create note
     *
     * @param string $message
     * @param string|null $filename
     * @return array
     */
    public function createNote($message, $filename = null)
    {
        $filename = $filename ? $filename : $this->createFilename();
        $target = $this->targetPath($filename) . $filename . '.txt';

        $fp = fopen($target, 'wb');
        fwrite($fp, $message);
        fclose($fp);

        if ($this->getOption('debug')) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Backup note.', $this->getOption('logTarget'), 'BackupMODX');
        }

        return array(
            'name' => $filename,
            'filename' => basename($target),
            'path' => $target,
            'size' => $this->humanFilesize(filesize($target)),
        );
    }

    /**
     * Create backup
     *
     * @param bool $files
     * @param bool $database
     * @param string $message
     * @param int $timelimit
     * @return array|string|null
     */
    public function backup($files = true, $database = true, $message = '', $timelimit = 0)
    {
        set_time_limit($timelimit);
        ini_set('max_execution_time', $timelimit);
        register_shutdown_function(array($this, 'shutdown'));

        $filename = $this->createFilename();
        $targetPath = $this->targetPath($filename);

        // Create Backupdirectory if not exists
        $cacheManager = $this->modx->getCacheManager();
        if ($cacheManager->writeTree($targetPath)) {
            if ($database == true) {
                $database = $this->backupDatabase($filename);
            }
            if ($message != '') {
                $note = $this->createNote($message, $filename);
            } else {
                $note = '';
            }
            if ($files == true) {
                $files = $this->backupFiles($filename);
            }

            if ($database || $note || $files) {
                return array(
                    'filename' => $filename,
                    'path' => $targetPath,
                    'files' => array(
                        'database' => (!$database) ? '' : $database,
                        'files' => (!$files) ? '' : $files,
                        'note' => (!$note) ? '' : $note,
                    ),
                );
            } else {
                $cacheManager->deleteTree($targetPath, array(
                    'deleteTop' => true,
                    'skipDirs' => false,
                    'extensions' => array('.sql', '.zip', '.txt')
                ));
                return $this->modx->lexicon('backupmodx.err_create_backup');
            }
        } else {
            return $this->modx->lexicon('backupmodx.err_create_target_path');
        }
    }

    /**
     * Create filename
     *
     * @return string
     */
    private function createFilename()
    {
        return date('Y-m-d--His') . '--' . substr(md5(openssl_random_pseudo_bytes(20)), -4);
    }

    /**
     * Get target path
     *
     * @param string $filename
     * @return string
     */
    private function targetPath($filename)
    {
        $filename = $filename ? $filename : $this->createFilename();

        return $this->getOption('targetPath') . $filename . '/';
    }

    /**
     * Get human filesize
     *
     * @param int $bytes
     * @param int $decimals
     * @return string
     */
    private function humanFilesize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = intval(floor((strlen($bytes) - 1) / 3));
        if ($factor === 0) {
            return sprintf('%d', $bytes / pow(1024, $factor)) . ' Bytes';
        } else {
            return sprintf('%.' . $decimals . 'f', $bytes / pow(1024, $factor)) . ' ' . @$sz[$factor] . 'B';
        }
    }

    /**
     * Get an array of all files to backup
     *
     * @param string $directory
     * @param array $excludeFolders
     * @param array $excludeFiles
     * @param string $stripPrefix
     * @return array
     */
    private function getFiles($directory, $excludeFolders, $excludeFiles, $stripPrefix = '')
    {
        $excludeFolders = array_map(function ($item) {
            $item = trim($item);
            return rtrim($item, '/\\');
        }, $excludeFolders);
        $excludeFiles = array_map(function ($item) {
            $item = trim($item);
            return '~' . str_replace('~', '\~', $item) . '~';
        }, $excludeFiles);
        $stripPrefix = rtrim($stripPrefix, '/\\');

        $files = array();
        /**
         * @param SplFileInfo $file
         * @param mixed $key
         * @param RecursiveCallbackFilterIterator $iterator
         * @return bool True if you need to recurse or if the item is acceptable
         */
        $filter = function ($file, $key, $iterator) use ($excludeFolders, $excludeFiles) {
            $cleanFilename = $key;
            if ($file->isDir()) {
                if ($iterator->hasChildren() && !in_array($cleanFilename, $excludeFolders)) {
                    return true;
                }
            }
            if ($file->isFile()) {
                foreach ($excludeFiles as $excludeFile) {
                    if (preg_match($excludeFile, $file->getFilename())) {
                        return false;
                    }
                }
            }
            return $file->isFile();
        };

        $innerIterator = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator($innerIterator, $filter));

        foreach ($iterator as $path => $fileInfo) {
            $name = pathinfo($path, PATHINFO_BASENAME);
            $dir = pathinfo($path, PATHINFO_DIRNAME);
            $dir = (strpos($dir, $stripPrefix) === 0) ? ltrim(substr($dir, strlen($stripPrefix)), '/\\') : $dir;
            $files[$path] = ($dir) ? $dir . '/' . $name : $name;
        }

        return $files;
    }

    /**
     * If the MODX core path is not moved, strip the MODX base path, otherwise
     * strip everything until both paths are different
     *
     * @return string
     */
    private static function stripPrefix()
    {
        if (strcmp(MODX_BASE_PATH . 'core/', MODX_CORE_PATH) === 0) {
            $stripPrefix = MODX_BASE_PATH;
        } else {
            $stripPrefix = '';
            for ($i = 0; $i <= strlen(MODX_BASE_PATH); $i++) {
                if (strcmp(substr(MODX_BASE_PATH . 'core/', 0, $i), substr(MODX_CORE_PATH, 0, $i)) === 0) {
                    $stripPrefix = substr(MODX_BASE_PATH, 0, $i);
                }
            }
        }
        return $stripPrefix;
    }

    /**
     * Display a shutdown message (i.e. in case of reaching the maximum
     * execution time) instead of just a blank result
     */
    public function shutdown()
    {
        $error = error_get_last();
        if (is_array($error) && isset($error['type']) && $error['type'] === E_ERROR) {
            $message = (isset($error['message'])) ? $error['message'] : 'Unknown Error!';
            if ($this->getOption('debug')) {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Script terminated with the error ' . $message, $this->getOption('logTarget'), 'BackupMODX');
            }
            if (php_sapi_name() == 'cli') {
                fwrite(STDERR, $message . "\n");
                exit(1);
            } else {
                exit(json_encode(array(
                    'success' => false,
                    'message' => $message,
                )));
            }
        }
    }
}
