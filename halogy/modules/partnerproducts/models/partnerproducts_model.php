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

class Partnerproducts_Model extends CI_Model 
{
  function __construct()
  {
    parent::__construct();

    // get siteID, if available
    if(defined('SITEID'))
    {
      $this->siteID = SITEID;
    }	
  }
  
  function prod_info($id)
  {
    $this->db->where('id', $id);
    
    $query = $this->db->get('partner_products');
    
    return $query->row();
  }
  
}