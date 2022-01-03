<?php
/**
 * Remove backup
 *
 * @package backupmodx
 * @subpackage processors
 */

class BackupMODXRemovebackupProcessor extends modProcessor
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
        $this->backupmodx->removeBackup($_SESSION['tmpActiveBackup']['filename']);
        unset($_SESSION['tmpActiveBackup']);

        return $this->outputArray(array(), 0);
    }
}

return 'BackupMODXRemovebackupProcessor';
