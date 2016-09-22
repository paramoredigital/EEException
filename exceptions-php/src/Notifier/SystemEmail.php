<?php

namespace EEException\Notifier;

require_once(realpath(__DIR__).'/NotifierInterface.php');

class SystemEmail implements NotifierInterface
{
    protected $_email_config = array(
        'recipients' => array('jesse@getbunch.com'),
        'subject' => 'Testing EEException SystemEmail',
        'message' => ''
    );

    /**
     * @param array $notifier_config
     */
    public function __construct(array $notifier_config)
    {
        $this->_email_config = array_merge($this->_email_config, $notifier_config);
    }

    public function SendErrorString($code, $message)
    {
        $config = $this->_get_config();

        foreach($config['recipients'] as $recipient)
            mail($recipient, $config['subject'], $message);

        return true;
    }

    protected function _get_config()
    {
        return $this->_email_config;
    }
}
