<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Halogy
 *
 * A user friendly, modular content management system for PHP 5.0
 * Built on CodeIgniter - http://codeigniter.com
 *
 * @package		Halogy
 * @author		Haloweb Ltd
 * @copyright	Copyright (c) 2012, Haloweb Ltd
 * @license		http://halogy.com/license
 * @link		http://halogy.com/
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

class Template {

	// set defaults
	var $CI;								// CI instance
	var $base_path = '';					// default base path
	var $moduleTemplates = array();
	var $template = array();
	
	function Template()
	{
		$this->CI =& get_instance();
		
		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}

		$this->uploadsPath = $this->CI->config->item('uploadsPath');

		// populate module templates array
		$this->moduleTemplates = array(
			'blog',		
			'blog_single',
			'blog_search',
			'community_account',
			'community_create_account',
			'community_forgotten',
			'community_home',
			'community_login',
			'community_members',
			'community_messages',
			'community_messages_form',
			'community_messages_popup',
			'community_messages_read',
			'community_reset',
			'community_view_profile',
			'community_view_profile_private',
			'events',		
			'events_single',
			'events_featured',
			'events_search',
			'forums',
			'forums_delete',
			'forums_edit_post',
			'forums_edit_topic',						
			'forums_forum',
			'forums_post_reply',
			'forums_post_topic',
			'forums_search',
			'forums_topic',
			'shop_account',
			'shop_browse',
			'shop_cancel',
			'shop_cart',
			'shop_checkout',
			'shop_create_account',
			'shop_donation',
			'shop_featured',
			'shop_forgotten',
			'shop_login',
			'shop_orders',
			'shop_prelogin',
			'shop_product',
			'shop_recommend',
			'shop_recommend_popup',			
			'shop_reset',
			'shop_review',
			'shop_review_popup',
			'shop_success',
			'shop_view_order',
			'wiki',
			'wiki_form',
			'wiki_page',
			'wiki_search'
		);		
	}

	function generate_template($pagedata, $file = false)
	{	
		// page data
		@$this->template['page:title'] = (isset($pagedata['title'])) ? htmlentities($pagedata['title']) : htmlentities($this->CI->site->config['siteName']);
		@$this->template['page:keywords'] = (isset($pagedata['keywords'])) ? $pagedata['keywords'] : '';
		@$this->template['page:description'] = (isset($pagedata['description'])) ? $pagedata['description'] : '';
		@$this->template['page:date'] = (isset($pagedata['dateCreated'])) ? dateFmt($pagedata['dateCreated']) : '';
		@$this->template['page:date-modified'] = (isset($pagedata['dateModified'])) ? dateFmt($pagedata['dateModified']) : '';
		@$this->template['page:uri'] = site_url($this->CI->uri->uri_string());
		@$this->template['page:uri-encoded'] = $this->CI->core->encode($this->CI->uri->uri_string());
		@$this->template['page:uri:segment(1)'] = $this->CI->uri->segment(1);
		@$this->template['page:uri:segment(2)'] = $this->CI->uri->segment(2);
		@$this->template['page:uri:segment(3)'] = $this->CI->uri->segment(3);
		@$this->template['page:template'] = ($this->template['page:template']) ? $this->template['page:template'] : '';
        
        $this->template['current:year']       = date('Y');
        $this->template['list:sale_products'] = $this->list_sale_products();
        
        $this->template['list:partner_products'] = $this->list_partner_products();
        
        $this->CI->load->model('shop/shop_model', 'shop');
        // cart total items
        
        $cart_items       = $this->CI->shop->load_cart();   
        $total_cart_items = count($cart_items['cart']);
     
        $this->template['total:cart_items'] = $total_cart_items;
        $this->template['cart:hover']       = ($total_cart_items > 0 ? 'my-cart-hover' : '');
        $this->template['cart:subtotal']    = number_format($cart_items['subtotal'], 2);
        
        // Random Product
        
        $random_prod = $this->list_random_featured_products(5);
        
        $prod_arr = array();
        foreach($random_prod as $prod)
        {
          $prod_arr[$prod['productID']] = array(
              'image_1' => $prod['image_1'],
              'image_2' => $prod['image_2'],
              'image_3' => $prod['image_3'],
              'image_4' => $prod['image_4'],
              'image_5' => $prod['image_5']
          );
        }
        
       
        
        $counter = 0;
        $landscape_images_arr = array();
        foreach($prod_arr as $key => $value)
        {
          if($value['image_1'] != '' && file_exists(BASEPATH.'../../static/uploads/'.$value['image_1']))
          {
            list($w_1, $h_1, $t_1, $att_1)  = getimagesize(site_url('/static/uploads/'.$value['image_1']));
          }
          
          if($value['image_2'] != '' && file_exists(BASEPATH.'../../static/uploads/'.$value['image_2']))
          {
            list($w_2, $h_2, $t_2, $att_2)  = getimagesize(site_url('/static/uploads/'.$value['image_2']));            
          }
          
          if($value['image_3'] != '' && file_exists(BASEPATH.'../../static/uploads/'.$value['image_3']))
          {
            list($w_3, $h_3, $t_3, $att_3)  = getimagesize(site_url('/static/uploads/'.$value['image_3']));
          }
          
          if($value['image_4'] != '' && file_exists(BASEPATH.'../../static/uploads/'.$value['image_4']))
          {
            list($w_4, $h_4, $t_4, $att_4)  = getimagesize(site_url('/static/uploads/'.$value['image_4']));
          }
          
          if($value['image_5'] != '' && file_exists(BASEPATH.'../../static/uploads/'.$value['image_5']))
          {
            list($w_51, $h_5, $t_5, $att_5) = getimagesize(site_url('/static/uploads/'.$value['image_5']));
          }
          
          
          
          if(!empty($w_1) && !empty($h_1) && !empty($w_2) && !empty($h_2) && !empty($w_3) && !empty($h_3) && !empty($w_4) && !empty($h_4) && !empty($w_5) && !empty($h_5))
		  {
			  if($w_1 > $h_1)
			  {
				$landscape_images_arr[] = $key;
			  }
			  elseif($w_2 > $h_2)
			  {
				$landscape_images_arr[] = $key;
			  }
			  elseif($w_3 > $h_3)
			  {
				$landscape_images_arr[] = $key;
			  }
			  elseif($w_4 > $h_4)
			  {
				$landscape_images_arr[] = $key;
			  }
			  elseif($w_5 > $h_5)
			  {
				$landscape_images_arr[] = $key;
			  }
		  }
		  
		  /* generates error messages
          if($w_1 > $h_1)
          {
            $landscape_images_arr[] = $key;
          }
          elseif($w_2 > $h_2)
          {
            $landscape_images_arr[] = $key;
          }
          elseif($w_3 > $h_3)
          {
            $landscape_images_arr[] = $key;
          }
          elseif($w_4 > $h_4)
          {
            $landscape_images_arr[] = $key;
          }
          elseif($w_5 > $h_5)
          {
            $landscape_images_arr[] = $key;
          }*/
          
        }
        
        shuffle($landscape_images_arr);
        
		if(!empty($landscape_images_arr))
		{
			$product_data = $this->CI->shop->get_product($landscape_images_arr['0']);
			list($w_1, $h_1, $t_1, $att_1)  = getimagesize(site_url('/static/uploads/'.$product_data['image_1']));
			list($w_2, $h_2, $t_2, $att_2)  = getimagesize(site_url('/static/uploads/'.$product_data['image_2']));
			
			if($w_1 > $h_1)
			{
			  $image = site_url('/static/uploads/'.$product_data['image_1']);
			}
			elseif($w_2 > $h_2)
			{
			  $image = site_url('/static/uploads/'.$product_data['image_2']);
			}
			else
			{
			  $image = site_url('/static/images/front/no-image.jpg');
			}
		}
        
		$hf = $this->CI->db->get_where('ha_shop_homefeatured', array('featureID'=>1))->result_array();
		if(!empty($hf))
		{
			$this->template['random_prod_0_title'] = str_replace("?", '', htmlspecialchars($hf[0]['np_title']));
			$this->template['random_prod_0_link'] = $hf[0]['np_url'];
			$this->template['random_prod_0_image_1'] = site_url($hf[0]['np_image']);
			
			$this->template['random_prod_1_title'] = str_replace("?", '', htmlspecialchars($hf[0]['pw_title']));
			$this->template['random_prod_1_link'] = $hf[0]['pw_url'];
			$this->template['random_prod_1_image_1'] = site_url($hf[0]['pw_image']);
		}
        
		/*
        $this->template['random_prod_0_title']   = $random_prod['0']['productName'];
        $this->template['random_prod_0_image_1'] = site_url('resize.php?src='.site_url('/static/uploads/'.$random_prod['0']['image_1']).'&w=460&h=265&s=0');
        $this->template['random_prod_0_image_2'] = site_url('resize.php?src='.site_url('/static/uploads/'.$random_prod['0']['image_2']).'&w=460&h=265&s=0');
        $this->template['random_prod_0_link']    = site_url('/shop/'.$random_prod['0']['productID'].'/'.strtolower(url_title($random_prod['0']['productName'])));
        
        $this->template['random_prod_1_title']   = $random_prod['1']['productName'];
        $this->template['random_prod_1_image_1'] = site_url('resize.php?src='.site_url('/static/uploads/'.$random_prod['1']['image_1']).'&w=225&h=265&s=0');
        $this->template['random_prod_1_image_2'] = site_url('resize.php?src='.site_url('/static/uploads/'.$random_prod['1']['image_2']).'&w=225&h=265&s=0');
        $this->template['random_prod_1_link']    = site_url('/shop/'.$random_prod['1']['productID'].'/'.strtolower(url_title($random_prod['1']['productName'])));
		*/
        
        //$this->template['random_prod_2_title']   = $random_prod['2']['productName'];
        //$this->template['random_prod_2_image_1'] = site_url('resize.php?src='.site_url('/static/uploads/'.$random_prod['2']['image_1']).'&w=225&h=265&s=0');
        //$this->template['random_prod_2_image_2'] = site_url('resize.php?src='.site_url('/static/uploads/'.$random_prod['2']['image_2']).'&w=225&h=265&s=0');
        //$this->template['random_prod_2_link']    = site_url('/shop/'.$random_prod['2']['productID'].'/'.strtolower(url_title($random_prod['2']['productName'])));
        
        
        // Submit Newsletter
        if($this->CI->input->post('submit_newsletter'))
        {
			//die('test');
         
          $this->CI->form_validation->set_rules('newsletter_email', 'Email', 'required|valid_email');
          
          if($this->CI->form_validation->run() != FALSE)
          {
			
			$email = $this->CI->input->post('newsletter_email');
			
			/* MailChimp */
			
			$mailchimp_api = "96de329b64d577bb611da6254dbf8ba7-us5";
			$listid = '718d73e8f4';
			//require_once(BASEPATH.'lib/MailChimp.php');
			$mc = $this->CI->load->library('MailChimp', $mailchimp_api);
			$result = $mc->call('lists/subscribe', array('id'=>$listid, 'email'=>array('email'=>$email), 'send_welcome'=>false));
			if(empty($result['error']))
				$this->template['newsletter:success'] = 'Successfully added you to the mailing list. ';
			else
				$this->CI->form_validation->set_error($result['error']);
			
			/*
            $email = $this->CI->input->post('newsletter_email');
            
            // check if email already exists
            $this->CI->db->where('email', $email);
            $query = $this->CI->db->get('email_list_subscribers');
            $row   = $query->row();
            
            if(count($row) == 0) // email doesnt exists
            { 
              $data = array(
                'email'   => $email,
                'listID'  => '1',
                'siteID'  => '1'
              );
           
              if($this->CI->db->insert('email_list_subscribers', $data))
              {
                $this->template['newsletter:success'] = 'Successfully added you to the mailing list. ';
              }
              
            }
            else
            {
              $this->CI->form_validation->set_error('The email already exist.');
            }
            */         
          }
          
          $this->template['newsletter:error'] = (validation_errors() ? validation_errors() : '');
        }
          
          
        
        // Shop Categories
        $this->template['list:shop_cat'] = $this->list_shop_categories();

        
		// find out if logged in
		$this->template['logged-in'] = ($this->CI->session->userdata('session_user')) ? TRUE : FALSE;
		
		// find out if subscribed
		$this->template['subscribed'] = ($this->CI->session->userdata('subscribed')) ? TRUE : FALSE;		

		// find out if admin
		$this->template['admin'] = ($this->CI->session->userdata('session_admin')) ? TRUE : FALSE;		

		// find out if this is ajax
		$this->template['ajax'] = ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))) ? TRUE : FALSE;

		// find out if browser is iphone
		$this->template['mobile'] = (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'Android')) ? TRUE : FALSE;

		// permissions
		if ($this->CI->session->userdata('session_admin'))
		{
			if ($permissions = $this->CI->permission->get_group_permissions($this->CI->session->userdata('groupID')))
			{
				foreach($permissions as $permission)
				{
					@$this->template['permission:'.$permission] = TRUE;
				}
			}
		}

		// feed (if it exists for the module)
		@$this->template['page:feed'] = (isset($pagedata['feed'])) ? $pagedata['feed'] : '';

		// either build template from a file or from db
		if ($file)
		{
			$templateBody = $this->parse_template($file, FALSE, NULL, FALSE);
		}
		else
		{
			$templateData = $this->CI->core->get_template($pagedata['templateID']);
			$templateBody = $templateData['body'];
		}

		// parse it for everything else
		$this->template['body'] = $this->parse_template($templateBody, FALSE, NULL, FALSE);

		// get navigation and build menu
		if (preg_match_all('/{navigation(\:([a-z0-9\.-]+))?}/i', $this->template['body'], $matches))
		{
			$this->template = $this->parse_navigation('navigation', $this->template);			
		}

		return $this->template;
	}

	function parse_includes($body)
	{
		// get includes
		preg_match_all('/include\:([a-z0-9\.-]+)/i', $body, $includes);

		if ($includes)
		{
			$includeBody = '';
			foreach($includes[1] as $include => $value)
			{
				$includeRow = $this->CI->core->get_include($value);

				$includeBody = $this->parse_body($includeRow['body'], FALSE, NULL, FALSE);

				$includeBody = $this->CI->parser->conditionals($includeBody, $this->template, TRUE);

				$body = str_replace('{include:'.$value.'}', $includeBody, $body);
			}
		}

		return $body;
	}

	function parse_navigation($navTag, $template)
	{
		// get all navigation
		$template[$navTag] = $this->parse_nav();
		
		// get parents
		$template[$navTag.':parents'] = $this->parse_nav(0, FALSE);
		
		// get uri
		$uri = (!$this->CI->uri->segment(1)) ? 'home' : $this->CI->uri->segment(1);
		
		// get children of active nav item
		if ($parent = $this->CI->core->get_page(FALSE, $uri))
		{
			$template[$navTag.':children'] = $this->parse_nav($parent['pageID']);
		}
		else
		{
			$template[$navTag.':children'] = '';
		}

		return $template;
	}
	
	function parse_nav($parentID = 0, $showChildren = TRUE)
	{
		$output = '';
		
		if ($navigation = $this->get_nav_parents($parentID))
		{			
			$i = 1;
			foreach($navigation as $nav)
			{
				// set first and last state on menu
				$class = '';
				$class .= ($i == 1) ? 'first ' : '';
				$class .= (sizeof($navigation) == $i) ? 'last ' : '';
				
				// look for children
				$children = ($showChildren) ? $this->get_nav_children($nav['navID']) : FALSE;
								
				// parse the nav item for the link
				$output .= $this->parse_nav_item($nav['uri'], $nav['navName'], $children, $class);
				
				// parse for children
				if ($children)
				{
					$x = 1;
					$output .= '<ul class="subnav">';
					foreach($children as $child)
					{
						// set first and last state on menu
						$class = '';
						$class .= ($x == 1) ? 'first ' : '';
						$class .= (sizeof($children) == $x) ? 'last ' : '';
								
						// look for sub children
						$subChildren = $this->get_nav_children($child['navID']);
						
						// parse nav item
						$navItem = $this->parse_nav_item($child['uri'], $child['navName'], $subChildren, $class);
						$output .= $navItem;
						
						// parse for children
						if ($subChildren)
						{
							$y = 1;
							$output .= '<ul class="subnav">';
							foreach($subChildren as $subchild)
							{
								// set first and last state on menu
								$class = '';
								$class .= ($y == 1) ? 'first ' : '';
								$class .= (sizeof($subChildren) == $y) ? 'last ' : '';
								
								$navItem = $this->parse_nav_item($subchild['uri'], $subchild['navName'], '', $class).'</li>';
								$output .= $navItem;
								$y++;
							}
							$output .= '</ul>';
						}
						$output .= '</li>';
						$x++;
					}
					$output .= '</ul>';
				}

				$output .= '</li>';
				
				$i++;
			}
		}
		
		return $output;
	}

	function parse_nav_item($uri, $name, $children = FALSE, $class = '')
	{
		// init stuff
		$output = '';
		$childs = array();

		// tidy children array
		if ($children)
		{
			foreach($children as $child)
			{
				$childs[] = $child['uri'];
			}
		}
		
		// set active state on menu
		$currentNav = $uri;
		$output .= '<li class="';
		if (($currentNav != '/' && $currentNav == $this->CI->uri->uri_string()) || 
			$currentNav == $this->CI->uri->segment(1) ||  
			(($currentNav == '' || $currentNav == 'home' || $currentNav == '/') && 
				($this->CI->uri->uri_string() == '' || $this->CI->uri->uri_string() == '/home' || $this->CI->uri->uri_string() == '/')) ||
			@in_array(substr($this->CI->uri->uri_string(),1), $childs)
		)
		{
			$class .= 'active selected ';
		}
		if ($children)
		{
			$class .= 'expanded ';
		}
		
		// filter uri to make sure it's cool
		if (substr($uri,0,1) == '/')
		{
			$href = $uri;
		}
		elseif (stristr($uri,'http://'))
		{
			$href = $uri;
		}
		elseif (stristr($uri,'www.'))
		{
			$href = 'http://'.$uri;
		}
		elseif (stristr($uri,'mailto:'))
		{
			$href = $uri;
		}
		elseif ($uri == 'home')
		{
			$href = '/';
		}			
		else
		{
			$href = '/'.$uri;
		}

        if($name != 'Home')
        {
          // output anchor with span in case of additional styling
    	  $output .= trim($class).'" id="nav-'.trim($uri).'"><a href="'.site_url($href).'" class="'.trim($class).'"><span>'.htmlentities($name).'</span></a>';  
        }
		
		return $output;
	}

	function get_nav($navID = '')
	{
		// default where
		$this->CI->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// where parent is set
		$this->CI->db->where('parentID', 0);

		// get navigation from pages
		$this->CI->db->select('uri, pageID as navID, pageName as navName');
				
		$this->CI->db->order_by('pageOrder', 'asc');
		
		$query = $this->CI->db->get('pages');
		
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}		
	}

	function get_nav_parents($parentID = 0)
	{
		// default where
		$this->CI->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// where parent is set
		$this->CI->db->where('parentID', $parentID); 
		
		// where parent is set
		$this->CI->db->where('active', 1);

		// get navigation from pages
		$this->CI->db->select('uri, pageID as navID, pageName as navName');
		
		// nav has to be active because its parents
		$this->CI->db->where('navigation', 1);
		
		$this->CI->db->order_by('pageOrder', 'asc');
		
		$query = $this->CI->db->get('pages');
		
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}		
	}

	function get_nav_children($navID = '')
	{
		// default where
		$this->CI->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// get nav by ID
		$this->CI->db->where('parentID', $navID);
		
		// where parent is set
		$this->CI->db->where('active', 1);

		// select
		$this->CI->db->select('uri, pageID as navID, pageName as navName');
		
		// where viewable
		$this->CI->db->where('navigation', 1);
		
		// page order
		$this->CI->db->order_by('pageOrder', 'asc');
		
		// grab
		$query = $this->CI->db->get('pages');
				
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}		
	}

	function parse_template($body, $condense = FALSE, $link = '', $mkdn = TRUE)
	{		
		$body = $this->parse_body($body, $condense, $link, $mkdn);
		
		return $body;
	}

	function parse_body($body, $condense = FALSE, $link = '', $mkdn = TRUE)
	{		
		// parse for images		
		$body = $this->parse_images($body);

		// parse for files
		$body = $this->parse_files($body);

		// parse for files
		$body = $this->parse_includes($body);

		// parse for modules
		$this->template = $this->parse_modules($body, $this->template);		
		// site globals
		$body = str_replace('{site:name}', $this->CI->site->config['siteName'], $body);
		$body = str_replace('{site:domain}', $this->CI->site->config['siteDomain'], $body);
		$body = str_replace('{site:url}', $this->CI->site->config['siteURL'], $body);
		$body = str_replace('{site:email}', $this->CI->site->config['siteEmail'], $body);
		$body = str_replace('{site:tel}', $this->CI->site->config['siteTel'], $body);		
		$body = str_replace('{site:currency}', $this->CI->site->config['currency'], $body);
		$body = str_replace('{site:currency-symbol}', currency_symbol(), $body);

		// logged in userdata
		$body = str_replace('{userdata:id}', ($this->CI->session->userdata('userID')) ? $this->CI->session->userdata('userID') : '', $body);
		$body = str_replace('{userdata:email}', ($this->CI->session->userdata('email')) ? $this->CI->session->userdata('email') : '', $body);
		$body = str_replace('{userdata:username}', ($this->CI->session->userdata('username')) ? $this->CI->session->userdata('username') : '', $body);
		$body = str_replace('{userdata:name}', ($this->CI->session->userdata('firstName') && $this->CI->session->userdata('lastName')) ? $this->CI->session->userdata('firstName').' '.$this->CI->session->userdata('lastName') : '', $body);		
		$body = str_replace('{userdata:first-name}', ($this->CI->session->userdata('firstName')) ? $this->CI->session->userdata('firstName') : '', $body);
		$body = str_replace('{userdata:last-name}', ($this->CI->session->userdata('lastName')) ? $this->CI->session->userdata('lastName') : '', $body);

		// other useful stuff
		$body = str_replace('{date}', dateFmt(date("Y-m-d H:i:s"), ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'), $body);
		$body = str_replace('{date:unixtime}', time(), $body);
		
		// condense
		if ($condense)
		{
			if ($endchr = strpos($body, '{more}'))
			{
				$body = substr($body, 0, ($endchr + 6));
				$body = str_replace('{more}', '<p class="more"><a href="'.$link.'" class="button more">Read more</a></p>', $body);				
			}
		}
		else
		{
			$body = str_replace('{more}', '', $body);
		}

		// parse for clears
		$body = str_replace('{clear}', '<div style="clear:both;"/></div>', $body);

		// parse for pads
		$body = str_replace('{pad}', '<div style="padding-bottom:10px;width:10px;clear:both;"/></div>', $body);
		
		// parse body for markdown and images
		if ($mkdn === TRUE)
		{
			// parse for mkdn
			$body = mkdn($body);
		}
		
		return $body;
	}

	function parse_modules($body, $template)
	{
		// get web forms
		if (preg_match_all('/{webform:([A-Za-z0-9_\-]+)}/i', $body, $matches))
		{
			// filter matches
			$webformID = preg_replace('/{|}/', '', $matches[0][0]);
			$webform = $this->CI->core->get_web_form_by_ref($matches[1][0]);
			$template[$webformID] = '';
			$required = array();
	
			// get web form
			if ($webform)
			{
				// set fields
				if ($webform['fieldSet'] == 1)
				{
					$required[] = 'fullName';
					$required[] = 'subject';
					$required[] = 'message';					

					// populate template
					$template[$webformID] .= '
						<div class="formrow field-fullName">
							<label for="fullName">Full Name</label>
							<input type="text" id="fullName" name="fullName" value="'.$this->CI->input->post('fullName').'" class="formelement" />
						</div>
			
						<div class="formrow field-email">
							<label for="email">Email</label>
							<input type="text" id="email" name="email" value="'.$this->CI->input->post('email').'" class="formelement" />
						</div>
	
						<div class="formrow field-subject">
							<label for="subject">Subject</label>
							<input type="text" id="subject" name="subject" value="'.$this->CI->input->post('subject').'" class="formelement" />
						</div>
	
						<div class="formrow field-message">		
							<label for="message">Message</label>
							<textarea id="message" name="message" class="formelement small">'.$this->CI->input->post('message').'</textarea>
						</div>
					';
				}
				
				// set fields
				if ($webform['fieldSet'] == 2)
				{
					$required[] = 'fullName';

					// populate template
					$template[$webformID] .= '
						<div class="formrow field-fullName">
							<label for="fullName">Full Name</label>
							<input type="text" id="fullName" name="fullName" value="'.$this->CI->input->post('fullName').'" class="formelement" />
						</div>
			
						<div class="formrow field-email">
							<label for="email">Email</label>
							<input type="text" id="email" name="email" value="'.$this->CI->input->post('email').'" class="formelement" />
						</div>

						<input type="hidden" name="subject" value="'.$webform['formName'].'" />
					';
				}

				// set fields
				if ($webform['fieldSet'] == 0)
				{
					// populate template
					$template[$webformID] .= '
						<input type="hidden" name="subject" value="'.$webform['formName'].'" />
					';
				}

				// set account
				if ($webform['account'] == 1)
				{
					// populate template
					$template[$webformID] .= '
						<input type="hidden" name="subject" value="'.$webform['formName'].'" />					
						<input type="hidden" name="message" value="'.$webform['outcomeMessage'].'" />
						<input type="hidden" name="groupID" value="'.$webform['groupID'].'" />						
					';
				}

				// set required
				if ($required)
				{
					$template[$webformID] .= '
						<input type="hidden" name="required" value="'.implode('|', $required).'" />
					';
				}

				// output encoded webform ID
				$template[$webformID] .= '
					<input type="hidden" name="formID" value="'.$this->CI->core->encode($matches[1][0]).'" />
				';	
			}
			else
			{
				$template[$webformID] = '';
			}
		}

		// get blog headlines
		if (preg_match_all('/{headlines:blog(:category(\(([A-Za-z0-9_-]+)\))?)?(:limit(\(([0-9]+)\))?)?}/i', $body, $matches))
		{
			// load blog model
			$this->CI->load->model('blog/blog_model', 'blog');

			// filter through matches
			for ($x = 0; $x < sizeof($matches[0]); $x++)
			{
				// filter matches
				$headlineID = preg_replace('/{|}/', '', $matches[0][$x]);
				$limit = ($matches[6][$x]) ? $matches[6][$x] : $this->CI->site->config['headlines'];			
				$headlines = ($matches[3][$x]) ? $this->CI->blog->get_posts_by_category($matches[3][$x], $limit) : $this->CI->blog->get_posts($limit);
			
				// get latest posts
				if ($headlines)
				{	
					// fill up template array
					$i = 0;
					foreach ($headlines as $headline)
					{
						// get rid of any template tags
						$headlineBody = $this->parse_body($headline['body'], TRUE, site_url('blog/'.dateFmt($headline['dateCreated'], 'Y/m').'/'.$headline['uri']));
						$headlineExcerpt = $this->parse_body($headline['excerpt'], TRUE, site_url('blog/'.dateFmt($headline['dateCreated'], 'Y/m').'/'.$headline['uri']));
	
						// populate loop
						$template[$headlineID][$i] = array(
							'headline:link' => site_url('blog/'.dateFmt($headline['dateCreated'], 'Y/m').'/'.$headline['uri']),
							'headline:title' => $headline['postTitle'],
							'headline:date' => dateFmt($headline['dateCreated'], ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
							'headline:day' => dateFmt($headline['dateCreated'], 'd'),
							'headline:month' => dateFmt($headline['dateCreated'], 'M'),
							'headline:year' => dateFmt($headline['dateCreated'], 'y'),						
							'headline:body' => $headlineBody,
							'headline:excerpt' => $headlineExcerpt,						
							'headline:comments-count' => $headline['numComments'],
							'headline:author' => $this->CI->blog->lookup_user($headline['userID'], TRUE),
							'headline:author-id' => $headline['userID'],
							'headline:class' => ($i % 2) ? ' alt ' : ''						
						);
	
						$i++;
					}
				}
				else
				{
					$template[$headlineID] = array();
				}
			}
		}

		// get events headlines
		if (preg_match_all('/{headlines:events(:limit(\(([0-9]+)\))?)?}/i', $body, $matches))
		{
			// load events model
			$this->CI->load->model('events/events_model', 'events');

			// filter matches
			$headlineID = preg_replace('/{|}/', '', $matches[0][0]);
			$limit = ($matches[3][0]) ? $matches[3][0] : $this->CI->site->config['headlines'];			

			// get latest posts
			if ($headlines = $this->CI->events->get_events($limit))
			{	
				// fill up template array
				$i = 0;
				foreach ($headlines as $headline)
				{
					$headlineBody = $this->parse_body($headline['description'], TRUE, site_url('events/viewevent/'.$headline['eventID']));
					$headlineExcerpt = $this->parse_body($headline['excerpt'], TRUE, site_url('events/viewevent/'.$headline['eventID']));
					
					$template[$headlineID][$i] = array(
						'headline:link' => site_url('events/viewevent/'.$headline['eventID']),
						'headline:title' => $headline['eventTitle'],
						'headline:date' => dateFmt($headline['eventDate'], ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
						'headline:day' => dateFmt($headline['eventDate'], 'd'),
						'headline:month' => dateFmt($headline['eventDate'], 'M'),
						'headline:year' => dateFmt($headline['eventDate'], 'y'),	
						'headline:body' => $headlineBody,
						'headline:excerpt' => $headlineExcerpt,
						'headline:author' => $this->CI->events->lookup_user($headline['userID'], TRUE),
						'headline:author-id' => $headline['userID'],						
						'headline:class' => ($i % 2) ? ' alt ' : ''
					);

					$i++;
				}
			}
			else
			{
				$template[$headlineID] = array();
			}
		}
		
		// get wiki headlines
		if (preg_match_all('/{headlines:wiki(:category(\(([A-Za-z0-9_-]+)\))?)?(:limit(\(([0-9]+)\))?)?}/i', $body, $matches))
		{
			// load wiki model
			$this->CI->load->model('wiki/wiki_model', 'wiki');

			// filter matches
			$headlineID = preg_replace('/{|}/', '', $matches[0][0]);
			$limit = ($matches[3][0]) ? $matches[3][0] : $this->CI->site->config['headlines'];			

			// get latest posts
			if ($headlines = $this->CI->wiki->get_pages($limit))
			{	
				// fill up template array
				$i = 0;
				foreach ($headlines as $headline)
				{
					
					$template[$headlineID][$i] = array(
						'headline:link' => site_url('wiki/' .$headline['uri']),
						'headline:title' => $headline['pageName'],
					);

					$i++;
				}
			}
			else
			{
				$template[$headlineID] = array();
			}
		}
		
		// get gallery
		if (preg_match_all('/{gallery:([A-Za-z0-9_-]+)(:limit\(([0-9]+)\))?}/i', $body, $matches))
		{
			// load libs etc
			$this->CI->load->model('images/images_model', 'images');

			// filter through matches
			for ($x = 0; $x < sizeof($matches[0]); $x++)
			{	
				// filter matches
				$headlineID = preg_replace('/{|}/', '', $matches[0][0]);
				$limit = ($matches[3][$x]) ? $matches[3][$x] : 9;

				// get latest posts
				if ($gallery = $this->CI->images->get_images_by_folder_ref($matches[1][$x], $limit))
				{	
					// fill up template array
					$i = 0;
                    #shuffle($gallery);
					foreach ($gallery as $galleryimage)
					{
						if ($imageData = $this->get_image($galleryimage['imageRef']))
						{
							$imageHTML = display_image($imageData['src'], $imageData['imageName']);
							$imageHTML = preg_replace('/src=("[^"]*")/i', 'src="'.site_url('/images/'.$imageData['imageRef'].strtolower($imageData['ext'])).'"', $imageHTML);
							
							$thumbHTML = display_image($imageData['src'], $imageData['imageName']);
							$thumbHTML = preg_replace('/src=("[^"]*")/i', 'src="'.site_url('/thumbs/'.$imageData['imageRef'].strtolower($imageData['ext'])).'"', $imageHTML);									
							
							$template[$headlineID][$i] = array(
								'galleryimage:link'      => site_url('images/'.$imageData['imageRef'].$imageData['ext']),
								'galleryimage:desc'      => $imageData['imageName'],
                                'galleryimage:title'     => ucwords(str_replace('-', ' ', $imageData['imageRef'])),
								'galleryimage:image'     => $imageHTML,
								'galleryimage:thumb'     => $thumbHTML,
								'galleryimage:filename'  => $imageData['imageRef'].$imageData['ext'],
                                'galleryimage:src'       => $imageData['src'],
								'galleryimage:date'      => dateFmt($imageData['dateCreated'], ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
								'galleryimage:author'    => $this->CI->images->lookup_user($imageData['userID'], TRUE),
								'galleryimage:author-id' => $imageData['userID'],					
								'galleryimage:class'     => $imageData['class']
							);
							
							$i++;
						}
					}
				}
				else
				{
					$template[$headlineID] = array();
				}
			}
		}

		// get shop gateway
		if (preg_match('/{shop:(.+)}|{headlines:shop/i', $body))
		{
			// load messages model
			$this->CI->load->model('shop/shop_model', 'shop');
			
			// shop globals
			$template['shop:email'] = $this->CI->site->config['shopEmail'];
			$template['shop:paypal'] = $this->CI->shop->paypal_url;
			$template['shop:gateway'] = ($this->CI->site->config['shopGateway'] == 'sagepay' || $this->CI->site->config['shopGateway'] == 'authorize') ? site_url('/shop/checkout') : $this->CI->shop->gateway_url;
			
			// get shop headlines
			if (preg_match_all('/{headlines:shop(:category\(([A-Za-z0-9_-]+)\))?(:limit\(([0-9]+)\))?}/i', $body, $matches))
			{
				// filter matches
				$headlineID = preg_replace('/{|}/', '', $matches[0][0]);
				$limit = ($matches[4][0]) ? $matches[4][0] : $this->CI->site->config['headlines'];
				$catSafe = $matches[2][0];
					
				// get latest posts
				if ($headlines = $this->CI->shop->get_latest_products($catSafe, $limit))
				{	
					// fill up template array
					$i = 0;
					foreach ($headlines as $headline)
					{
						// get body and excerpt
						$headlineBody = (strlen($headline['description']) > 100) ? substr($headline['description'], 0, 100).'...' : $headline['description'];
						$headlineExcerpt = nl2br($headline['excerpt']);
	
						// get images
						if (!$headlineImage = $this->CI->uploads->load_image($headline['productID'], false, true))
						{
							$headlineImage['src'] = $this->CI->config->item('staticPath').'/images/nopicture.jpg';
						}	

						// get images
						if (!$headlineThumb = $this->CI->uploads->load_image($headline['productID'], true, true))
						{
							$headlineThumb['src'] = $this->CI->config->item('staticPath').'/images/nopicture.jpg';
						}
						
						// populate template
						$template[$headlineID][$i] = array(
							'headline:id' => $headline['productID'],
							'headline:link' => site_url('shop/'.$headline['productID'].'/'.strtolower(url_title($headline['productName']))),
							'headline:title' => $headline['productName'],
							'headline:subtitle' => $headline['subtitle'],
							'headline:date' => dateFmt($headline['dateCreated'], ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
							'headline:body' => $headlineBody,
							'headline:excerpt' => $headlineExcerpt,
							'headline:price' => currency_symbol().number_format($headline['price'],2),
							'headline:image-path' => $headlineImage['src'],
							'headline:thumb-path' => $headlineThumb['src'],
							'headline:cell-width' => floor(( 1 / $limit) * 100),
							'headline:price' => currency_symbol().number_format($headline['price'],2),
							'headline:stock' => $headline['stock'],
							'headline:class' => ($i % 2) ? ' alt ' : ''
						);
	
						$i++;
					}
				}
				else
				{
					$template[$headlineID] = array();
				}
			}

			// get shop headlines
			if (preg_match_all('/{headlines:shop:featured(:limit(\(([0-9]+)\))?)?}/i', $body, $matches))
			{
				// filter matches
				$headlineID = preg_replace('/{|}/', '', $matches[0][0]);
				$limit = ($matches[3][0]) ? $matches[3][0] : $this->CI->site->config['headlines'];				
	
				// get latest posts
				if ($headlines = $this->CI->shop->get_latest_featured_products($limit))
				{	
					// fill up template array
					$i = 0;
					foreach ($headlines as $headline)
					{
						// get body and excerpt
						$headlineBody = (strlen($headline['description']) > 100) ? substr($headline['description'], 0, 100).'...' : $headline['description'];
						$headlineExcerpt = nl2br($headline['excerpt']);
												
						// get images
						if (!$headlineImage = $this->CI->uploads->load_image($headline['productID'], false, true))
						{
							$headlineImage['src'] = $this->CI->config->item('staticPath').'/images/nopicture.jpg';
						}
	
						// get thumb
						if (!$headlineThumb = $this->CI->uploads->load_image($headline['productID'], true, true))
						{
							$headlineThumb['src'] = $this->CI->config->item('staticPath').'/images/nopicture.jpg';
						}
							
						$template[$headlineID][$i] = array(
							'headline:id' => $headline['productID'],
							'headline:link' => site_url('shop/'.$headline['productID'].'/'.strtolower(url_title($headline['productName']))),
							'headline:title' => $headline['productName'],
							'headline:subtitle' => $headline['subtitle'],
							'headline:date' => dateFmt($headline['dateCreated'], ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
							'headline:body' => $headlineBody,
							'headline:excerpt' => $headlineExcerpt,
							'headline:price' => currency_symbol().number_format($headline['price'],2),
							'headline:image-path' => $headlineImage['src'],
							'headline:thumb-path' => $headlineThumb['src'],
							'headline:cell-width' => floor(( 1 / $limit) * 100),
							'headline:price' => currency_symbol().number_format($headline['price'],2),
							'headline:stock' => $headline['stock'],
							'headline:class' => ($i % 2) ? ' alt ' : ''
						);
	
						$i++;
					}
				}
				else
				{
					$template[$headlineID] = array();
				}
			}

			// get shop cart headlines
			if (preg_match('/({headlines:shop:((.+)?)})+/i', $body))
			{
				// get shopping cart
				$cart = $this->CI->shop->load_cart();
	
				// get latest posts
				if ($headlines = $cart['cart'])
				{	
					// fill up template array
					$i = 0;
					foreach ($headlines as $headline)
					{					
						$template['headlines:shop:cartitems'][$i] = array(
							'headline:link' => site_url('shop/'.$headline['productID'].'/'.strtolower(url_title($headline['productName']))),
							'headline:title' => $headline['productName'],
							'headline:quantity' => $headline['quantity'],
							'headline:price' => currency_symbol().(number_format($headline['price'] * $headline['quantity'], 2)),
							'headline:class' => ($i % 2) ? ' alt ' : ''
						);
	
						$i++;
					}
					$template['headlines:shop:numitems'] = count($headlines);
					$template['headlines:shop:subtotal'] = currency_symbol().number_format($cart['subtotal'], 2);
				}
				else
				{
					$template['headlines:shop:numitems'] = 0;
					$template['headlines:shop:subtotal'] = currency_symbol().number_format(0, 2);
					$template['headlines:shop:cartitems'] = array();
				}
			}
			
			// get shop navigation
			if (preg_match('/({shop:categories((.+)?)})+/i', $body))
			{
				$template['shop:categories'] = '';
				
				if ($categories = $this->CI->shop->get_category_parents())
				{
                  
                  
                  $curr_segment = $this->CI->uri->segment(3);
                  
					$i = 1;
					foreach($categories as $nav)
					{
                      $class = ($curr_segment == $nav['catSafe'] ? 'current' : '');
						// get subnav
						if ($children = $this->CI->shop->get_category_children($nav['catID']))
						{
							$template['shop:categories'] .= '<li class="'.$class.' expanded ';
							if ($i == 1)
							{
								$template['shop:categories'] .= 'first ';
							}
							if ($i == sizeof($categories))
							{
								$template['shop:categories'] .= 'last ';
							}
                            
							$template['shop:categories'] .= '"><a href="'.site_url('shop/browse/'.$nav['catSafe']).'">'.htmlentities($nav['catName'], NULL, 'UTF-8').'</a><ul class="subnav">';
							
							foreach($children as $child)
							{
								$template['shop:categories'] .= '<li class="';
								if ($child['catID'] == $this->CI->uri->segment(3) || $nav['catSafe'] == $this->CI->uri->segment(2))
								{
                                  $template['shop:categories'] .= 'active selected';
								}
								$template['shop:categories'] .= '"><a href="/shop/'.$nav['catSafe'].'/'.$child['catSafe'].'">'.htmlentities($child['catName'], NULL, 'UTF-8').'</a></li>';
							}
							$template['shop:categories'] .= '</ul>';
						}					
						else
						{
							$template['shop:categories'] .= '<li class="';
							if ($nav['catID'] == $this->CI->uri->segment(3) || $nav['catSafe'] == $this->CI->uri->segment(3))
							{
								$template['shop:categories'] .= 'active selected ';
							}
							if ($i == 1)
							{
								$template['shop:categories'] .= 'first ';
							}
							if ($i == sizeof($categories))
							{
								$template['shop:categories'] .= 'last ';
							}
							$template['shop:categories'] .= '"><a href="'.site_url('shop/browse/'.$nav['catSafe']).'">'.htmlentities($nav['catName'], NULL, 'UTF-8').'</a>';
						}
						
						$template['shop:categories'] .= '</li>';					
						$i++;
					}
				}
			}
		}

		// message centre stuff
		if (preg_match('/({((.+)?)messages:unread((.+)?)})+/i', $body))
		{
			// load messages model
			$this->CI->load->model('community/messages_model', 'messages');

			// get message count		
			@$template['messages:unread'] = ($messageCount = $this->CI->messages->get_unread_message_count()) ? $messageCount : 0;		
		}
		
		return $template;
	}
	
	function parse_images($body)
	{
		// parse for images
		preg_match_all('/image\:([a-z0-9\-_]+)/i', $body, $images);
		if ($images)
		{
			foreach($images[1] as $image => $value)
			{
				$imageHTML = '';
				if ($imageData = $this->get_image($value))
				{
					$imageHTML = display_image($imageData['src'], $imageData['imageName'], $imageData['maxsize'], 'id="'.$this->CI->core->encode($this->CI->session->userdata('lastPage').'|'.$imageData['imageID']).'" class="pic '.$imageData['class'].'"');
					$imageHTML = preg_replace('/src=("[^"]*")/i', 'src="'.site_url('/images/'.$imageData['imageRef'].strtolower($imageData['ext'])).'"', $imageHTML);
				}
				elseif ($this->CI->session->userdata('session_admin'))
				{
					$imageHTML = '<a href="'.site_url('/admin/images').'" target="_parent"><img src="'.$this->CI->config->item('staticPath').'/images/btn_upload.png" alt="Upload Image" /></a>';
				}
				$body = str_replace('{image:'.$value.'}', $imageHTML, $body);
			}
		}	

		// parse for thumbs
		preg_match_all('/thumb\:([a-z0-9\-_]+)/i', $body, $images);
		if ($images)
		{
			foreach($images[1] as $image => $value)
			{
				$imageHTML = '';
				if ($imageData = $this->get_image($value))
				{
					$imageHTML = display_image($imageData['thumbnail'], $imageData['imageName'], $imageData['maxsize'], 'id="'.$this->CI->core->encode($this->CI->session->userdata('lastPage').'|'.$imageData['imageID']).'" class="pic thumb '.$imageData['class'].'"');
					$imageHTML = preg_replace('/src=("[^"]*")/i', 'src="/thumbs/'.$imageData['imageRef'].strtolower($imageData['ext']).'"', $imageHTML);
				}
				elseif ($this->CI->session->userdata('session_admin'))
				{
					$imageHTML = '<a href="'.site_url('/admin/images').'" target="_parent"><img src="'.$this->CI->config->item('staticPath').'/images/btn_upload.png" alt="Upload Image" /></a>';
				}
				$body = str_replace('{thumb:'.$value.'}', $imageHTML, $body);
			}
		}	
		
		return $body;
	}

	function get_image($imageRef)
	{	
		$this->CI->db->where('siteID', $this->siteID);
		$this->CI->db->where('deleted', 0);
		$this->CI->db->where('imageRef', $imageRef);
		$query = $this->CI->db->get('images');
		
		// get data
		if ($query->num_rows())
		{
			// path to uploads
			$pathToUploads = $this->uploadsPath;

			$row = $query->row_array();

			$image = $row['filename'];
			$ext = substr($image,strpos($image,'.'));
	
			$imagePath = $pathToUploads.'/'.$image;
			$thumbPath = str_replace($ext, '', $imagePath).'_thumb'.$ext;

			$row['ext'] = $ext;
			$row['src'] = $imagePath;
			$row['thumbnail'] = (file_exists('.'.$thumbPath)) ? $thumbPath : $imagePath;
			
			return $row;
		}
		else
		{
			return FALSE;
		}		
	}

	function parse_files($body)
	{
		// parse for files
		preg_match_all('/file\:([a-z0-9\-_]+)/i', $body, $files);
		if ($files)
		{
			foreach($files[1] as $file => $value)
			{
				$fileData = $this->get_file($value);
					
				$body = str_replace('{file:'.$value.'}', anchor('/files/'.$fileData['fileRef'].$fileData['extension'], 'Download', 'class="file '.str_replace('.', '', $fileData['extension']).'"'), $body);
			}
		}
		
		return $body;
	}

	function get_file($fileRef)
	{
		// get data
		if ($file = $this->CI->uploads->load_file($fileRef, TRUE))
		{	
			return $file;
		}
		else
		{
			return false;
		}		
	}
    
    function list_shop_categories()
    {
		$where = array();
		$shop_cats = $this->CI->core->viewall('shop_cats', $where, array('catOrder', 'ASC'));
		$shop_cats_arr = array();
		$counter = 0;
		foreach($shop_cats['shop_cats'] as $value)
		{
	    	if($value['home'])
			{
		        $shop_cats_arr[$counter] = array(
		            'catLink'  => site_url('shop/browse/'.$value['catSafe']),
		            'catName'  => $value['catName'],
		            'catImage' => '<img src="'.site_url('resize.php?src='.site_url('static/uploads/'.$value['catImage']).'&amp;w=290&amp;h=210&amp;s=0').'" alt="'.$value['catName'].'" />',
		            'catColor' => $value['catColor'],
		            'catDesc'  => $value['description'],
					'catHome' => $value['home']
		        );
				$counter++;
			}
		}
      
      return $shop_cats_arr;
    }
    
    function list_sale_products()
    {
      $this->CI->load->model('shop/shop_model', 'shop');
      $this->CI->load->library('image_lib');
	  
      $list_sales_products = $this->CI->shop->get_sales_product();
      $itemsPerRow = 4;
      $counter = 0;
      $sale_prod_arr = array();
      foreach($list_sales_products as $sale_prod)
      {
         $productBody    = (strlen($this->_strip_markdown($sale_prod['description'])) > 100) ? substr($this->_strip_markdown($sale_prod['description']), 0, 100).'...' : nl2br($this->_strip_markdown($sale_prod['description']));
         $productExcerpt = nl2br($this->_strip_markdown($sale_prod['excerpt']));

          // get images
          if(!$image = $this->CI->uploads->load_image($sale_prod['productID'], false, true))
          {
            $image['src'] = site_url('/images/nopicture.jpg');
          }
          if(!$thumb = $this->CI->uploads->load_image($sale_prod['productID'], true, true))
          {
            $thumb['src'] = site_url('/images/nopicture.jpg');
          }
          
          // check for sales price
          if($sale_prod['sale_price'] != '0.00')
          {
            $price = number_format($sale_prod['sale_price'], 2);
          }
          else
          {
            $price = number_format($sale_prod['price'], 2);
          }
		  $price = str_replace('.00', '', $price);
          
		  $img = explode('.', $image['src']);
		  $browse_img = $img[0]."_browse.".$img[1];
		
			//pr(BASEPATH."../..".$image['src']);
			/*
		  if(!file_exists($browse_img))
		  {
			$folder = "/static/uploads";
			$this->CI->image_lib->clear();
			
			//pr($image['src']);
			$imgconfig['image_library'] = 'gd';
			$imgconfig['source_image'] = BASEPATH."../..".$image['src'];
			$imgconfig['create_thumb'] = true;
			$imgconfig['thumb_marker'] = "_browse";
			$imgconfig['maintain_ratio'] = true;
			$imgconfig['width'] = 220;
			$imgconfig['height'] = 290;
			
			$this->CI->image_lib->initialize($imgconfig);
			$rs = $this->CI->image_lib->resize();
			if(!$rs) { echo $this->CI->image_lib->display_errors(); }
		  }
			*/
		  
		  //if(!file_exists($img[0]."_prod.".$img[1]))
          // populate template array
	      $var1 = $this->CI->shop->get_variations($sale_prod['productID'], 1);
		  $var1html = "";
		  if(!empty($var1))
		  {
		  	foreach($var1 as $varitem)
		  	{

				$varbackorder = ($varitem['backorder']==1) ? "(Backorder)" : "";
				$varname = ($varitem['price'] > 0) ? $varitem['variation']." (+".$varitem['price'].")" : $varitem['variation'];
			    $var1html .= "<option value='{$varitem['variationID']}'>{$varname}</option>";
		  	}
	  	  }
		  
	      $var2 = $this->CI->shop->get_variations($sale_prod['productID'], 2);
		  $var2html = "";
		  if(!empty($var2))
		  {
		  	foreach($var2 as $varitem)
		  	{
				$varbackorder = ($varitem['backorder']==1) ? "(Backorder)" : "";
				$varname = ($varitem['price'] > 0) ? $varitem['variation']." (+".$varitem['price'].")" : $varitem['variation'];
			    $var2html .= "<option value='{$varitem['variationID']}'>{$varname}</option>";
		  	}
	  	  }
		  
	      $var3 = $this->CI->shop->get_variations($sale_prod['productID'], 3);
		  $var3html = "";
		  if(!empty($var3))
		  {
		  	foreach($var3 as $varitem)
		  	{
				$varbackorder = ($varitem['backorder']==1) ? "(Backorder)" : "";
				$varname = ($varitem['price'] > 0) ? $varitem['variation']." (+".$varitem['price'].")" : $varitem['variation'];
			    $var3html .= "<option value='{$varitem['variationID']}'>{$varname}</option>";
		  	}
	  	  }
		  
          $sale_prod_arr[$counter] = array(
              'product:id'         => $sale_prod['productID'],
              'product:link'       => site_url('/shop/'.$sale_prod['productID'].'/'.strtolower(url_title($sale_prod['productName']))),
              'product:title'      => substr($sale_prod['productName'], 0, 20) . ' ...',
              'product:subtitle'   => $sale_prod['subtitle'],
              'product:body'       => $productBody,
              'product:excerpt'    => $productExcerpt,
              'product:image-path' => site_url('resize.php?src='.site_url($image['src']).'&amp;w=400&amp;s=0'),
              'product:thumb-path' => site_url('resize.php?src='.site_url($image['src']).'&amp;w=220&amp;h=290&amp;s=0'),
			  //'product:thumb-path' => site_url($browse_img),
			  'product:cell-width' => floor(( 1 / $itemsPerRow) * 100),
              'product:price'      => currency_symbol().$price,
              'product:stock'      => $sale_prod['stock'],
              'product:is_sale'    => ($sale_prod['sale_price'] != '0.00' ? '<span class="is-sale"></span>' : ''),
			  'variation1' 			=> $var1html,
			  'variation2' 			=> $var2html,
			  'variation3' 			=> $var3html
			  
          );
          
          $counter++;
      }
      
      return $sale_prod_arr;
      
    }
    
    function list_random_products()
    {
      $this->CI->load->model('shop/shop_model', 'shop');
      
      $list_products = $this->CI->shop->get_all_products();
      
      shuffle($list_products);
      
      return $list_products;
    }
	
	function list_random_featured_products($limit=5)
    {
      $this->CI->load->model('shop/shop_model', 'shop');
      
      $list_products = $this->CI->shop->get_all_featured_products($limit);
      
      shuffle($list_products);
      
      return $list_products;
    }
    
    function list_partner_products()
    {
      $where = array();
      $partner_products =  $this->CI->core->viewall('partner_products', $where, array('id', 'DESC'), '99');
      
      $counter = 0;
      $prod_arr = array();
      foreach($partner_products['partner_products'] as $prod)
      {
      	if (!$prod['alt_url'])
      	{
      		$prod['alt_url'] = $prod['url'];
      	}
        $prod_arr[$counter] = array(
			'prod:id' => $prod['id'],
            'prod:title'  => $prod['title'],
            'prod:url'    => $prod['url'],
            'prod:image'  => site_url('resize.php?src='.site_url('/static/uploads/'.$prod['image']).'&amp;w=220&amp;s=0'),
            'prod:alt_image' => site_url('resize.php?src='.site_url('/static/uploads/'.$prod['alt_image']).'&amp;w=220&amp;s=0'),
            'prod:alt_url'    => $prod['alt_url']
        );
        $counter++;
      }
      
      return $prod_arr;
      
    }
    
    function _strip_markdown($string)
	{
      return preg_replace('/([*\-#]+)/i', '', preg_replace('/{(.*)}/i', '', $string));
	}
    
   
}
