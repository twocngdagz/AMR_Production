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
	var $includes_path = '/includes/admin';				// path to includes for header and footer
	var $redirect      = '/admin/partner-products/viewall';				// default redirect
	var $permissions   = array();

	function __construct()
	{
      parent::__construct();

      // check user is logged in, if not send them away from this controller
      if(!$this->session->userdata('session_admin'))
      {
        redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
      }
		
      // get permissions and redirect if they don't have access to this module
      if(!$this->permission->permissions)
      {
        redirect('/admin/dashboard/permissions');
      }
		

      // get siteID, if available
      if(defined('SITEID'))
      {
        $this->siteID = SITEID;
      }

      //  load models and libs
      $this->load->model('partnerproducts_model', 'partnerproducts');
      $this->load->library('tags');
	}
	
	function index()
	{
      redirect($this->redirect);
	}
	
	function viewall()
	{
      // default where
      
      $where = array();

      $limit = ($this->site->config['paging']) ?  $this->site->config['paging']: 999;
      	
      // grab data and display
      $output = $this->core->viewall('partner_products', $where, array(), $limit);

      $this->load->view($this->includes_path.'/header');
      $this->load->view('admin/viewall', $output);
      $this->load->view($this->includes_path.'/footer');
	}
    
    function add()
    {
      $output = array();
      $this->core->required = array(
        'title' => array('label' => 'Title', 'rules' => 'required'),
        'url'   => array('label' => 'URL',   'rules' => '')
      );
      
      if(count($_POST))
      {
        if(@$_FILES['image']['name'] != '')
		    {
          if($imageData = $this->uploads->upload_image(FALSE, '', 'image'))
          {	
            $this->core->set['image'] = $imageData['file_name'];
          }  
        }

        if(@$_FILES['alt_image']['name'] != '')
        {
          if($imageData = $this->uploads->upload_image(FALSE, '', 'alt_image'))
          { 
            $this->core->set['alt_image'] = $imageData['file_name'];
          }  
        }
        
        if($this->uploads->errors)
		    {
          $this->form_validation->set_error($this->uploads->errors);
		    }
        else
        {
          if($this->core->update('partner_products'))
          {
            $this->session->set_flashdata('success-message', 'Successfully added the product');
            redirect($this->redirect);
          }
        }
        
      }
      
      $this->load->view($this->includes_path.'/header');
      $this->load->view('admin/add', $output);
      $this->load->view($this->includes_path.'/footer');
    }
    
    function edit($id)
    {
      
      $prod_info = $this->partnerproducts->prod_info($id);
      
      $this->core->required = array(
        'title' => array('label' => 'Title', 'rules' => 'required'),
        'url'   => array('label' => 'URL',   'rules' => '')
      );
      
      
      
      if(count($_POST))
      {
        if(@$_FILES['image']['name'] != '')
		    {
          if($imageData = $this->uploads->upload_image(FALSE, '', 'image'))
          {	
            $this->core->set['image'] = $imageData['file_name'];
          }  
        }

        if(@$_FILES['alt_image']['name'] != '')
        {
          if($imageData = $this->uploads->upload_image(FALSE, '', 'alt_image'))
          { 
            $this->core->set['alt_image'] = $imageData['file_name'];
          }  
        }
        
        if($this->uploads->errors)
		    {
          $this->form_validation->set_error($this->uploads->errors);
		    }
        else
        {
          if($this->core->update('partner_products', array('id'=>$id)))
          {
            $this->session->set_flashdata('success-message', 'Successfully added the product');
            redirect($this->redirect);
          }
        }
        
      }
      
      $output = array(
          'prod_info' => $prod_info
      );
      
      $this->load->view($this->includes_path.'/header');
      $this->load->view('admin/edit', $output);
      $this->load->view($this->includes_path.'/footer');
    }


}