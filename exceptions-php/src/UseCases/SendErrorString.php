<?php

namespace EEException\UseCases;

require_once(realpath(__DIR__) . '/../Notifier/Airbrake.php');

class SendErrorString
{
    /**
     * @var \EEException\Notifier\NotifierInterface
     */
    private $_notifier;

    /**
     * @param \EEException\Notifier\NotifierInterface $_notifier
     */
    function __construct(\EEException\Notifier\NotifierInterface $_notifier)
    {
        $this->_notifier = $_notifier;
    }

    /**
     * @param string $message
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function execute($code, $message)
    {
        if (!$code OR !in_array($code, array(E_NOTICE, E_USER_NOTICE, E_WARNING, E_USER_WARNING, E_ERROR, E_CORE_ERROR, E_RECOVERABLE_ERROR, E_USER_ERROR) ))
        {
            throw new \InvalidArgumentException('Error code must be a valid value.');
        }

        if (!is_string($message) OR strlen($message) === 0)
        {
            throw new \InvalidArgumentException('Message must be non-empty string.');
        }

        $this->_notifier->SendErrorString($code, $message);

        return true;
    }
}
