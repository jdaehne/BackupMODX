<?php
/**
 * BackupMODX Widget
 *
 * @package backupmodx
 * @subpackage widget
 */

class modDashboardWidgetBackupModx extends modDashboardWidgetInterface
{
    /**
     * @return string
     */
    public function render()
    {
        $corePath = $this->modx->getOption('backupmodx.core_path', null, $this->modx->getOption('core_path') . 'components/backupmodx/');
        $backupmodx = $this->modx->getService('backupmodx', 'BackupMODX', $corePath . '/model/backupmodx/', array(
            'core_path' => $corePath
        ));

        $groups = $backupmodx->getOption('groups');
        if (strpos($groups, ',') !== false) {
            $groups = array_map('trim', explode(',', $groups));
        }
        if ($this->modx->user->isMember($groups)) {
            $this->controller->addLexiconTopic($this->widget->get('lexicon'));

            $assetsUrl = $backupmodx->getOption('assetsUrl');
            $jsUrl = $backupmodx->getOption('jsUrl') . 'mgr/';
            $jsSourceUrl = $assetsUrl . '../../../source/js/mgr/';
            $cssUrl = $backupmodx->getOption('cssUrl') . 'mgr/';
            $cssSourceUrl = $assetsUrl . '../../../source/css/mgr/';

            if ($backupmodx->getOption('debug') && ($backupmodx->getOption('assetsUrl') != MODX_ASSETS_URL . 'components/backupmodx/')) {
                $this->controller->addJavascript($jsSourceUrl . 'backupmodx.js');
                $this->controller->addJavascript($jsSourceUrl . 'helper/util.js');
                $this->controller->addCss($cssSourceUrl . 'backupmodx.css');
            } else {
                $this->controller->addJavascript($jsUrl . 'backupmodx.min.js');
                $this->controller->addCss($cssUrl . 'backupmodx.min.css');
            }
            $this->controller->addHtml('<script>Ext.onReady(function() {
    BackupMODX.config = ' . json_encode($backupmodx->options, JSON_PRETTY_PRINT) . ';
});</script>');

            return $this->getFileChunk($backupmodx->getOption('templatesPath') . 'backupmodx.widget.tpl', array(
                'backupmodx.assets_url' => $backupmodx->getOption('assetsUrl')
            ));
        } else {
            return '';
        }
    }
}

return 'modDashboardWidgetBackupModx';
