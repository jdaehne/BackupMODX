<?php
/**
 * Backup Restore
 *
 * @package backupmodx
 * @subpackage classfile
 */

namespace BackupMODX\Backup;

use BackupMODX\BackupMODX;
use BackupMODX\Helper\SQLImport;
use Exception;

/**
 * Class Restore
 */
class Restore extends BackupMODX
{
    /**
     * Restore backup
     *
     * @param string $backup
     * @return bool|string
     */
    public function restoreBackup($backup)
    {
        $databasefile = $this->getOption('targetPath') . $backup . '/' . $backup . '.sql';

        if (!$this->isValidName($backup) || !file_exists($databasefile)) {
            return $this->modx->lexicon('backupmodx.err_backup_ne');
        }

        try {
            SQLImport::importSQL($this->modx, $databasefile);
        } catch (Exception $e) {
            $this->log('PDO Exeption ' . $e->getMessage());
            return $this->modx->lexicon('backupmodx.err_pdo_exeption');
        }

        $this->debug('Restore backup: ' . $databasefile);

        return true;
    }
}
