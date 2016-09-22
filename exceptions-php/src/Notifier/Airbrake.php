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
        die(__METHOD__);
        $apiKey  = $this->_apiKey;
        $options = $this->_getConfigOptions();

        // Create new Notifier instance.
        $notifier = new \Airbrake\Notifier($options);
        // Set global notifier instance.
        \Airbrake\Instance::set($notifier);


        // return $client->notify($message);
        // Somewhere in the app...
        try {
            throw new Exception($message);
        } catch(Exception $e) {
            \Airbrake\Instance::notify($e);
        }
    }

    /**
     * @return array
     */
    protected function _getConfigOptions()
    {
        return $this->_airbrake_config;
    }
}
