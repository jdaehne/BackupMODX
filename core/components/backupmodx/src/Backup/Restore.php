<?php
/**
 * BackupMODX
 *
 * Copyright 2015-2019 by Jan Dähne <thomas.jakobi@partout.info>
 *
 * @package backupmodx
 * @subpackage classfile
 */

namespace BackupMODX\Backup;

use BackupMODX;
use BackupMODX\Helper\SQLImport;
use Exception;
use modX;
use xPDO;

/**
 * Class Restore
 */
class Restore extends BackupMODX
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
     * Restore backup
     *
     * @param string $backup
     * @return bool
     */
    public function restoreBackup($backup)
    {
        $databasefile = $this->getOption('targetPath') . $backup . '/' . $backup . '.sql';

        if (file_exists($databasefile)) {
            try {
                SQLImport::importSQL($this->modx, $databasefile);
            } catch (Exception $e) {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'PDO Exeption ' . $e->getMessage(), $this->getOption('logTarget'), 'BackupMODX');
                return $this->modx->lexicon('backupmodx.err_pdo_exeption');
            }

            if ($this->getOption('debug')) {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Restore backup: ' . $databasefile, $this->getOption('logTarget'), 'BackupMODX');
            }

            return true;
        }

        return $this->modx->lexicon('backupmodx.err_backup_ne');
    }
}
