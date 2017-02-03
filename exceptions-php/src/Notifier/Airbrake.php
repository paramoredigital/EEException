<?php

namespace EEException\Notifier;

require_once(realpath(__DIR__).'/NotifierInterface.php');
require_once(realpath(__DIR__).'/../vendor/autoload.php');

class Airbrake implements NotifierInterface
{
    /**
     * @var string
     */
    protected $_apiKey;

    /**
     * @var array
     */
    protected $_airbrake_config = array(
        'host' => '',
        'apiKey' => '',
        'environment' => '',
        'component' => '',
        'action' => '',
        'url' => '',
        'rootDirectory' => '',
        'timeout' => 30
    );

    /**
     * @param array $notifier_config
     */
    function __construct(array $notifier_config)
    {
        $this->_apiKey = $notifier_config['apiKey'];
        unset($notifier_config['apiKey']);

        $this->_airbrake_config = array_merge($this->_airbrake_config, $notifier_config);
    }

    /**
     * @param string $message
     * @return bool
     */
    public function SendErrorString($code, $message)
    {
        $apiKey  = $this->_apiKey;
        $options = $this->_getConfigOptions();

        // Create new Notifier instance.
        $notifier = new \Airbrake\Notifier($options);

        switch ($code)
        {
            case E_NOTICE:
            case E_USER_NOTICE:
                $exc = new \Airbrake\Errors\Notice($message, debug_backtrace());
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $exc = new \Airbrake\Errors\Warning($message, debug_backtrace());
                break;
            case E_ERROR:
            case E_CORE_ERROR:
            case E_RECOVERABLE_ERROR:
                $exc = new \Airbrake\Errors\Fatal($message, debug_backtrace());
                break;
            case E_USER_ERROR:
            default:
                $exc = new \Airbrake\Errors\Error($message, debug_backtrace());
        }
        \Airbrake\Instance::set($notifier);
        \Airbrake\Instance::notify($exc);
    }

    /**
     * @return array
     */
    protected function _getConfigOptions()
    {
        return $this->_airbrake_config;
    }
}
