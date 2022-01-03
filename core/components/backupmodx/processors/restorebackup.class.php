<?php
/**
 * Restore backup
 *
 * @package backupmodx
 * @subpackage processors
 */

// Timeouts
set_time_limit(0);
ini_set('max_execution_time', 0);

use BackupMODX\Backup\Restore;

class BackupMODXRestorebackupProcessor extends modProcessor
{
    /** @var BackupMODX $backupmodx */
    public $backupmodx;

    /**
     * BackupMODXGetProcessor constructor.
     * @param modX $modx A reference to the modX instance
     * @param array $properties An array of properties
     */
    public function __construct(modX $modx, array $properties)
    {
        parent::__construct($modx, $properties);

        $corePath = $modx->getOption('backupmodx.core_path', null, $modx->getOption('core_path') . 'components/backupmodx/');
        $this->backupmodx = $modx->getService('backupmodx', 'BackupMODX', $corePath . 'model/backupmodx/', array(
            'core_path' => $corePath
        ));
    }

    public function process()
    {
        $database = $this->getProperty('database');

        $restore = new Restore($this->modx);
        $result = $restore->restoreBackup($database);
        if ($result === true) {
            return $this->success();
        } else {
            return $this->failure($result);
        }
    }
}

return 'BackupMODXRestorebackupProcessor';
