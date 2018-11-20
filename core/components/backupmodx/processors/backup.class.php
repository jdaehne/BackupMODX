<?php

class backup extends modProcessor {

    public function process() {

        $backupMODX = new BackupMODX($this->modx, $config = array());

        $database = $_REQUEST['database'] == 'false' ? false : true;
        $files = $_REQUEST['files'] == 'false' ? false : true;

        // create Backup
        if ($backup = $backupMODX->backupMODX($files, $database, $_REQUEST['note'])) {
            $_SESSION['tmpActiveBackup'] = $backup;
            return $this->outputArray($backup, 1);
        }
    }
}

return 'backup';
