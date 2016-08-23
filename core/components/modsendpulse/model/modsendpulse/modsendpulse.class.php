<?php

ini_set('display_errors', 1);
ini_set('error_reporting', -1);

/**
 * The base class for modsendpulse.
 */
class modsendpulse
{
    /* @var modX $modx */
    public $modx;

    /** @var mixed|null $namespace */
    public $namespace = 'modsendpulse';
    /** @var array $config */
    public $config = array();
    /** @var array $initialized */
    public $initialized = array();

    /** @var $token */
    protected $token;

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->getOption('core_path', $config,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modsendpulse/');
        $assetsPath = $this->getOption('assets_path', $config,
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/modsendpulse/');
        $assetsUrl = $this->getOption('assets_url', $config,
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/modsendpulse/');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'namespace'       => $this->namespace,
            'connectorUrl'    => $connectorUrl,
            'assetsBasePath'  => MODX_ASSETS_PATH,
            'assetsBaseUrl'   => MODX_ASSETS_URL,
            'assetsPath'      => $assetsPath,
            'assetsUrl'       => $assetsUrl,
            'actionUrl'       => $assetsUrl . 'action.php',
            'cssUrl'          => $assetsUrl . 'css/',
            'jsUrl'           => $assetsUrl . 'js/',
            'corePath'        => $corePath,
            'modelPath'       => $corePath . 'model/',
            'processorsPath'  => $corePath . 'processors/',
            'templatesPath'   => $corePath . 'elements/templates/mgr/',
            'jsonResponse'    => true,
            'prepareResponse' => true,
            'showLog'         => false,
        ), $config);

        $this->modx->addPackage('modsendpulse', $this->getOption('modelPath'));
        $this->modx->lexicon->load('modsendpulse:default');
        $this->namespace = $this->getOption('namespace', $config, 'modsendpulse');
    }

    /**
     * @param       $n
     * @param array $p
     */
    public function __call($n, array$p)
    {
        echo __METHOD__ . ' says: ' . $n;
    }

    /**
     * @param       $key
     * @param array $config
     * @param null  $default
     *
     * @return mixed|null
     */
    public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
    {
        $option = $default;
        if (!empty($key) AND is_string($key)) {
            if ($config != null AND array_key_exists($key, $config)) {
                $option = $config[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}_{$key}");
            }
        }
        if ($skipEmpty AND empty($option)) {
            $option = $default;
        }

        return $option;
    }

    public function initialize($ctx = 'web', array $scriptProperties = array())
    {
        if (isset($this->initialized[$ctx])) {
            return $this->initialized[$ctx];
        }

        $this->modx->error->reset();
        $this->config = array_merge($this->config, $scriptProperties, array('ctx' => $ctx));

        if ($ctx != 'mgr' AND (!defined('MODX_API_MODE') OR !MODX_API_MODE)) {

        }

        $load = !empty($this->getToken());
        $this->initialized[$ctx] = $load;

        return $load;
    }


    /**
     * return lexicon message if possibly
     *
     * @param string $message
     *
     * @return string $message
     */
    public function lexicon($message, $placeholders = array())
    {
        $key = '';
        if ($this->modx->lexicon->exists($message)) {
            $key = $message;
        } elseif ($this->modx->lexicon->exists($this->namespace . '_' . $message)) {
            $key = $this->namespace . '_' . $message;
        }
        if ($key !== '') {
            $message = $this->modx->lexicon->process($key, $placeholders);
        }

        return $message;
    }

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return array|string
     */
    public function failure($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => false,
            'message' => $this->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
    }

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return array|string
     */
    public function success($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => true,
            'message' => $this->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array
     */
    public function explodeAndClean($array, $delimiter = ',')
    {
        $array = explode($delimiter, $array);     // Explode fields to array
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        return $array;
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array|string
     */
    public function cleanAndImplode($array, $delimiter = ',')
    {
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        $array = implode($delimiter, $array);

        return $array;
    }

    /**
     * @param array  $array
     * @param string $prefix
     *
     * @return array
     */
    public function flattenArray(array $array = array(), $prefix = '')
    {
        $outArray = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $outArray = $outArray + $this->flattenArray($value, $prefix . $key . '.');
            } else {
                $outArray[$prefix . $key] = $value;
            }
        }

        return $outArray;
    }


    /**
     * @param string $message
     * @param array  $data
     * @param bool   $showLog
     * @param bool   $writeLog
     */
    public function log($message = '', $data = array(), $showLog = false)
    {
        if ($showLog OR $this->getOption('showLog', null, false, true)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $message);
            if (!empty($data)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($data, 1));
            }
        }
    }


    /**
     * Sets data to cache
     *
     * @param mixed $data
     * @param mixed $options
     *
     * @return string $cacheKey
     */
    public function setCache($data = array(), $options = array())
    {
        $cacheKey = $this->getCacheKey($options);
        $cacheOptions = $this->getCacheOptions($options);
        if (!empty($cacheKey) AND !empty($cacheOptions) AND $this->modx->getCacheManager()) {
            $this->modx->cacheManager->set(
                $cacheKey,
                $data,
                $cacheOptions[xPDO::OPT_CACHE_EXPIRES],
                $cacheOptions
            );
        }

        return $cacheKey;
    }

    /**
     * Returns data from cache
     *
     * @param mixed $options
     *
     * @return mixed
     */
    public function getCache($options = array())
    {
        $cacheKey = $this->getCacheKey($options);
        $cacheOptions = $this->getCacheOptions($options);
        $cached = '';
        if (!empty($cacheOptions) AND !empty($cacheKey) AND $this->modx->getCacheManager()) {
            $cached = $this->modx->cacheManager->get($cacheKey, $cacheOptions);
        }

        return $cached;
    }


    /**
     * @param array $options
     *
     * @return bool
     */
    public function clearCache($options = array())
    {
        $cacheKey = $this->getCacheKey($options);
        $cacheOptions = $this->getCacheOptions($options);
        $cacheOptions['cache_key'] .= $cacheKey;
        if (!empty($cacheOptions) AND $this->modx->getCacheManager()) {
            return $this->modx->cacheManager->clean($cacheOptions);
        }

        return false;
    }

    /**
     * Returns array with options for cache
     *
     * @param $options
     *
     * @return array
     */
    public function getCacheOptions($options = array())
    {
        if (empty($options)) {
            $options = $this->config;
        }
        $cacheOptions = array(
            xPDO::OPT_CACHE_KEY     => empty($options['cache_key'])
                ? 'default' : 'default/' . $this->namespace . '/',
            xPDO::OPT_CACHE_HANDLER => !empty($options['cache_handler'])
                ? $options['cache_handler'] : $this->modx->getOption('cache_resource_handler', null, 'xPDOFileCache'),
            xPDO::OPT_CACHE_EXPIRES => $options['cacheTime'] !== ''
                ? (integer)$options['cacheTime'] : (integer)$this->modx->getOption('cache_resource_expires', null, 0),
        );

        return $cacheOptions;
    }

    /**
     * Returns key for cache of specified options
     *
     * @var mixed $options
     * @return bool|string
     */
    public function getCacheKey($options = array())
    {
        if (empty($options)) {
            $options = $this->config;
        }
        if (!empty($options['cache_key'])) {
            return $options['cache_key'];
        }
        $key = !empty($this->modx->resource) ? $this->modx->resource->getCacheKey() : '';

        return $key . '/' . sha1(serialize($options));
    }

    public function getToken($cache = true)
    {
        $tmp = array(
            'cache_key' => 'token/' . $this->getOption('client_id', null),
            'cacheTime' => 3540,
        );
        if (!$cache OR !$data = $this->getCache($tmp)) {
            $data = $this->sendPulseGetToken();
            $this->setCache($data, $tmp);
        }
        $this->token = $this->getOption('access_token', $data);

        return $this->token;
    }


    protected function sendPulseApiUrl($mode = '')
    {
        $url = $this->getOption('api_url', null, 'https://api.sendpulse.com', true);
        $url = rtrim($url, '/') . '/' . $mode;

        return $url;
    }

    protected function sendPulseGetToken(array $params = array())
    {
        $mode = '/oauth/access_token/';
        $params = array_merge(array(
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->getOption('client_id', null),
            'client_secret' => $this->getOption('client_secret', null),
        ), $params);
        $data = $this->request($mode, $params, false);

        return $data;
    }

    public function sendPulseCreateAddressBook(array $params = array())
    {
        $mode = '/addressbooks/';
        $params = array_merge(array(
            'bookName' => null,
        ), $params);
        $data = $this->request($mode, $params);

        return $data;
    }

    /**
     * @param string $mode
     * @param null   $params
     * @param string $url
     *
     * @return array|mixed
     */
    public function request($mode = '', $params = null, $isToken = true, $url = '')
    {
        $mode = trim($mode, '/');

        if (empty($url)) {
            $url = $this->sendPulseApiUrl($mode);
        }

        $ch = curl_init($url);

        if ($isToken AND $this->token) {
            $headers = array('Authorization: Bearer ' . $this->token);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt_array(
            $ch,
            array(
                CURLOPT_POST           => count($params),
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_RETURNTRANSFER => 1,
            )
        );

        if (count($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        $data = curl_exec($ch);
        curl_close($ch);
        $data = $this->prepareData($data, $mode);
        $this->log('', $data);

        return $data;
    }

    /**
     * @param array  $response
     * @param string $mode
     *
     * @return array
     */
    protected function prepareData($data, $mode = '')
    {
        $mode = strtolower($mode);

        $this->modx->log(1, print_r($mode, 1));

        switch ($mode) {
            case '':
                break;
            case 'oauth/access_token':
                $data = json_decode($data, true);
                break;
            default:
                $data = json_decode($data, true);
               /* if (empty($data['ok'])) {
                    $this->log('', $data, true);
                }
                $data = isset($data['result']) ? $data['result'] : array();*/
                break;
        }

        return $data;
    }
}