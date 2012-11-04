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
 * EEException Plugin
 *
 * @package        ExpressionEngine
 * @subpackage    Addons
 * @category    Plugin
 * @author        Jesse Bunch
 * @link        http://paramore.is/
 */

$plugin_info = array(
    'pi_name' => 'EEException',
    'pi_version' => '1.0',
    'pi_author' => 'Jesse Bunch',
    'pi_author_url' => 'http://paramore.is/',
    'pi_description' => 'This plugin allows you to send exceptions to Codebase from your ExpressionEngine templates.',
    'pi_usage' => Eeexception::usage()
);

class Eeexception
{
    public $return_data;

    public function __construct()
    {
        $this->EE =& get_instance();
    }

    public function notify()
    {
        $error_message = $this->EE->TMPL->fetch_param('error_message', '');
        $component = $this->EE->TMPL->fetch_param('component', 'EEException Plugin');
        $action = $this->EE->TMPL->fetch_param('action', '');

        if (TRUE === $this->EE->extensions->active_hook('eeexception_send_string'))
            $this->EE->extensions->call(
                'eeexception_send_string',
                $error_message,
                array(
                    'component' => $component,
                    'action' => $action
                )
            );
        else
            $this->_logExtensionNotInstalledError();


        return '';
    }

    protected function _logExtensionNotInstalledError()
    {
        $this->EE->load->library('logger');
        $this->EE->logger->developer('You must install the EEException extension prior to using the EEException plugin!', TRUE, 86400);
    }

    public static function usage()
    {
        ob_start();
        ?>

    Since you did not provide instructions on the form, make sure to put plugin documentation here.
    <?php
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
}
/* End of file pi.eeexception.php */
/* Location: /system/expressionengine/third_party/eeexception/pi.eeexception.php */