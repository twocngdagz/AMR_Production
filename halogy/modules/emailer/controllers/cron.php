<?php
/**
 * Halogy
 *
 * A user friendly, modular content management system for PHP 5.0
 * Built on CodeIgniter - http://codeigniter.com
 *
 * @package		Halogy
 * @author		Haloweb Ltd.
 * @copyright	Copyright (c) 2008-2011, Haloweb Ltd.
 * @license		http://halogy.com/license
 * @link		http://halogy.com/
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

class Cron extends MX_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('emailer_model', 'emailer');
		$this->load->library('email');
		$this->load->module('pages');
		
		// configure email class
		$config['mailtype'] = 'html';
		$config['validate'] = TRUE;
		$this->email->initialize($config);
	}
	
	function batchmail()
	{
		$this->output->set_output($this->emailer->batchmail());
	}

}