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

class Admin extends MX_Controller {

	// set defaults
	var $includes_path = '/includes/admin';		// path to includes for header and footer
	var $redirect = '/admin/emailer/campaigns'; // default redirect
	var $permissions = array();
	
	function __construct()
	{
		parent::__construct();

		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_admin'))
		{
			redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
		}
		
		// get permissions and redirect if they don't have access to this module
		if (!$this->permission->permissions)
		{
			redirect('/admin/dashboard/permissions');
		}
		if (!in_array($this->uri->segment(2), $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}

		// load models and libs
		$this->load->model('emailer_model', 'emailer');
		$this->load->library('email');
		
		// configure email class
		$config['mailtype'] = 'html';
		$config['validate'] = TRUE;
		$this->email->initialize($config);
	}
	
	function index()
	{
		redirect($this->redirect);
	}
	
	function campaigns()
	{
		// grab data and display
		$output = $this->core->viewall('email_campaigns');

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/campaigns',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_campaign()
	{
		// check permissions for this page
		if (!in_array('emailer_campaigns_edit', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// required
		$this->core->required = array(
			'campaignName' => array('label' => 'Campaign name', 'rules' => 'required'),
		);

		// get values (always before post handling)
		$output['data'] = $this->core->get_values('email_campaigns');	

		// set date
		$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
		
		// update table
		if ($this->core->update('email_campaigns') && count($_POST))
		{
			// where to redirect to
			redirect($this->redirect);
		}

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/add_campaign', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function edit_campaign($campaignID)
	{
		// check permissions for this page
		if (!in_array('emailer_campaigns_edit', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// required
		$this->core->required = array(
			'campaignName' => array('label' => 'Campaign name', 'rules' => 'required'),
		);

		// where
		$objectID = array('campaignID' => $campaignID);	

		// get values (always before post handling)
		$output['data'] = $this->core->get_values('email_campaigns', $objectID);	
		
		// update table
		if ($this->core->update('email_campaigns', $objectID) && count($_POST))
		{
			// where to redirect to
			redirect($this->redirect);
		}

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/edit_campaign',$output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}


	function delete_campaign($campaignID)
	{
		// check permissions for this page
		if (!in_array('emailer_campaigns_delete', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
			
		if ($this->core->delete('email_campaigns', array('campaignID' => $campaignID)))
		{
			// where to redirect to
			redirect($this->redirect);
		}
	}

	function emails($campaignID = '')
	{
		$output = $this->emailer->get_emails($campaignID);

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/emails',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_email($campaignID = '')
	{
		$emailID = $this->emailer->add_temp_email();
		redirect('/admin/emailer/edit_email/'.$emailID.'/'.$campaignID);
	}

	function edit_email($emailID)
	{
		// check permissions for this page
		if (!in_array('emailer_edit', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// required
		$this->core->required = array(
			'emailName' => array('label' => 'Email name', 'rules' => 'required'),
		);

		// where
		$objectID = array('emailID' => $emailID);

		// get values (always before post handling)
		$output['data'] = $this->core->get_values('emails', $objectID);	
					
		// update table
		if ($this->input->post('cancel'))
		{
			redirect($this->redirect);
		}
		else
		{
			// undelete
			$this->core->set = array('deleted' => 0);

			// set date
			$this->core->set['dateModified'] = date("Y-m-d H:i:s");
			
			// set deploy date
			$this->core->set['deployDate'] = ($this->input->post('deployDate')) ? date("Y-m-d H:i:s", strtotime($this->input->post('deployDate').' 6AM')) : date("Y-m-d H:i:s", strtotime('now'));

			// update
			if ($this->core->update('emails', $objectID) && count($_POST))
			{				
				// send test email
				if ($this->input->post('testEmails'))
				{
					if ($this->emailer->sendtest($objectID['emailID'], $this->session->userdata('userID')))
					{
						// set success message
						$this->session->set_flashdata('message', 'The test email was sent successfully.');
					}
					else
					{
						// set fail message
						$this->session->set_flashdata('message', 'The test email failed.');
					}
						
					// where to redirect to
					redirect('/admin/emailer/edit_email/'.$emailID);
				}
				
				// goes to the deploy page and updates on progress
				elseif ($this->input->post('deployButton'))
				{
					if ($this->emailer->deploymail($emailID))
					{
						$this->emailer->setdeploy($objectID);
											
						redirect('/admin/emailer/viewsend/'.$objectID['emailID']);
					}
					else
					{
						$this->form_validation->set_error('There are no emails in the mailing list to send to!');
					}
				}
				
				// save email and redirect
				else
				{
					// set success message
					$this->session->set_flashdata('message', 'Your changes were saved.');
					
					// where to redirect to
					redirect('/admin/emailer/edit_email/'.$emailID);
				}
			}
			
			// set message
			if ($message = $this->session->flashdata('message'))
			{
				$output['message'] = '<p>'.$message.'</p>';
			}

			// get campaigns
			$output['campaigns'] = $this->emailer->get_campaigns();
			$output['emailID'] = isset($emailID) ? $emailID : 0;
			
			// populate default deploy date
			$output['data']['deployDate'] = ($output['data']['deployDate'] == 0) ? date("Y-m-d H:i:s") : $output['data']['deployDate'];
	
			// get templates
			$output['templates'] = $this->emailer->get_templates();
			
			// get lists
			$output['lists'] = $this->emailer->get_lists();
			
			// templates
			$this->load->view($this->includes_path.'/header');
			$this->load->view('admin/edit_email',$output);
			$this->load->view($this->includes_path.'/footer');
		}
	}

	function delete_email($emailID, $parentID = '')
	{
		// check permissions for this page
		if (!in_array('emailer_delete', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		if ($this->core->delete('emails', array('emailID' => $emailID)))
		{			
			// where to redirect to
			redirect('admin/emailer/emails/'.$parentID);
		}
	}
	
	function lists()
	{
		// grab data and display
		$output['email_lists'] = $this->emailer->get_lists();

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/lists',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_list()
	{
		// check permissions for this page
		if (!in_array('emailer_lists', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// required
		$this->core->required = array(
			'listName' => array('label' => 'List name', 'rules' => 'required'),
		);
		
		// get values (always before post handling)
		$output['data'] = $this->core->get_values('email_lists');
		
		if (count($_POST))
		{			
			// set date
			$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
			
			// update table
			if ($this->core->update('email_lists'))
			{
				$listID = $this->db->insert_id();
				
				if ($subscribers = $this->input->post('subscribers'))
				{
					$subscribersArray = array();
					
					foreach (explode("\n", $subscribers) as $subscriber)
					{
						$email = '';
						$name = '';
						
						foreach (preg_split('/,/', $subscriber) as $part)
						{
							$part = trim(str_replace('"', '', $part));
							
							if ($part)
							{
								if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $part))
								{
									$email = strtolower($part);
								}
								else
								{
									$name = $part;
								}
							}
						}
						
						if ($email)
						{
							$subscribersArray[$email] = $name;
						}
					}
					
					$this->emailer->add_list_subscribers($listID, $subscribersArray);
				}
				
				// where to redirect to
				redirect('admin/emailer/lists');
			}
		}
	
		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/add_list', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function edit_list($listID)
	{
		// check permissions for this page
		if (!in_array('emailer_lists', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// required
		$this->core->required = array(
			'listName' => array('label' => 'List name', 'rules' => 'required'),
		);

		// where
		$objectID = array('listID' => $listID);	

		// get values (always before post handling)
		$output['data'] = $this->core->get_values('email_lists', $objectID);
		
		if (count($_POST))
		{			
			// set date
			$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
			
			// update table
			if ($this->core->update('email_lists', $objectID))
			{
				// reset list
				$this->emailer->reset_list_subscribers($listID);
				
				if ($subscribers = $this->input->post('subscribers'))
				{
					$subscribersArray = array();
					
					foreach (explode("\n", $subscribers) as $subscriber)
					{
						$email = '';
						$name = '';
						
						foreach (preg_split('/,/', $subscriber) as $part)
						{
							$part = trim(str_replace('"', '', $part));
							
							if ($part)
							{
								if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $part))
								{
									$email = strtolower($part);
								}
								else
								{
									$name = $part;
								}
							}
						}
						
						if ($email && !array_key_exists($email, $subscribersArray))
						{
							$subscribersArray[$email] = $name;
						}
					}
					
					$this->emailer->add_list_subscribers($listID, $subscribersArray);
				}
				
				// where to redirect to
				redirect('admin/emailer/lists');
			}
		}
		
		// get list subscribers
		if ($subscribers = $this->emailer->get_list_subscribers($listID))
		{
			foreach ($subscribers as $email => $name)
			{
				$subscribersArray[] = ($name) ? $email.', '.$name : $email;
			}
			
			$output['data']['subscribers'] = implode("\n", $subscribersArray);
		}

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/edit_list',$output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}


	function delete_list($listID)
	{
		// check permissions for this page
		if (!in_array('emailer_lists', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
			
		if ($this->core->delete('email_lists', array('listID' => $listID)))
		{
			// where to redirect to
			redirect('admin/emailer/lists');
		}
	}

	function templates()
	{
		// check permissions for this page
		if (!in_array('emailer_templates', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// grab data and display
		$output = $this->core->viewall('email_templates', array('siteID' => $this->siteID, 'deleted' => 0));

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/templates',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_template()
	{
		// check permissions for this page
		if (!in_array('emailer_templates', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// required fields
		$this->core->required = array('templateName' => 'Template name');			

		// get values
		$output['data'] = $this->core->get_values('email_templates');

		// set date
		$this->core->set['dateCreated'] = date("Y-m-d H:i:s");

		// update
		if ($this->core->update('email_templates') && count($_POST))
		{
			// whe$this->core->required = to redirect to
			redirect('/admin/emailer/templates');
		}

		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/add_template', $output);
		$this->load->view($this->includes_path.'/footer');
	}

	function edit_template($templateID)
	{
		// check permissions for this page
		if (!in_array('emailer_templates', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// required fields
		$this->core->required = array('templateName' => 'Template name');			

		// where
		$objectID = array('templateID' => $templateID);		

		// set date
		$this->core->set['dateModified'] = date("Y-m-d H:i:s");
		
		// get values
		$output['data'] = $this->core->get_values('email_templates', $objectID);

		// update
		if ($this->core->update('email_templates', $objectID) && count($_POST))
		{
			// where to redirect to
			redirect('/admin/emailer/templates');
		}

		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/edit_template',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function delete_template($templateID)
	{
		// check permissions for this page
		if (!in_array('emailer_templates', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// where
		$objectID = array('templateID' => $templateID);		
	
		if ($this->core->soft_delete('email_templates', $objectID))
		{
			// where to redirect to
			redirect('/admin/emailer/templates');
		}
	}

	function view_template($templateID, $emailID = '')
	{	
		$this->load->library('parser');
		
		$pagedata = array('templateID' => $templateID, 'emailID' => $emailID);

		if ($emailID)
		{
			$output = $this->emailer->generate_page($pagedata, '', true);
		}
		else
		{
			$template = $this->emailer->get_templates($templateID);
			$output = $this->emailer->generate_template($template);
		}

		$this->parser->parse('view_template',$output);
	}

	function add_block($emailID,$block)
	{
		// check the block has content and is not null
		if ($_POST['body'] != 'undefined' && strlen($_POST['body']) > 0)
		{
			$objectID = array('emailID' => $emailID);
	
			$body = str_replace('[!!ADDBLOCK!!]', '', $_POST['body']);
			$body = htmlentities($body, NULL, 'UTF-8');
			$body = html_entity_decode($body, NULL, 'UTF-8');
			
			$this->emailer->add_block($body,$objectID,$block);
	
			$this->output->set_output(@$this->template->parse_body($body));
		}
	}

	function viewsend($emailID = '')
	{	
		if ($emailID)
		{
			$output['emailID'] = $emailID;
		}
		else
		{
			$email = $this->emailer->get_latest_deployed_email();
			$output['emailID'] = $email['emailID'];
		}

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/viewsend', $output);
		$this->load->view($this->includes_path.'/footer');
	}

	function viewsend_ajax($emailID = '')
	{
		$this->emailer->batchmail();		
		
		$this->output->set_output($this->emailer->viewsend($emailID));
	}

	function view_email($pageID = '')
	{
		$this->output->set_output($this->emailer->parse_email($pageID));
	}

}