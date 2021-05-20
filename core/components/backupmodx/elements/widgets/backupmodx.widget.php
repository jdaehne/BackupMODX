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
     * @var BackupMODX
     */
    public $backupmodx;

    public $cssBlockClass = 'dashboard-block-quadro" id="dashboard-block-quadro';

    /**
     * Renders the content of the block in the appropriate size
     * @return string
     */
    public function process()
    {
        $output = $this->render();
        $modxVersion = $this->modx->getVersionData();
        if (!empty($output)) {
            $widgetArray = $this->widget->toArray();
            $widgetArray['content'] = $output;
            $widgetArray['class'] = $this->cssBlockClass;
            $widgetArray['name_trans'] .= '<span class="quadro-widget-about modx' . $modxVersion['version'] . '" onclick="BackupMODX.about()"><img width="80" height="20" src="' . $this->backupmodx->getOption('assetsUrl') . 'img/mgr/quadro-small.png" srcset="' . $this->backupmodx->getOption('assetsUrl') . 'img/mgr/quadro-small@2x.png 2x" alt="Quadro"></span>';
            $output = $this->getFileChunk('dashboard/block.tpl', $widgetArray);
            $output = preg_replace('@\[\[(?:[^]\[]+|(?R))*+]]@is', '', $output);
        }
        return $output;
    }

    /**
     * modDashboardWidgetBackupModx constructor.
     * @param xPDO $modx
     * @param modDashboardWidget $widget
     * @param modManagerController $controller
     */
    public function __construct(xPDO &$modx, &$widget, &$controller)
    {
        parent::__construct($modx, $widget, $controller);

        $corePath = $this->modx->getOption('backupmodx.core_path', null, $this->modx->getOption('core_path') . 'components/backupmodx/');
        $this->backupmodx = $this->modx->getService('backupmodx', 'BackupMODX', $corePath . 'model/backupmodx/', array(
            'core_path' => $corePath
        ));

        $this->controller->addLexiconTopic($this->widget->get('lexicon'));
    }

    /**
     * @return string
     */
    public function render()
    {
        $groups = $this->backupmodx->getOption('groups');
        if (strpos($groups, ',') !== false) {
            $groups = array_map('trim', explode(',', $groups));
        }
        if ($this->modx->user->isMember($groups)) {
            $this->controller->addLexiconTopic($this->widget->get('lexicon'));

            $assetsUrl = $this->backupmodx->getOption('assetsUrl');
            $jsUrl = $this->backupmodx->getOption('jsUrl') . 'mgr/';
            $jsSourceUrl = $assetsUrl . '../../../source/js/mgr/';
            $cssUrl = $this->backupmodx->getOption('cssUrl') . 'mgr/';
            $cssSourceUrl = $assetsUrl . '../../../source/css/mgr/';

            if ($this->backupmodx->getOption('debug') && ($this->backupmodx->getOption('assetsUrl') != MODX_ASSETS_URL . 'components/backupmodx/')) {
                $this->controller->addJavascript($jsSourceUrl . 'backupmodx.js?v=v' . $this->backupmodx->version);
                $this->controller->addJavascript($jsSourceUrl . 'helper/util.js?v=v' . $this->backupmodx->version);
                $this->controller->addCss($cssSourceUrl . 'backupmodx.css?v=v' . $this->backupmodx->version);
            } else {
                $this->controller->addJavascript($jsUrl . 'backupmodx.min.js?v=v' . $this->backupmodx->version);
                $this->controller->addCss($cssUrl . 'backupmodx.min.css?v=v' . $this->backupmodx->version);
            }
            $this->controller->addHtml('<script>Ext.onReady(function() {
    BackupMODX.config = ' . json_encode($this->backupmodx->options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . ';
});</script>');

            return $this->getFileChunk($this->backupmodx->getOption('templatesPath') . 'backupmodx.widget.tpl', array(
                'backupmodx.assets_url' => $this->backupmodx->getOption('assetsUrl')
            ));
        } else {
            return '';
        }
    }
}

return 'modDashboardWidgetBackupModx';
