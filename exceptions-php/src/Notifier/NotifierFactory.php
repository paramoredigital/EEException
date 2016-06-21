<?php

namespace EEException\Notifier;

require_once(realpath(__DIR__) . '/NotifierInterface.php');
require_once(realpath(__DIR__) . '/Airbrake.php');
require_once(realpath(__DIR__) . '/SystemEmail.php');
require_once(realpath(__DIR__) . '/../Exceptions/NotifierNotFoundException.php');

class NotifierFactory
{
    const NOTIFIER_AIRBRAKE = 'airbrake';
    const NOTIFIER_SYSTEM_EMAIL = 'systememail';

    /**
     * @param string $type
     * @param array $config
     * @return NotifierInterface
     * @throws NotifierNotFoundException
     */
    public static function getNotifier($type, $config = array())
    {
        switch (strtolower($type)) {
            case self::NOTIFIER_AIRBRAKE:
                return new Airbrake($config);
                break;
            case self::NOTIFIER_SYSTEM_EMAIL:
                return new SystemEmail($config);
                break;
        }

        throw new NotifierNotFoundException(sprintf('Notifier %s was not found.', $type));
    }



}
