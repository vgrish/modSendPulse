<?php

/**
 * The home manager controller for modsendpulse.
 *
 */
class modsendpulseHomeManagerController extends modsendpulseMainController
{
    /* @var modsendpulse $modsendpulse */
    public $modsendpulse;


    /**
     * @param array $scriptProperties
     */
    public function process(array $scriptProperties = array())
    {
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('modsendpulse');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->modsendpulse->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->modsendpulse->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $this->addJavascript($this->modsendpulse->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->modsendpulse->config['jsUrl'] . 'mgr/widgets/items.grid.js');
        $this->addJavascript($this->modsendpulse->config['jsUrl'] . 'mgr/widgets/items.windows.js');
        $this->addJavascript($this->modsendpulse->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->modsendpulse->config['jsUrl'] . 'mgr/sections/home.js');
        $this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({ xtype: "modsendpulse-page-home"});
		});
		</script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->modsendpulse->config['templatesPath'] . 'home.tpl';
    }
}