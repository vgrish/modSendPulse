<?php

/**
 * Class modsendpulseMainController
 */
abstract class modsendpulseMainController extends modExtraManagerController
{
    /** @var modsendpulse $modsendpulse */
    public $modsendpulse;


    /**
     * @return void
     */
    public function initialize()
    {
        $corePath = $this->modx->getOption('modsendpulse_core_path', null,
            $this->modx->getOption('core_path') . 'components/modsendpulse/');
        require_once $corePath . 'model/modsendpulse/modsendpulse.class.php';

        $this->modsendpulse = new modsendpulse($this->modx);
        $this->addCss($this->modsendpulse->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->modsendpulse->config['jsUrl'] . 'mgr/modsendpulse.js');
        $this->addHtml('
		<script type="text/javascript">
			modsendpulse.config = ' . $this->modx->toJSON($this->modsendpulse->config) . ';
			modsendpulse.config.connector_url = "' . $this->modsendpulse->config['connectorUrl'] . '";
		</script>
		');

        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('modsendpulse:default');
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends modsendpulseMainController
{

    /**
     * @return string
     */
    public static function getDefaultController()
    {
        return 'home';
    }
}