<?php
/**
 * Download
 *
 * @package backupmodx
 * @subpackage processors
 */

class BackupMODXDownloadProcessor extends modProcessor
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
        $file = $this->getProperty('file', '');
        $folder = $this->getProperty('folder', '');

        $download = $this->backupmodx->getFile($folder, $file);
        if (file_exists($download)) {
            switch (pathinfo($file, PATHINFO_EXTENSION)) {
                case 'sql':
                    header('Content-Type: application/sql', false);
                    break;
                case 'txt':
                    header('Content-Type: text/plain', false);
                    break;
                case 'zip':
                    header('Content-Type: application/zip', false);
                    break;
                default:
                    die();
            }
            header('Content-Disposition: attachment; filename="' . $file . '"');
            header('Content-Length: ' . @filesize($download));

            $chunksize = 1*(1024*1024); // how many bytes per chunk
            $handle = fopen($download, 'rb');

            if ($handle === false) {
                return false;
            }
            while (!feof($handle)) {
                $buffer = fread($handle, $chunksize);
                print $buffer;
            }

            fclose($handle);
            die();
        }
        return false;
    }
}

return 'BackupMODXDownloadProcessor';
