<?php

namespace EEException\Notifier;

interface NotifierInterface
{
    /**
     * @param array $notifier_config
     */
    public function __construct(array $notifier_config);

    /**
     * @param string $message
     * @return bool
     */
    public function SendErrorString($message);
}
