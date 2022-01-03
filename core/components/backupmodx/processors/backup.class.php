<?php
/**
 * Create backup
 *
 * @package backupmodx
 * @subpackage processors
 */

// Timeouts
set_time_limit(0);
ini_set('max_execution_time', 0);

use BackupMODX\Backup\Backup;

class BackupMODXBackupProcessor extends modProcessor
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
        $database = $this->getProperty('database') == 'true';
        $files = $this->getProperty('files') == 'true';
        $note = $this->getProperty('note');
        $timelimit = $this->backupmodx->getOption('timelimit', array(), 120);

        $backup = new Backup($this->modx);
        $result = $backup->backup($files, $database, $note, $timelimit);
        if (is_array($result)) {
            $_SESSION['tmpActiveBackup'] = $result;
            return $this->outputArray($result, 1);
        } else {
            return $this->failure($result);
        }
    }
}

return 'BackupMODXBackupProcessor';
