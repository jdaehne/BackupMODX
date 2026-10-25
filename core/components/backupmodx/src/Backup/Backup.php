<?php
/**
 * Backup Backup
 *
 * @package backupmodx
 * @subpackage classfile
 */

namespace BackupMODX\Backup;

use BackupMODX\BackupMODX;
use FilesystemIterator;
use Madnest\Madzipper\Madzipper;
use Exception;
use Ifsnop\Mysqldump as IMysqldump;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Class Backup
 */
class Backup extends BackupMODX
{
    /**
     * @var array $excludeFolders Default excluded folders
     */
    public $excludeFolders = array(
        '{core_path}cache',
        '{core_path}packages',
        '{core_path}vendor',
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
        $excludeFolders = array_merge($this->excludeFolders, array($this->getOption('targetPath')), $this->getListOption('excludeFolders'));
        $excludeFolders = array_map(array($this, 'translatePath'), $excludeFolders);
        $excludeFolders = array_map(array($this, 'trimPath'), $excludeFolders);
        $excludeFiles = array_merge($this->excludeFiles, $this->getListOption('excludeFiles'));

        $this->debug('Excluded ' . count($excludeFolders) . ' folders.');
        $this->debug('Folder list:' . "\n" . print_r($excludeFolders, true));

        $stripPrefix = self::stripPrefix();
        $modxPaths = array(
            MODX_ASSETS_PATH,
            MODX_CONNECTORS_PATH,
            MODX_CORE_PATH,
            MODX_MANAGER_PATH,
        );

        // Collect all files to back up in the base path (without the MODX folders) and in the different MODX folders
        $files = $this->getFiles(MODX_BASE_PATH, array_merge($modxPaths, $excludeFolders), $excludeFiles, $stripPrefix);
        foreach ($modxPaths as $modxPath) {
            $files = array_merge($files, $this->getFiles($modxPath, $excludeFolders, $excludeFiles, $stripPrefix));
        }

        if ($this->getOption('debug')) {
            $this->log('File backup ' . count($files) . ' files.');
            $fileSizeList = [];
            foreach ($files as $file) {
                $fileSizeList[] = $stripPrefix . $file . ': ' . $this->humanFilesize(filesize($stripPrefix . $file));
            }
            $this->log('File backup files list:' . "\n" . print_r($fileSizeList, true));
        }

        $filename = $filename ?: $this->createFilename();
        $target = $this->targetPath($filename) . $filename . '.zip';

        $this->debug('File backup target: ' . $target);

        try {
            $zipper = new Madzipper();
            $zipper->make($target);
            foreach ($files as $path => $name) {
                $zipper->add($path, $name);
            }
            $zipper->close();
            return $this->fileResult($filename, $target);
        } catch (Exception $e) {
            $msg = 'Zip Error: ' . $e->getMessage();
            $this->debug($msg);
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

        $filename = $filename ?: $this->createFilename();
        $target = $this->targetPath($filename) . $filename . '.sql';

        try {
            $dump = new IMysqldump\Mysqldump('mysql:host=' . $host . ';dbname=' . $database, $username, $password, array(
                'add-drop-table' => true,
                'exclude-tables' => array(
                    $this->modx->getTableName('modSession')
                )
            ));
            $dump->start($target);

            $this->debug('Database backup.');
            $this->debug('Database backup size: ' . $this->humanFilesize(filesize($target)));
            $this->debug('Database backup target: ' . $target);

            return $this->fileResult($filename, $target);
        } catch (Exception $e) {
            $msg = 'mysqldump Error: ' . $e->getMessage();
            $this->debug($msg);
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
        $filename = $filename ?: $this->createFilename();
        $target = $this->targetPath($filename) . $filename . '.txt';

        file_put_contents($target, $message);

        $this->debug('Backup note.');

        return $this->fileResult($filename, $target);
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
        if (!$this->modx->getCacheManager()->writeTree($targetPath)) {
            return $this->modx->lexicon('backupmodx.err_create_target_path');
        }

        $database = $database ? $this->backupDatabase($filename) : false;
        $note = ($message != '') ? $this->createNote($message, $filename) : '';
        $files = $files ? $this->backupFiles($filename) : false;

        if (!$database && !$note && !$files) {
            $this->deleteFolder($targetPath);
            return $this->modx->lexicon('backupmodx.err_create_backup');
        }

        return array(
            'filename' => $filename,
            'path' => $targetPath,
            'files' => array(
                'database' => $database ?: '',
                'files' => $files ?: '',
                'note' => $note ?: '',
            ),
        );
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
        $filename = $filename ?: $this->createFilename();

        return $this->getOption('targetPath') . $filename . '/';
    }

    /**
     * Get the result array of a created backup file
     *
     * @param string $filename
     * @param string $target
     * @return array
     */
    private function fileResult($filename, $target)
    {
        return array(
            'name' => $filename,
            'filename' => basename($target),
            'path' => $target,
            'size' => $this->humanFilesize(filesize($target)),
        );
    }

    /**
     * Get a comma separated option as array
     *
     * @param string $key
     * @return array
     */
    private function getListOption($key)
    {
        $value = $this->getOption($key);

        return ($value != '') ? explode(',', $value) : array();
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
     * @param string $path
     * @return string
     */
    public function trimPath($path)
    {
        return rtrim($path, '/');
    }

    /**
     * Get an array of all files to back up
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
            return rtrim(trim($item), '/\\');
        }, $excludeFolders);
        $excludeFiles = array_map(function ($item) {
            return '~' . str_replace('~', '\~', trim($item)) . '~';
        }, $excludeFiles);
        $stripPrefix = rtrim($stripPrefix, '/\\');

        /**
         * @param SplFileInfo $file
         * @param mixed $key
         * @param RecursiveCallbackFilterIterator $iterator
         * @return bool True if you need to recurse or if the item is acceptable
         */
        $filter = function ($file, $key, $iterator) use ($excludeFolders, $excludeFiles) {
            if ($file->isDir()) {
                return $iterator->hasChildren() && !in_array($key, $excludeFolders);
            }
            if ($file->isFile()) {
                foreach ($excludeFiles as $excludeFile) {
                    if (preg_match($excludeFile, $file->getFilename())) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        };

        $innerIterator = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator($innerIterator, $filter));

        $files = array();
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
            return MODX_BASE_PATH;
        }

        $stripPrefix = '';
        for ($i = 0; $i <= strlen(MODX_BASE_PATH); $i++) {
            if (strcmp(substr(MODX_BASE_PATH . 'core/', 0, $i), substr(MODX_CORE_PATH, 0, $i)) === 0) {
                $stripPrefix = substr(MODX_BASE_PATH, 0, $i);
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
            $this->debug('Script terminated with the error ' . $message);
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
