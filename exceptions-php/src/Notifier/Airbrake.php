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
        'apiEndPoint' => '',
        'apiKey' => '',
        'environmentName' => '',
        'component' => '',
        'action' => '',
        'url' => '',
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
    public function SendErrorString($message)
    {
        $apiKey  = $this->_apiKey;
        $options = $this->_getConfigOptions();

        $client = new \Airbrake\Notifier($options);

        return $client->notify($message);
    }

    /**
     * @return array
     */
    protected function _getConfigOptions()
    {
        return $this->_airbrake_config;
    }
}
