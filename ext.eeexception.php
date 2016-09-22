<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package        ExpressionEngine
 * @author        ExpressionEngine Dev Team
 * @copyright    Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license        http://expressionengine.com/user_guide/license.html
 * @link        http://expressionengine.com
 * @since        Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * @package ExpressionEngine
 * @subpackage Addons
 * @category Extension
 * @author Jesse Bunch
 * @link http://paramore.is/
 */

require_once(realpath(__DIR__) . '/exceptions-php/src/UseCases/SendErrorString.php');
require_once(realpath(__DIR__) . '/exceptions-php/src/UseCases/RegisterHandler.php');
require_once(realpath(__DIR__) . '/exceptions-php/src/Notifier/NotifierInterface.php');
require_once(realpath(__DIR__) . '/exceptions-php/src/Notifier/NotifierFactory.php');
require_once(realpath(__DIR__) . '/exceptions-php/src/Exceptions/NotifierNotFoundException.php');

use \EEException\Notifier\NotifierFactory as NotifierFactory;
use \EEException\Notifier\NotifierNotFoundException as NotifierNotFoundException;
use EEException\UseCases\SendErrorString as SendErrorStringUseCase;
use EEException\UseCases\RegisterHandler as RegisterHandler;

class Eeexception_ext
{

    public $settings = array();
    public $description = 'This extension provides a hook that allows exceptions to be reported to Codebase, Airbrake, or any other destination.';
    public $docs_url = '';
    public $name = 'EEException';
    public $settings_exist = 'n';
    public $version = '1.1';

    private $EE;

    /**
     * @param string $settings
     */
    public function __construct($settings = '')
    {
        $this->EE =& get_instance();
        $this->settings = $settings;
    }

    /**
     * @return void
     */
    public function activate_extension()
    {
        $this->settings = array();

        $data = array(
            'class' => __CLASS__,
            'method' => 'eeexception_send_string',
            'hook' => 'eeexception_send_string',
            'version' => $this->version,
            'enabled' => 'y'
        );

        $this->EE->db->insert('extensions', $data);
    }

    public function eeexception_register_handler()
    {
        $eeexception_config = $this->_get_eeexception_config();
        $selected_notifier = $this->_get_default_notifier($eeexception_config);
        $notifier_config = $this->_get_notifier_config($selected_notifier, $eeexception_config);

        if ($this->_eeexception_config_isnt_set($eeexception_config))
        {
            $this->_logConfigInvalidError();
            return;
        }


        $usecase = new RegisterHandler($notifier_config);
        $usecase->execute();
    }

    /**
     * @param $error_code
     * @param $error_message
     * @param array $notifier_config_overrides
     */
    public function eeexception_send_string($error_code, $error_message, $notifier_config_overrides = array())
    {
        $eeexception_config = $this->_get_eeexception_config();
        $selected_notifier = $this->_get_default_notifier($eeexception_config);
        $notifier_config = $this->_get_notifier_config($selected_notifier, $eeexception_config);
        $notifier_config = $this->_override_default_notifier_config($notifier_config, $notifier_config_overrides);

        if ($this->_eeexception_config_isnt_set($eeexception_config)) {
            $this->_logConfigInvalidError();
            return;
        }

        try {
            $notifier = NotifierFactory::getNotifier($selected_notifier, $notifier_config);
        } catch (NotifierNotFoundException $e) {
            $this->_logInvalidNotifierError();
            return;
        }

        $usecase = new SendErrorStringUseCase($notifier);

        try {
            $usecase->execute($error_code, $error_message);
        } catch (InvalidArgumentException $e) {
            $this->_logInvalidMessageStringError($e);
        }
    }

    /**
     * @param array $eeexception_config
     * @return bool
     */
    protected function _eeexception_config_isnt_set(array $eeexception_config)
    {
        return !isset($eeexception_config);
    }

    /**
     * @param array $notifier_config
     * @param array $notifier_config_overrides
     * @return array
     */
    public function _override_default_notifier_config(array $notifier_config, array $notifier_config_overrides)
    {
        unset($notifier_config_overrides['apiKey'], $notifier_config_overrides['host']);
        $notifier_config = array_merge($notifier_config, $notifier_config_overrides);

        return $notifier_config;
    }

    /**
     * @param string $default_notifier
     * @param array $eeexception_config
     * @return array
     */
    protected function _get_notifier_config($default_notifier, array $eeexception_config)
    {
        return $eeexception_config['notifier_config'][$default_notifier];
    }

    /**
     * @param array $eeexception_config
     * @return array
     */
    protected function _get_default_notifier(array $eeexception_config)
    {
        return $eeexception_config['default_notifier'];
    }

    /**
     * @return array
     */
    protected function _get_eeexception_config()
    {
        return $this->EE->config->item('eeexception_config', null);
    }

    protected function _logAPIKeyMissingError()
    {
        $this->EE->load->library('logger');
        $this->EE->logger->developer('You forgot to set the "codebase_exceptions_api_key" item in your config file! This is needed for EEException to work.', TRUE, 86400);
    }

    private function _logInvalidNotifierError()
    {
        $this->EE->load->library('logger');
        $this->EE->logger->developer('The notifier you specified in EEException\'s configuration is not valid.', TRUE, 86400);
    }

    protected function _logInvalidMessageStringError($e)
    {
        $this->EE->load->library('logger');
        $this->EE->logger->developer('EEException '. $e->getMessage(), TRUE, 86400);
    }

    protected function _logConfigInvalidError()
    {
        $this->EE->load->library('logger');
        $this->EE->logger->developer('EEException has not been configured properly. Please see the documentation.', TRUE, 86400);
    }

    function disable_extension()
    {
        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->delete('extensions');
    }

    function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version)
        {
            return false;
        }

        if ($current < '1.2')
        {
            $data = array(
                'class' => __CLASS__,
                'method' => 'eeexception_register_handler',
                'hook' => 'eeexception_register_handler',
                'version' => $this->version,
                'enabled' => 'y'
            );

            $this->EE->db->insert('extensions', $data);
        }

        return TRUE;
    }
}

/* End of file ext.eeexception.php */
/* Location: /system/expressionengine/third_party/eeexception/ext.eeexception.php */