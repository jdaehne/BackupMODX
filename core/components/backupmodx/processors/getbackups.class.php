<?php

class getbackups extends modProcessor {

    public function process() {

        $backupMODX = new BackupMODX($this->modx, $config = array());

        // get Backups
        if ($backups = $backupMODX->getBackups()) {
            return $this->outputArray($backups, 1);
        }
    }
}

return 'getbackups';
