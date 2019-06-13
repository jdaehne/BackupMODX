<?php
/**
 * Get backups
 *
 * @package backupmodx
 * @subpackage processor
 */

class BackupMODXGetbackupsProcessor extends modProcessor
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
        if ($backups = $this->backupmodx->getBackups()) {
            $restores = array();
            foreach ($backups as $backup) {
                if (isset($backup['files']['database'])) {
                    $restores[] = $backup;
                }
            }
            if ($restores) {
                return $this->outputArray($restores, 1);
            } else {
                return $this->failure($this->modx->lexicon('backupmodx.err_no_restore_available'));
            }
        } else {
            return $this->failure($this->modx->lexicon('backupmodx.err_no_backups_available'));
        }
    }
}

return 'BackupMODXGetbackupsProcessor';
