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

class Tracker extends MX_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('emailer_model');		
	}
	
	function trackviews($trackingID = '')
	{
		if ($trackingID)
		{
			$this->emailer_model->addview($trackingID);	 
			
			$img = imagecreate( 10, 10 );
			$background = imagecolorallocate($img, 255, 255, 255);
			$transparent = imagecolortransparent( $img, $background );
	
			header("X-Powered-By:");
			header("Content-type: image/gif" );
			
			imagegif( $img );
		}
	}

	function tracklink($key = '')
	{
		if ($key)
		{
			if ($path = $this->emailer_model->tracklink($key))
			{
				header("location: ".$path);
			}
			else
			{
				show_404();
			}
		}
	}

	function viewemail($emailID)
	{
		$html = $this->emailer_model->parse_email($emailID);
		$html = preg_replace('/{UNSUBSCRIBE}|{FULLNAME}|{BASEURL}|{VIEWONLINE}/', '', $html);
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<html><body>'.$html.'</body></html>
		';
	}
	
}