<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Email Logger
 *
 * This control allows you to bypass sending email and log it's content 
 * instead. Most appropriate for development environment or other rapid
 * testing scenarios. The actual mail will never get sent only logged.  
 *
 * By defailt it will not do anything. You either need to manually pass
 * in 'log' as the protocol or the perferred way would be to add the 
 * following record to your email config file (config/email.php):
 *  
 *    $config['force_log_protocol'] = true;
 *
 * TIP: Add an environment-specific config file to automatically control.
 * https://www.codeigniter.com/userguide3/libraries/config.html#environments
 *
 * @package		CodeIgniter
 * @subpackage	CodeIgniter Email Logger
 * @category	Libraries
 * @author		Louis Walch
 * @link		http://www.hellolouis.com
 */

class MY_Email extends CI_Email {

    /**
     * Force all mail sent from CI to be logged. Off by default.
     *
     * @var bool
     */
    protected $_force_logging    = FALSE;


    /**
     * Plain-text subject
     *
     * @var string
     */
    protected $_raw_subject     = '';


	// --------------------------------------------------------------------

 
    public function __construct(array $config = array()) {

        // Global Code Igniter instance.
        $CI = &get_instance();

        // Make sure email configuration file is loaded.
        $CI->load->config('email');

        // Allow config file to determine what this class should do.
        if (config_item('force_email_logging') && is_bool(config_item('force_email_logging'))) {
            $this->_force_logging = config_item('force_email_logging');
        }

        parent::__construct($config);

    }


    // --------------------------------------------------------------------

    /**
     * Set Email Subject
     *
     * @param   string
     * @return  CI_Email
     */
    public function subject($subject) {

        $this->_raw_subject = $subject;

        return parent::subject($subject);

    }

    // --------------------------------------------------------------------

    /**
     * Intercept the spool request to check if we should switch to log protocol.
     * We need to do this everytime in case the protocol is being passed in from Controller.
     *
     * @return  bool
     */
    protected function _spool_email() {

        if ($this->_force_logging) {

            $this->_send_to_log();

        } else {

            // Continue as if nothing ever happened.
            return parent::_spool_email();

        }

    }

    // --------------------------------------------------------------------

    /**
     * Send to log
     *
     * @return  bool
     */
    protected function _send_to_log() {

        log_message('debug', '====== SENGING MAIL ===================================================');

        log_message('debug', 'To: ' . implode(', ', $this->_recipients));
        log_message('debug', 'Subject: ' . $this->_raw_subject);
        log_message('debug', $this->_body);

        log_message('debug', '=======================================================================');

        return TRUE;

    }

}
