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
class Eeexception_ext
{

    public $settings = array();
    public $description = 'This extension provides a hook that can be used from other addons to send Exceptions to Codebase.';
    public $docs_url = '';
    public $name = 'EEException';
    public $settings_exist = 'n';
    public $version = '1.0';

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

    /**
     * @param $error_message
     * @return void;
     */
    public function eeexception_send_string($error_message)
    {
        $api_key = $this->EE->config->item('codebase_exceptions_api_key', null);

        if (strlen($error_message) < 1) {
            $this->_logInvalidMessageStringError();
            return;
        }

        if (!$api_key) {
            $this->_logAPIKeyMissingError();
            return;
        }

        $notifier = new \EEException\Notifier\Codebase($api_key);
        $interactor = new \EEException\UseCases\SendErrorString($notifier);

        try {
            $interactor->execute($error_message);
        } catch (InvalidArgumentException $e) {
            $this->_logInvalidMessageStringError();
        }
    }

    protected function _logAPIKeyMissingError()
    {
        $this->EE->load->library('logger');
        $this->EE->logger->developer('You forgot to set the "codebase_exceptions_api_key" item in your config file! This is needed for EEException to work.', TRUE, 86400);
    }

    protected function _logInvalidMessageStringError()
    {
        $this->EE->load->library('logger');
        $this->EE->logger->developer('EEException called with an invalid message string.', TRUE, 86400);
    }

    /**
     * @return void
     */
    function disable_extension()
    {
        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->delete('extensions');
    }

    /**
     * @param string $current
     * @return bool
     */
    function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version)
            return FALSE;

        return TRUE;
    }
}

/* End of file ext.eeexception.php */
/* Location: /system/expressionengine/third_party/eeexception/ext.eeexception.php */