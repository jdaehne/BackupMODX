<?php

class BackupMODX {


    /**
     * BackupMODX constructor
     *
     * @param MODX $modx A reference to the MODX instance.
     * @param array $options An array of options. Optional.
     */
     public function __construct(MODX $modx, array $config = array())
    {
        // init modx
        $this->modx = $modx;

        // config
        $this->targetPath = $this->modx->getOption('backupmodx.targetPath', null, '{assets_path}backups/', true);
        $this->targetPath = str_replace("{assets_path}", MODX_ASSETS_PATH, $this->targetPath);
        $this->targetPath = str_replace("{core_path}", MODX_CORE_PATH, $this->targetPath);
        $this->excludes = $this->modx->getOption('backupmodx.excludes');
        $this->excludes = str_replace("{assets_path}", MODX_ASSETS_PATH, $this->excludes);
        $this->zipAlias = $this->modx->getOption('backupmodx.zipAlias', null, 'zip', true);
        $this->mysqldumpAlias = $this->modx->getOption('backupmodx.mysqldumpAlias', null, 'mysqldump', true);
        $this->mysqlAlias = $this->modx->getOption('backupmodx.mysqlAlias', null, 'mysql', true);

        $this->config = $this->modx->getConfig();
    }


    // Backup Files
    public function backupFiles($filename = NULL)
    {

        // Creating exclude-files command
	    if (!empty($this->excludes)) {
	        $excludes = explode(",", $this->excludes);
	        $excluidesCommand = '';
	    }

        $modx_base_path = MODX_BASE_PATH;
		$modx_core_path = MODX_CORE_PATH;

        $filename = $filename ? $filename : $this->createFilename();
        $target = $this->targetPath($filename) . $filename . '.zip';

        $alias = $this->zipAlias;

        // Generate exclude command
        if (is_array($excludes)) {
            foreach ($excludes as $exclude){
                $exclude = trim($exclude);
                if (is_dir($exclude)) {
                    $exclude = rtrim($exclude, '/') . '/\*';
                }
                $excludeCommand .= ' ' . $exclude;
            }
        }

        // Zip files
        exec("{$alias} -r {$target} {$modx_base_path} {$modx_core_path} -x {$modx_core_path}cache/\* {$this->targetPath}\* {$excludeCommand}");

        return array(
            'name' => $filename,
            'filename' => basename($target),
            'path' => $target,
            'size' => round(filesize($target) / 1000000, 2),
        );
    }

    // Backup Database
    public function backupDatabase($filename = NULL)
    {
        unset($return);

        $host = $this->config['host'];
        $username = $this->config['username'];
        $password = $this->config['password'];
        $database = $this->config['dbname'];

        $filename = $filename ? $filename : $this->createFilename();
        $target = $this->targetPath($filename) . $filename . '.sql';

        $alias = $this->mysqldumpAlias;

        exec("{$alias} --host={$host} --user={$username} --password={$password} --databases {$database} --no-create-db --default-character-set=utf8 --result-file={$target}", $output, $return);


        // If mysql dump fails try different way
        if ($return) {
            //die('Error: ' . $return . $output);
        }

        return array(
            'name' => $filename,
            'filename' => basename($target),
            'path' => $target,
            'size' => round(filesize($target) / 1000000, 2),
        );
    }


    // Create Filename
    public function createFilename()
    {
        return date("Y-m-d--His") . '--' . substr(md5(openssl_random_pseudo_bytes(20)),-4);
    }


    // Create Note
    public function createNote($message, $filename = NULL)
    {
        $filename = $filename ? $filename : $this->createFilename();
        $target = $this->targetPath($filename) . $filename . '.txt';

        $fp = fopen($target,"wb");
        fwrite($fp, $message);
        fclose($fp);

        return array(
            'name' => $filename,
            'filename' => basename($target),
            'path' => $target,
            'size' => round(filesize($target) / 1000000, 2),
        );
    }


    // Remove Backup
    public function backupMODX($files = true, $database = true, $message = "")
    {
        set_time_limit(0);
	    ini_set('max_execution_time', 0);

        $filename = $this->createFilename();
        $targetPath = $this->targetPath($filename);

        // Create Backupdirectory if not exists
        exec("mkdir -p $targetPath");

        if ($database == true) {
            $database = $this->backupDatabase($filename);
        }

        if ($message != "") {
            $note = $this->createNote($message, $filename);
        }

        if ($files == true) {
            $files = $this->backupFiles($filename);
        }

        return array(
            'filename' => $filename,
            'path' => $targetPath,
            'files' => array(
                'database' => !$database ? array() : $database,
                'files' => !$files ? array() : $files,
                'note' => !$note ? array() : $note,
            ),
        );
    }


    // Get Backups
    public function targetPath($filename)
    {
        $filename = $filename ? $filename : $this->createFilename();

        return $this->targetPath . $filename . '/';
    }


    // Get Backups
    public function getBackups()
    {
        $backups = array();
        $folders = preg_grep('/^([^.])/', scandir($this->targetPath)); // ignore hidden files: "." and ".."

        foreach ($folders as $folder) {
            $filenames = preg_grep('/^([^.])/', scandir($this->targetPath . $folder));

            $files = array();
            $note = '';

            foreach ($filenames as $file) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);

                if ($ext == 'zip') {
                    $files['files'] = $file;
                }

                if ($ext == 'sql') {
                    $files['database'] = $file;
                }

                if ($ext == 'txt') {
                    $files['note'] = $file;
                    $note = file_get_contents($this->targetPath . $folder . '/' . $file);
                }
            }


            $backups[] = array(
                'name' => $folder,
                'path' => $this->targetPath,
                'date' => filemtime($this->targetPath . $folder),
                'date_format' => date("F d Y - H:i a", filemtime($this->targetPath . $folder)),
                'note' => $note,
                'files' => $files,
            );
        }

        return array_reverse($backups);
    }

    // Restore Backup
    public function restoreBackup($backup)
    {
        $host = $this->config['host'];
        $username = $this->config['username'];
        $password = $this->config['password'];
        $database = $this->config['dbname'];

        $alias = $this->mysqlAlias;

        $databasefile = $this->targetPath . $backup . '/' . $backup . '.sql';

        if (file_exists($databasefile)) {
            exec("{$alias} --host={$host} --user={$username} --password={$password} {$database} < {$databasefile}", $output, $return);
            return true;
        }

        return false;
    }

    // Remove Backup
    public function removeBackup($backup)
    {
        $path = $this->targetPath . $backup;

        if (file_exists($path)) {
            exec("rm -rf {$path}");
            return true;
        }

        return false;
    }


    // Clean old Cron Backups
    public function cleanCronBackups($maxDatabase = 10, $maxFiles = 5)
    {
        $max = $maxFiles > $maxDatabase ? $maxFiles : $maxDatabase;

        $backups = $this->getBackups();

        // Remove Files
        foreach (array_slice($backups, $maxFiles) as $backup) {
            $file = $backup['path'] . $backup['filename'] . '/' . $backup['files']['files'];
            if (file_exists($file)) {
                unlink($file);
            }
        }

        // Remove Databases
        foreach (array_slice($backups, $maxDatabase) as $backup) {
            $file = $backup['path'] . $backup['filename'] . '/' . $backup['files']['database'];
            if (file_exists($file)) {
                unlink($file);
            }
        }

        // Remove empty Backups
        foreach (array_slice($backups, $max) as $backup) {
            $this->removeBackup($backup['filename']);
        }
    }


    // Get Backup-File
    public function getFile($folder, $filename)
    {
        $file = $this->targetPath . $folder . '/' . $filename;

        if (file_exists($file)) {
            return $file;
        }

        return false;
    }

}
