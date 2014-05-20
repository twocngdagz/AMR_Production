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

class Emailer extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		
		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
	}
	
	function subscriptions($email = '')
	{
		// load models and libs
		$this->load->model('emailer_model', 'emailer');
		$this->load->library('email');
		$this->load->module('pages');
		
		// decode email
		if (!$email = $this->core->decode($email))
		{
			// get from userdata
			if ($email = $this->session->userdata('email'))
			{
				redirect('/emailer/subscriptions/'.$this->core->encode($email));
			}
			else
			{
				show_error('No valid email address found.');
			}
		}
		
		// get user
		$user = $this->emailer->get_user_by_email($email);

		// set stuff
		$subscribedLists = array();

		// get name
		if ($user)
		{
			$name = $user['name'];
		}
		elseif ($this->session->userdata('firstName'))
		{
			$name = trim($this->session->userdata('firstName').' '.$this->session->userdata('lastName'));
		}
		else
		{
			$name = '';
		}
		
		if (count($_POST))
		{
			// reset lists
			$this->emailer->reset_list_subscriber($email);
			
			if ($this->input->post('lists'))
			{
				foreach($this->input->post('lists') as $listID)
				{	
					$this->emailer->add_list_subscriber($listID, $email, $name);
				}
			}
			
			// set message
			$this->session->set_flashdata('message', 'Your mailing list preferences have been updated.');
			
			// redirect
			redirect('emailer/subscriptions/'.$this->core->encode($email));
		}
			
		if ($lists = $this->emailer->get_lists_by_email($email))
		{
			foreach($lists as $list)
			{
				$subscribedLists[] = $list['listID'];
			}
		}
		
		if ($lists = $this->emailer->get_lists())
		{
			foreach($lists as $list)
			{
				$output['emailer:lists'][] = array(
					'list:id' => $list['listID'],
					'list:title' => $list['listName'],
					'list:subscribed' => (in_array($list['listID'], $subscribedLists)) ? 'checked="checked"' : ''
				);
			}
		}
		
		// output key
		$output['emailer:key'] = $this->core->encode($email);
		
		// load message
		$output['message'] = ($this->session->flashdata('message')) ? '<p>'.$this->session->flashdata('message').'</p>' : FALSE;
		
		// load errors
		$output['errors'] = (validation_errors()) ? validation_errors() : FALSE;

		// display with cms layer
		$this->pages->view('subscriptions', $output, 'emailer');		
	}
	
	function unsubscribe_newsletter()
	{
		$emailposted = $this->input->get('er');
		$listIDposted = $this->input->get('lst');
		
		$emailposted = $this->core->decode($emailposted);
		$listIDposted = $this->core->decode($listIDposted);
		
		//echo $emailposted;
		//echo $listIDposted;
		
		
		if ($emailposted != '') {
			
            $this->db->where('email', $emailposted);
            $this->db->where('listID', $listIDposted);
            $query = $this->db->get('email_list_subscribers');

            if ($query->num_rows() > 0) {
                
				$this->db->where('listID', $listIDposted);
				$this->db->where('email', $emailposted);
				$this->db->delete('email_list_subscribers');
				
				redirect('/unsubscribed');


			}else {
				redirect('/unsubscribe_error');
            }
			
		}
			
	}
	
		
	
	function unsubscribe($email)
	{
		// load models and libs
		$this->load->model('emailer_model', 'emailer');
		
		// decode email
		$email = $this->core->decode($email);
		
		if ($email)
		{
			$this->emailer->reset_list_subscriber($email);	
			
			// set message
			$this->session->set_flashdata('message', 'You have been unsubscribed from all mailing lists.');			
		}
				
		// redirect
		redirect('emailer/subscriptions/'.$this->core->encode($email));
	}
	
	
	function subscribe()
	{
		// subscribe email
		if ($this->input->post('listID') && $this->input->post('email'))
		{
			// load models and libs
			$this->load->model('emailer_model');
			
			if ($this->input->post('fullName'))
			{
				$name = trim($this->input->post('fullName'));
			}
			elseif ($this->input->post('firstName') && $this->input->post('lastName'))
			{
				$name = trim($this->input->post('firstName').' '.$this->input->post('lastName'));
			}
			elseif ($this->input->post('firstName'))
			{
				$name = trim($this->input->post('firstName'));
			}
			else
			{
				$name = '';
			}
			
			if (is_array($this->input->post('listID')))
			{
				foreach ($this->input->post('listID') as $listID)
				{
					if ($listID)
					{
						$this->emailer_model->add_list_subscriber($listID, $this->input->post('email'), $name);
					}
				}
			}
			else
			{
				$this->emailer_model->add_list_subscriber($this->input->post('listID'), $this->input->post('email'), $name);
			}
		}

		// redirect
		if ($this->input->post('redirect'))
		{
			redirect($this->input->post('redirect'));
		}
	}
	
}