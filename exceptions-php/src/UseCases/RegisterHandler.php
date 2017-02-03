<?php

namespace EEException\UseCases;

require_once(realpath(__DIR__).'/../vendor/autoload.php');

class RegisterHandler
{
    /**
     * @var \EEException\Notifier\NotifierInterface
     */
    private $_notifier;

    /**
     * @param \EEException\Notifier\NotifierInterface $_notifier
     */
    function __construct(array $notifier_config)
    {
        $this->_notifier = new \Airbrake\Notifier($notifier_config);
    }

    /**
     * @param string $message
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function execute()
    {
        // Set global notifier instance.
        \Airbrake\Instance::set($this->_notifier);

        // Register error and exception handlers.
        $handler = new \Airbrake\ErrorHandler($this->_notifier);
        $handler->register();

        return true;
    }
}
