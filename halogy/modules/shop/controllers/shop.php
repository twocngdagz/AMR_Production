<?php
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

class Shop extends MX_Controller {

	// set defaults
	var $includes_path = '/includes/admin';		// path to includes for header and footer								
	var $permissions = array();
	var $sitePermissions = array();
	var $partials = array();

	function __construct()
	{
		parent::__construct();

		// get permissions for the logged in admin
		if ($this->session->userdata('session_admin'))
		{
			$this->permission->permissions = $this->permission->get_group_permissions($this->session->userdata('groupID'));
		}
		else
		{
			// get site permissions and redirect if it don't have access to this module
			if (!$this->permission->sitePermissions)
			{
				show_error('You do not have permission to view this page');
			}
			if (!in_array($this->uri->segment(1), $this->permission->sitePermissions))
			{
				show_error('You do not have permission to view this page');
			}
		}

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}		

		// load libs
		$this->load->library('auth');
		$this->load->library('tags');
		
		// load models for this controller
		$this->load->model('shop_model', 'shop');

		// load modules
		$this->load->module('pages');
		
		// load partials
		if ($products = $this->shop->get_products('', '', TRUE))
		{
			// load content
			$this->partials['shop:featured'] = $this->_populate_products($products);
		}

		// get latest products		
		if ($latestProducts = $this->shop->get_latest_products('', $this->site->config['headlines']))
		{
			// load content
			$this->partials['shop:latest'] = $this->_populate_products($latestProducts);
		}

		// get popular products		
		if ($popularProducts = $this->shop->get_popular_products($this->site->config['headlines']))
		{
			// load content
			$this->partials['shop:popular'] = $this->_populate_products($popularProducts);
		}

		// get most viewed products
		if ($mostViewedProducts = $this->shop->get_most_viewed_products($this->site->config['headlines']))
		{
			// load content
			$this->partials['shop:mostviewed'] = $this->_populate_products($mostViewedProducts);
		}
		
		// get tags
		if ($popularTags = $this->tags->get_popular_tags('shop_products'))
		{
			foreach($popularTags as $tag)
			{
				$this->partials['shop:tags'][] = array(
					'tag' => $tag['tag'],
					'tag:link' => site_url('/shop/tag/'.$tag['safe_tag']),
					'tag:count' => $tag['count']
				);
			}
		}

		// populate template
		$this->partials['rowpad:featured'] = '';
		for ($x = 0; $x < ($this->shop->siteVars['shopItemsPerRow'] - sizeof($products)); $x++)
		{
			$this->partials['rowpad:featured'] .= '<td width="'.floor((1 / $this->shop->siteVars['shopItemsPerRow']) * 100).'%">&nbsp;</td>';
		}
		$this->partials['rowpad:latest'] = '';
		for ($x = 0; $x < ($this->shop->siteVars['shopItemsPerRow'] - sizeof($latestProducts)); $x++)
		{
			$this->partials['rowpad:latest'] .= '<td width="'.floor((1 / $this->shop->siteVars['shopItemsPerRow']) * 100).'%">&nbsp;</td>';
		}
		$this->partials['rowpad:popular'] = '';
		for ($x = 0; $x < ($this->shop->siteVars['shopItemsPerRow'] - sizeof($popularProducts)); $x++)
		{
			$this->partials['rowpad:popular'] .= '<td width="'.floor((1 / $this->shop->siteVars['shopItemsPerRow']) * 100).'%">&nbsp;</td>';
		}
		$this->partials['rowpad:mostviewed'] = '';
		for ($x = 0; $x < ($this->shop->siteVars['shopItemsPerRow'] - sizeof($mostViewedProducts)); $x++)
		{
			$this->partials['rowpad:mostviewed'] .= '<td width="'.floor((1 / $this->shop->siteVars['shopItemsPerRow']) * 100).'%">&nbsp;</td>';
		}
	}
	
	function index()
	{
		redirect('/shop/featured');
	}

	function featured()
	{
		// get partials
		$output = $this->partials;				

		// set pagination and breadcrumb
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

		// set page title
		$output['page:title'] = 'Featured Products'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
		$output['page:heading'] = 'Featured Products';

		// display with cms layer	
		$this->pages->view('shop_featured', $output, TRUE);
	}

	function browse($cat = '', $parent = '')
	{
		// get partials
		$output = $this->partials;

       
        // get products
          
        if($this->input->post('filter_results'))
        {
          $prod_cat     = $this->input->post('prod_cat');
          $prod_sizes   = $this->input->post('prod_sizes');
          $is_sale      = $this->input->post('is_sale');
          $newly_listed = $this->input->post('newly_listed');

          redirect(site_url('/shop/browse/'.$prod_cat.'?prod_size='.$prod_sizes.'&is_sale='.$is_sale.'&newly_listed='.$newly_listed));
        }
          
		// get category
		if(is_numeric($cat))
		{
          $category = $this->shop->get_category($cat);
		}
		else
		{
          $category = $this->shop->get_category_by_reference($cat, $parent);
		}
        
		
		// get products
		if($category)
		{
          // set catID 
          $catID = $category['catID'];

          // get paging
          if($this->input->post('shopPaging'))
          {
            $this->session->set_userdata('shopPaging', $this->input->post('shopPaging'));
          }
          
          $limit = ($this->session->userdata('shopPaging')) ? $this->session->userdata('shopPaging') : $this->shop->siteVars['shopItemsPerPage'];

          $products                = $this->shop->get_products($catID, NULL, FALSE, $limit, $this->input->get('prod_size'), $this->input->get('is_sale'), $this->input->get('newly_listed'));
          $output['shop:products'] = $this->_populate_products($products);

          // populate template
          $output['rowpad'] = '';
          for($x = 0; $x < ($this->shop->siteVars['shopItemsPerRow'] - sizeof($products)); $x++)
          {
            $output['rowpad'] .= '<td width="'.floor((1 / $this->shop->siteVars['shopItemsPerRow']) * 100).'%">&nbsp;</td>';
          }
            
          $output['shop:paging'] = $limit;			
          $output['shop:total-products'] = ($products) ? $this->pagination->total_rows : 0;
			
			// populate categories
          $output['category:id']           = $category['catID'];
          $output['category:title']        = $category['catName'];
          $output['category:description']  = $this->template->parse_body($category['description']);
          $output['category:link']         = ($category['parentID']) ? '/shop/'.$category['parentSafe'].'/'.$category['catSafe'] : '/shop/'.$category['catSafe'];
          $output['category:parent:id']    = ($category['parentID']) ? $category['parentID'] : '';
          $output['category:parent:title'] = ($category['parentID']) ? $category['parentName'] : '';
          $output['category:parent:link']  = ($category['parentID']) ? '/shop/'.$category['parentSafe'] : '';
          $output['category:banner']       = $category['catBanner'];

          // set pagination and breadcrumb
          $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';
			
          // set page title as category
          $output['page:title'] = (($category['parentName']) ? $category['parentName'].' - ' : '').$category['catName'].(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
          $output['page:heading'] = (($category['parentName']) ? anchor('/shop/'.$category['parentSafe'], $category['parentName']).' &gt; ' : '').$category['catName'];
			
          // set meta description
          if($category['description'])
          {
            $output['page:description'] = $category['description'];
          }
          
          // Filter
          $output['form:prod_cat']     = form_dropdown('prod_cat',     $this->list_categories(), $cat,        'class="formelement"');
          $output['form:prod_sizes']   = form_dropdown('prod_sizes',   $this->prod_sizes(),      $this->input->get('prod_size'), 'class="formelement"');
          $output['form:is_sale']      = form_checkbox('is_sale',      'yes', ($this->input->get('is_sale')       == 'yes' ? 'checked' : '')) . ' On Sale';
          $output['form:newly_listed'] = form_checkbox('newly_listed', 'yes', ($this->input->get('newly_listed')  == 'yes' ? 'checked' : '')) . ' Newly Listed';
          
          // display with cms layer	
          $this->pages->view('shop_browse', $output, TRUE);
		}
		else
		{
          show_404();
		}
	}
	
	function tag($tag = '')
	{
		// get partials
		$output = $this->partials;
		
		// get paging
		if ($this->input->post('shopPaging'))
		{
			$this->session->set_userdata('shopPaging', $this->input->post('shopPaging'));
		}
		$limit = ($this->session->userdata('shopPaging')) ? $this->session->userdata('shopPaging') : $this->shop->siteVars['shopItemsPerPage'];

		// get products
		if ($products = $this->shop->get_products_by_tag($tag, $limit))
		{
			// load content
			$output['shop:products'] = $this->_populate_products($products);
		}

		// populate template
		$output['rowpad'] = '';
		for ($x = 0; $x < ($this->shop->siteVars['shopItemsPerRow'] - sizeof($products)); $x++)
		{
			$output['rowpad'] .= '<td width="'.floor((1 / $this->shop->siteVars['shopItemsPerRow']) * 100).'%">&nbsp;</td>';
		}			
		$output['shop:paging'] = $limit;			
		$output['shop:total-products'] = ($products) ? $this->pagination->total_rows : 0;

		// set pagination and breadcrumb
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

		// set page title as category
		$output['page:title'] = ucwords(str_replace('-', ' ', $tag)).(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
		$output['page:heading'] = ucwords(str_replace('-', ' ', $tag));

		// display with cms layer	
		$this->pages->view('shop_browse', $output, TRUE);
		
		
		// get partials
		$output = $this->partials;
	}

	function search()
	{
		// get partials
		$output = $this->partials;
		
		// set search session var
		if ($this->input->post('query'))
		{
			$this->session->set_userdata('shopSearch', $this->input->post('query'));
		}
		
		// get search
		$search = $this->session->userdata('shopSearch');

		// get paging
		if ($this->input->post('shopPaging'))
		{
			$this->session->set_userdata('shopPaging', $this->input->post('shopPaging'));
		}
		$limit = ($this->session->userdata('shopPaging')) ? $this->session->userdata('shopPaging') : $this->shop->siteVars['shopItemsPerPage'];

		// get products
		if ($products = $this->shop->get_products('', $search, FALSE, $limit))
		{
			// load content
			$output['shop:products'] = $this->_populate_products($products);
		}

		// populate template
		$output['rowpad'] = '';
		for ($x = 0; $x < ($this->shop->siteVars['shopItemsPerRow'] - sizeof($products)); $x++)
		{
			$output['rowpad'] .= '<td width="'.floor((1 / $this->shop->siteVars['shopItemsPerRow']) * 100).'%">&nbsp;</td>';
		}			
		$output['shop:paging'] = $limit;			
		$output['shop:total-products'] = ($products) ? $this->pagination->total_rows : 0;
	
		// populate categories
		$output['category:title'] = 'Search for "'.$search.'"';

		// set pagination and breadcrumb
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

		// set page title as category
		$output['page:title'] = 'Search the Shop'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
		$output['page:heading'] = 'Search for "'.$search.'"';

		// display with cms layer	
		$this->pages->view('shop_browse', $output, TRUE);	
	}

	function viewproduct($productID)
	{
      

      // get partials
      $output = $this->partials;

      // load data
      if(!$product = $this->shop->get_product($productID))
      {
        show_error('No product found!');
      }

      // add a hit
      $this->shop->add_view($productID);		

      // get data
      $image = $this->uploads->load_image($productID, false, true);
      $output['single_product:image-path'] = site_url($image['src']);

      $image = $this->uploads->load_image($productID, true, true);
      $output['single_product:thumb-path'] = site_url($image['src']);

      // get category data
      if($categories = $this->shop->get_cat_ids_for_product($productID))
      {
        // just get the first element
        $categories = array_reverse($categories);

        // filter through getting last element
        foreach($categories as $catID)
        {
          $category = $this->shop->get_category($catID);

          $output['category:id']           = $category['catID'];
          $output['category:title']        = $category['catName'];
          $output['category:description']  = $this->template->parse_body($category['description']);
          $output['category:link']         = ($category['parentID']) ? '/shop/'.$category['parentSafe'].'/'.$category['catSafe'] : '/shop/'.$category['catSafe'];
          $output['category:parent:id']    = ($category['parentID']) ? $category['parentID'] : '';
          $output['category:parent:title'] = ($category['parentID']) ? $category['parentName'] : '';
          $output['category:parent:link']  = ($category['parentID']) ? '/shop/'.$category['parentSafe'] : '';
        }
      }

      // get varations data
      $data['variation1'] = $this->shop->get_variations($productID, 1);
      $data['variation2'] = $this->shop->get_variations($productID, 2);
      $data['variation3'] = $this->shop->get_variations($productID, 3);		

      $single_prod_link = site_url('/shop/'.$product['productID'].'/'.strtolower(url_title($product['productName'])));
      
      // populate template
      $output['single_product:id']       = $product['productID'];
      $output['single_product:link']     = $single_prod_link;
      $output['single_product:title']    = $product['productName'];
      $output['single_product:subtitle'] = $product['subtitle'];
      $output['single_product:body']     = $this->template->parse_body($product['description']);
      $output['single_product:price']    = currency_symbol().number_format(($product['sale_price'] != '0.00' ? $product['sale_price'] : $product['price']), 2);
	  $output['single_product:price']    = str_replace('.00', '', $output['single_product:price']);
      $output['single_product:excerpt']  = $this->template->parse_body($product['excerpt']);
      $output['single_product:stock']    = $product['stock'];
      $output['single_product:category'] = (isset($category) && $category) ? $category['catName'] : '';
      
	  /*
	  if($product['image_1'] && file_exists('/static/uploads/'.$product['image_1']))
      {
        list($width, $height, $type, $attr) = getimagesize(site_url('/static/uploads/'.$product['image_1']));
		$w = $width;
        if($width > 450)
        {
          $w = 450;
        }
        $output['single_product:image_1_large']  = '<li><img src="'.site_url('/resize.php?src='.site_url('/static/uploads/'.$product['image_1']).'&w='.$w.'&s=0').'" alt="'.$product['productName'].'" /></li>';
		$output['single_product:image_1_thumb']  = '<li><img src="'.site_url('/resize.php?src='.site_url('/static/uploads/'.$product['image_1']).'&w=150&s=0').'" alt="'.$product['productName'].'" /></li>';
      }
      else
      {
        $output['single_product:image_1_large']  = '';
        $output['single_product:image_1_thumb']  = '';
      }
      
      if($product['image_2'] && file_exists('/static/uploads/'.$product['image_2']))
      {
        list($width, $height, $type, $attr) = getimagesize(site_url('/static/uploads/'.$product['image_2']));
		$w = $width;
        if($width > 450)
        {
          $w = 450;
        }
        $output['single_product:image_2_large']  = '<li><img src="'.site_url('/resize.php?src='.site_url('/static/uploads/'.$product['image_2']).'&w='.$w.'&s=0').'" alt="'.$product['productName'].'" /></li>';
        $output['single_product:image_2_thumb']  = '<li><img src="'.site_url('/resize.php?src='.site_url('/static/uploads/'.$product['image_2']).'&w=150&s=0').'" alt="'.$product['productName'].'" /></li>';    
      }
      else
      {
        $output['single_product:image_2_large']  = '';
        $output['single_product:image_2_thumb']  = '';
      }
      
      if($product['image_3'] && file_exists('/static/uploads/'.$product['image_3']))
      {
        
        list($width, $height, $type, $attr) = getimagesize(site_url('/static/uploads/'.$product['image_3']));
        $w = $width;
        if($width > 450)
        {
          $w = 450;
        }
        $output['single_product:image_3_large']  = '<li><img src="'.site_url('/resize.php?src='.site_url('/static/uploads/'.$product['image_3']).'&w='.$w.'&s=0').'" alt="'.$product['productName'].'" /></li>';
        $output['single_product:image_3_thumb']  = '<li><img src="'.site_url('/resize.php?src='.site_url('/static/uploads/'.$product['image_3']).'&w=150&s=0').'" alt="'.$product['productName'].'" /></li>';
		
	  }
      else
      {
        $output['single_product:image_3_large']  = '';
        $output['single_product:image_3_thumb']  = '';
      }
      
      if($product['image_4'] && file_exists('/static/uploads/'.$product['image_4']))
      {
        
        list($width, $height, $type, $attr) = getimagesize(site_url('/static/uploads/'.$product['image_4']));
        $w = $width;
        if($width > 450)
        {
          $w = 450;
        }
        $output['single_product:image_4_large']  = '<li><img src="'.site_url('/resize.php?src='.site_url('/static/uploads/'.$product['image_4']).'&w='.$w.'&s=0').'" alt="'.$product['productName'].'" /></li>';
        $output['single_product:image_4_thumb']  = '<li><img src="'.site_url('/resize.php?src='.site_url('/static/uploads/'.$product['image_4']).'&w=150&s=0').'" alt="'.$product['productName'].'" /></li>';
      	
	  }
      else
      {
        $output['single_product:image_4_large']  = '';
        $output['single_product:image_4_thumb']  = '';
      }
      
      if($product['image_5'] && file_exists('/static/uploads/'.$product['image_5']))
      {
        
        list($width, $height, $type, $attr) = getimagesize(site_url('/static/uploads/'.$product['image_5']));
		$w = $width;
        if($width > 450)
        {
          $w = 450;
        }
        $output['single_product:image_5_large']  = '<li><img src="'.site_url('/resize.php?src='.site_url('/static/uploads/'.$product['image_5']).'&w='.$w.'&s=0').'" alt="'.$product['productName'].'" /></li>';
        $output['single_product:image_5_thumb']  = '<li><img src="'.site_url('/resize.php?src='.site_url('/static/uploads/'.$product['image_5']).'&w=150&s=0').'" alt="'.$product['productName'].'" /></li>';        
		
	  }
      else
      {
        $output['single_product:image_5_large']  = '';
        $output['single_product:image_5_thumb']  = '';
      }
      */
	  
	  for($x=1; $x<6; $x++)
	  {
		  if(!empty($product['image_'.$x]) && file_exists(BASEPATH.'../../static/uploads/'.$product['image_'.$x]))
		  {
			  /*
			  $image = site_url('/static/uploads/'.$product['image_'.$x]);
			  $output['single_product:image_'.$x.'_large']  = '<li><img src="'.$image.'" width="450" style="width:450px !important;" alt="" /></li>';
	          $output['single_product:image_'.$x.'_thumb']  = '<li><img src="'.$image.'" width="150" style="width:150px !important;" alt="" /></li>';
			  */
			  
			  list($width, $height, $type, $attr) = getimagesize(BASEPATH.'../../static/uploads/'.$product['image_'.$x]);
	  		  $w = $width;
			  $h = $height;
	          if($width > 450)
	          {
	            $w = 450;
				$ratio = $w / $height;
				$h = round ($ratio * $height);
				$h = (int)$h;
	          }
			  
			  //pr($h, 1);
			  $img_url = site_url('/static/uploads/'.$product['image_'.$x]);
			  $img_url = str_replace('https://', 'http://', $img_url);
			  list($nw, $nh, $ntype, $nattr) = getimagesize(site_url('/resize.php?src='.$img_url.'&w='.$w.'&s=0'));
			  //pr($nh, 1);
			  $output['single_product:image_'.$x.'_large']  = '<li><img src="'.site_url('/resize.php?src='.$img_url.'&amp;w='.$w.'&amp;s=0').'" alt="'.$product['productName'].'" width="'.$nw.'" height="'.$nh.'" /></li>';
	          $output['single_product:image_'.$x.'_thumb']  = '<li><img src="'.site_url('/resize.php?src='.$img_url.'&amp;w=150&amp;s=0').'" alt="'.$product['productName'].'" /></li>'; 
			  
			  
			  /*
	          list($width, $height, $type, $attr) = getimagesize(BASEPATH.'../../static/uploads/'.$product['image_'.$x]);
	  		  $rs = product_image_resize($width, $height, $product['image_'.$x]);
	          $output['single_product:image_'.$x.'_large']  = '<li><img src="'.$rs['main'].'" alt="" /></li>';
	          $output['single_product:image_'.$x.'_thumb']  = '<li><img src="'.$rs['thumb'].'" alt="" /></li>';
			  */
		  }
		  else
		  {
	          $output['single_product:image_'.$x.'_large']  = '';
	          $output['single_product:image_'.$x.'_thumb']  = '';
		  }
	  }
     
      
		// get tags
		if ($product['tags'])
		{
			$tags = explode(',', $product['tags']);
			
			$i = 0;
			foreach ($tags as $tag)
			{
				$output['single_product:tags'][$i]['tag:link'] = site_url('shop/tag/'.$this->tags->make_safe_tag($tag));
				$output['single_product:tags'][$i]['tag'] = $tag;
				
				$i++;
			}
		}	
		
		$output['form:name']   = set_value('fullName', $this->session->userdata('firstName').' '.$this->session->userdata('lastName'));
		$output['form:email']  = set_value('email', $this->session->userdata('email'));
		$output['form:review'] = $this->input->post('review');

		// get reviews
		if ($reviews = $this->shop->get_reviews($product['productID']))
		{
			$i = 0;
			foreach ($reviews as $review)
			{
				// populate template
				$output['product:reviews'][$i]['review:class'] = ($i % 2) ? ' alt ' : '';
				$output['product:reviews'][$i]['review:id'] = $review['reviewID'];
				$output['product:reviews'][$i]['review:gravatar'] = 'http://www.gravatar.com/avatar.php?gravatar_id='.md5(trim($review['email'])).'&default='.urlencode(site_url('/static/uploads/avatars/noavatar.gif'));
				$output['product:reviews'][$i]['review:author'] = $review['fullName'];
				$output['product:reviews'][$i]['review:date'] = dateFmt($review['dateCreated'], ($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y');
				$output['product:reviews'][$i]['review:body'] = nl2br(strip_tags($review['review']));
				$output['product:reviews'][$i]['review:rating'] = $review['rating'];
				
				$i++;
			}
		}

		// set status
		if ($product['status'] == 'S')
		{
			$output['single_product:status'] = '<span class="instock">In stock</span>';
		}
		if ($product['status'] == 'O' || ($this->site->config['shopStockControl'] && !$product['stock']))
		{
			$output['single_product:status'] = '<span class="outofstock">Out of stock</span>';
			$output['single_product:stock'] = 0;
		}
		if ($product['status'] == 'P')
		{
			$output['single_product:status'] = '<span class="preorder">Available for pre-order</span>';
		}
		
		// set message
		if ($message = $this->session->flashdata('success'))
		{
			$output['message'] = $message;
		}

		// set title
		$output['page:title'] = $product['productName'].(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
		
		// set meta description
		if ($product['excerpt'])
		{
			$output['page:description'] = $product['excerpt'];
		}

		// load partials
		$output['single_product:variations'] = @$this->parser->parse('partials/variations', $data, TRUE);		

		// output product ID for CMS button
		$output['productID'] = $productID;
        
        // get related products
        $related_products                = $this->shop->get_products(NULL, NULL, FALSE, 4, '', '', '', $productID);
        shuffle($related_products);
        
        $output['shop:related_products'] = $this->_populate_products($related_products);
        $output['success']               = ($this->session->flashdata('success') ? $this->session->flashdata('success') : '');

        // Get users wishlist
        $users_wishlist = $this->shop->users_wishlist($this->session->userdata('userID'));
        
        if(in_array($productID, $users_wishlist))
        {
          $output['wishlist:btn'] = '<div class="remove-from-wishlist"><a href="'.$single_prod_link.'/remove-from-wishlist" onclick="return confirm(\'Are you sure you want to remove this product from your wishlist? \');">remove from wishlist</a></div>';
        }
        else
        {
			//RITZI
			$curr_user = $this->session->userdata('email');
			$reg_page = site_url('/shop/create_account');
			if(!empty($curr_user))
				$output['wishlist:btn'] = '<div class="add-to-wishlist"><a href="'.$single_prod_link.'/add-to-wishlist" onclick="return confirm(\'Are you sure you want to add this product to your wishlist? \');">add to wishlist</a></div>';
			else
				$output['wishlist:btn'] = '<div class="add-to-wishlist"><a href="'.$reg_page.'" onclick="return confirm(\'Register in order to save items to the wishlist! \');">add to wishlist</a></div>';
        }
        
		// display with cms layer	
		$this->pages->view('shop_product', $output, TRUE);
	}

	function cart($quantity = '', $id = '')
	{	
		// get partials
		$output = $this->partials;
		
		// handle upsell
		if ($this->input->post('upsellID'))
		{
			$upsell = $this->shop->get_upsell($this->input->post('upsellID'));
			
			// remove products
			if ($upsell['remove'])
			{
				foreach((array)$this->session->userdata('cart') as $key => $quantity)
				{
					$cartProduct = $this->shop->unpack_item($key, $quantity);
					$key = $this->core->encode($key);					
					
					foreach(explode(',', $upsell['productIDs']) as $removeProductID)
					{
						if ($cartProduct['productID'] == $removeProductID)
						{
							$this->shop->remove_from_cart($key);
						}
					}
				}
			}
			
			// add to cart
			$this->shop->add_to_cart($upsell['productID'], 1);
		}
		
		// cart functions (whats posted)
		else
		{	
			if ($quantity == 'add' && $this->input->post('productID'))
			{
				$this->shop->add_to_cart($this->input->post('productID'), $this->input->post('quantity'));	
			}
			if ($quantity == 'remove')
			{
				$this->shop->remove_from_cart($id);
			}
			if ($quantity == 'update')
			{
				foreach((array)$this->session->userdata('cart') as $key => $quantity)
				{				
					$key = $this->core->encode($key);
					$updateItem = $this->input->post('quantity');
					$this->shop->update_cart($key, $updateItem[$key]);
				}
			}
			if ($quantity == 'remove_donation')
			{
				$this->session->unset_userdata('cart_donation');
			}
		}

		// find out if there is a discount code applied
		if (isset($_POST['discountCode']))
		{
			$this->session->set_userdata('discountCode', $this->input->post('discountCode'));
			//$this->add_dicount($_POST['discountCode']);
		}

		// find out if there is a donation
		if ($donation = $this->input->post('donation'))
		{
			$this->session->set_userdata('cart_donation', $donation);
		}	

		// get shipping bands and modifiers
		$shippingBand = $this->input->post('shippingBand');
		$shippingModifier = $this->input->post('shippingModifier');

		// set shipping bands and modifiers
		if ($shippingBand || $shippingModifier)
		{
			if ($shippingBand != $this->session->userdata('shippingBand'))
			{
				$this->session->set_userdata('shippingBand', $shippingBand);
				$this->session->unset_userdata('shippingModifier');
			}
			elseif ($shippingModifier != $this->session->userdata('shippingModifier'))
			{
				$this->session->set_userdata('shippingModifier', $shippingModifier);
			}
		}
		elseif (!$this->session->userdata('shippingBand'))
		{
			$this->session->set_userdata('shippingBand', 1);
		}
		
		// set shipping band notes
		if ($this->session->userdata('shippingBand') > 1 || $this->session->userdata('shippingModifier'))
		{
			$shippingBand = $this->shop->get_band_by_multiplier($this->session->userdata('shippingBand'));
			$shippingNotes = 'Shipping method: '.$shippingBand['bandName'];
			
			if ($this->session->userdata('shippingModifier'))
			{
				$shippingModifier = $this->shop->get_modifier_by_multiplier($this->session->userdata('shippingModifier'));
				$shippingNotes .= ' ('.$shippingModifier['modifierName'].')';
			}

			$this->session->set_userdata('shippingNotes', $shippingNotes);
		}
		else
		{
			$this->session->unset_userdata('shippingNotes');
		}
		
		// redirects
		if ($this->input->post('checkout'))
		{
			redirect('/shop/checkout');
		}

		// load cart
		$data = $this->shop->load_cart();
		
		// populate template
		$output['cart:discounts'] = ($data['discounts'] > 0) ? currency_symbol().number_format(@$data['discounts'], 2) : '';
		$output['cart:gc_discounts'] = ($data['gc_discounts'] > 0) ? currency_symbol().number_format(@$data['gc_discounts'], 2) : '';
		$output['cart:subtotal'] = currency_symbol().number_format(@$data['subtotal'], 2);
		$output['cart:postage'] = currency_symbol().number_format(@$data['postage'], 2);
		$output['cart:tax'] = ($data['tax'] > 0) ? currency_symbol().number_format(@$data['tax'], 2) : '';
		$output['cart:total'] = currency_symbol().number_format((@$data['subtotal'] + @$data['postage'] + @$data['tax']), 2);

		// set totals to session
		$this->session->set_userdata('cart_postage', @$data['postage']);
		$this->session->set_userdata('cart_total', @$data['subtotal']);			

		// get shipping bands
		$data['shippingBand'] = ($this->input->post('shippingBand')) ? $this->input->post('shippingBand') : $this->session->userdata('shippingBand');
		//pr($data);

		// get shipping modifiers		
		if ($data['bands'] = $this->shop->get_bands())
		{
			// multiplier
			$multiplier = ($this->session->userdata('shippingBand')) ? $this->session->userdata('shippingBand') : 1;
			
			$data['shippingModifier'] = $this->session->userdata('shippingModifier');
			$data['modifiers'] = $this->shop->get_modifiers($multiplier);
		}
		
		// load content
		$output['cart:items']         = @$this->parser->parse('partials/cart', $data, TRUE);
		$output['cart:bands']         = @$this->parser->parse('partials/bands', $data, TRUE);
		$output['cart:modifiers']     = @$this->parser->parse('partials/modifiers', $data, TRUE);
		$output['form:discount-code'] = $this->session->userdata('discountCode');
		$output['form:donation']      = ($this->session->userdata('cart_donation')) ? number_format($this->session->userdata('cart_donation'), 2, '.', '') : '';	
		
		// set default upsell vars
		$upsell = '';
		
		// get upsell
		if ($this->shop->get_product_ids_in_cart() && $upsells = $this->shop->get_upsells())
		{
			// filter through each upsell
			foreach($upsells as $row)
			{
				$upsellArray = array();
				
				// get upsell based on total value
				if ($row['type'] == 'V')
				{
					if ($data['subtotal'] > $row['value'])
					{
						$upsell = $this->shop->get_product($row['productID']);
						$upsell['upsellID'] = $row['upsellID'];
					}
				}
				
				// get upsell based on the number of products
				elseif ($row['type'] == 'N')
				{
					if (sizeof($data['cart']) > $upsell['numProducts'])
					{
						$upsell = $this->shop->get_product($row['productID']);
						$upsell['upsellID'] = $row['upsellID'];
					}
				}
				
				// get upsell based on the products in cart
				elseif ($row['type'] == 'P')
				{
					$upsellProducts = explode(',',$row['productIDs']);
					foreach ($upsellProducts as $upsellProductID)
					{
						if (in_array($upsellProductID, $this->shop->get_product_ids_in_cart()))
						{
							$upsellArray[] = $upsellProductID;
						}
					}
					if (sizeof($upsellArray) == sizeof($upsellProducts))
					{
						$upsell = $this->shop->get_product($row['productID']);
						$upsell['upsellID'] = $row['upsellID'];
					}
				}
			}
		}

		// load upsell
		$output['upsell:id'] = ($upsell) ? $upsell['upsellID'] : '';
		$output['upsell:product-id'] = ($upsell) ? $upsell['productID'] : '';		
		$output['upsell:link'] = ($upsell) ? '/shop/'.$upsell['productID'].'/'.strtolower(url_title($upsell['productName'])) : '';
		$output['upsell:title'] = ($upsell) ? $upsell['productName'] : '';
		$output['upsell:subtitle'] = ($upsell) ? $upsell['subtitle'] : '';
		$output['upsell:body'] = ($upsell) ? $this->template->parse_body($upsell['description']) : '';
		$output['upsell:price'] = ($upsell) ? currency_symbol().number_format($upsell['price'],2) : '';
		$output['upsell:excerpt'] = ($upsell) ? $this->template->parse_body($upsell['excerpt']) : '';
		$output['upsell:stock'] = ($upsell) ? $upsell['stock'] : '';
		$image = ($upsell) ? $this->uploads->load_image($upsell['productID'], false, true) : '';
		$output['upsell:image-path'] = ($image) ? $image['src'] : $this->config->item('staticPath').'/images/nopicture.jpg';
		$image = ($upsell) ? $this->uploads->load_image($upsell['productID'], true, true) : '';
		$output['upsell:thumb-path'] = ($image) ? $image['src'] : $this->config->item('staticPath').'/images/nopicture.jpg';
		
		// get user data
		$user = $this->shop->get_user();
		
		// get user data
		$output['user:email'] = @$user['email'];
		$output['user:name'] = @trim($user['firstName'].' '.$user['lastName']);
		$output['user:first-name'] = @$user['firstName'];
		$output['user:last-name'] = @$user['lastName'];
		$output['user:address1'] = @$user['address1'];
		$output['user:address2'] = @$user['address2'];
		$output['user:address3'] = @$user['address3'];
		$output['user:city'] = @$user['city'];
		$output['user:state'] = @lookup_state($user['state']);
		$output['user:postcode'] = @$user['postcode'];
		$output['user:country'] = @lookup_country($user['country']);
		$output['user:country-code'] = @$user['country'];
		$output['user:phone'] = @$user['phone'];
		
		// get user data
		$output['user:billing-address1'] = @$user['billingAddress1'];
		$output['user:billing-address2'] = @$user['billingAddress2'];
		$output['user:billing-address3'] = @$user['billingAddress3'];
		$output['user:billing-city'] = @$user['billingCity'];
		$output['user:billing-state'] = @lookup_state($user['billingState']);
		$output['user:billing-postcode'] = @$user['billingPostcode'];
		$output['user:billing-country'] = @lookup_country($user['billingCountry']);
		$output['user:billing-country-code'] = @$user['billingCountry'];			
		
		// load errors
		$output['errors'] = (validation_errors()) ? validation_errors() : FALSE;

		// set page title
		$output['page:title'] = 'Shopping Cart'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

		// display with cms layer	
		$this->pages->view('shop_cart', $output, TRUE);
	}

	function checkout()
	{
		// get partials
		$output = $this->partials;
		
		// if the gateway is sagepay or authorize then we need to post first and then redirect
		if (count($_POST))
		{
			if ($this->site->config['shopGateway'] == 'paypalpro')
			{
				if ($response = $this->shop->validate_paypalpro())
				{
					// send order email
					$this->_create_order();
					header("Location: /shop/success");
					exit();
				}
				else
				{
					$this->form_validation->set_error($this->shop->errors);
				}
			}
			elseif ($this->site->config['shopGateway'] == 'authorize')
			{
				if ($response = $this->shop->validate_authorize())
				{
					// send order email
					$this->_create_order();
					header("Location: /shop/success");
					exit();
				}
				else
				{
					$this->form_validation->set_error($this->shop->errors);
				}
			}
			elseif ($this->site->config['shopGateway'] == 'sagepay')
			{
				if (!$this->input->post('Amount'))
				{
					$this->form_validation->set_error('No amount was specified in the form, please go back and try again or contact us.');
				}
				elseif ($response = $this->shop->init_sagepay())
				{
					header("Location: ".$response['NextURL']);
					exit();
				}
				else
				{
					$this->form_validation->set_error($this->shop->errors);
				}
			}
		}

		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_user'))
		{
			redirect('/shop/login/'.$this->core->encode($this->uri->uri_string()));
		}		
		
		// get user data
		$user = $this->shop->get_user();

		// check an address is set up
		if (!@$user['address1'] || !@$user['city'])
		{
			$this->form_validation->set_error('No address appears to be set up yet. Please make sure you update your delivery address.');
		}

		// check a state is set up
		if (@$user['country'] == 'US' && !@$user['state'])
		{
			$this->form_validation->set_error('No State appears to be set for delivery address. Please make sure you update your delivery address.');
		}
		
		// check a zipcode is set up
		if (@$user['country'] == 'US' && !@$user['postcode'])
		{
			$this->form_validation->set_error('No Zipcode appears to be set for delivery address. Please make sure you update your delivery address.');
		}
		
		// check a state is set up
		if (@$user['billingCountry'] == 'US' && !@$user['billingState'])
		{
			$this->form_validation->set_error('No State appears to be set for billing address. Please make sure you update your billing address.');
		}
		
		// check a zipcode is set up
		if (@$user['billingCountry'] == 'US' && !@$user['billingPostcode'])
		{
			$this->form_validation->set_error('No Zipcode appears to be set for billing address. Please make sure you update your billing address.');
		}

		// check country is set
		if (!@$user['country'])
		{
			$this->form_validation->set_error('You haven\'t yet set your country. Please make sure you update your shipping address.');
		}
		
		if ($data = $this->shop->load_cart())
		{
			// get transaction data
			$transaction = $this->shop->insert_transaction();
			
			// populate template
			$output['cart:discounts'] = ($data['discounts'] > 0) ? currency_symbol().number_format(@$data['discounts'], 2) : '';
			$output['cart:gc_discounts'] = ($data['gc_discounts'] > 0) ? currency_symbol().number_format(@$data['gc_discounts'], 2) : '';
			$output['cart:subtotal'] = currency_symbol().number_format($data['subtotal'], 2);
			$output['cart:postage'] = currency_symbol().number_format($data['postage'], 2);
			$output['cart:tax'] = ($data['tax'] > 0) ? currency_symbol().number_format($data['tax'], 2) : '';
			$output['cart:total'] = currency_symbol().number_format(($data['subtotal'] + $data['postage'] + $data['tax']), 2);
			$output['cart:amount'] = number_format(($data['subtotal'] + $data['postage'] + $data['tax']), 2);
			$output['stripe_amt']  = number_format($data['subtotal'] + $data['postage'] + $data['tax'], 2, '', '');
            
			// output transaction data
			$output['transaction:id']       = $transaction['transactionID'];
			$output['transaction:order-id'] = $transaction['orderID'];
			$output['transaction:subtotal'] = $data['subtotal'];
			$output['transaction:postage']  = $data['postage'];
			$output['transaction:amount']   = ($data['subtotal'] + $data['postage'] + $data['tax']);
			$output['transaction:currency'] = $this->site->config['currency'];			
		
			// get transaction data (for partial)
			$data['transaction'] = $transaction;
			$data['user']        = $user;
			$data['subtotal']    = $output['cart:subtotal'];
			$data['amount']      = $output['cart:amount'];
			$data['currency']    = $this->site->config['currency'];

			// get user data
			$output['user:email'] = @$user['email'];
			$output['user:name'] = @trim($user['firstName'].' '.$user['lastName']);
			$output['user:first-name'] = @$user['firstName'];
			$output['user:last-name'] = @$user['lastName'];
			$output['user:address1'] = @$user['address1'];
			$output['user:address2'] = @$user['address2'];
			$output['user:address3'] = @$user['address3'];
			$output['user:city'] = @$user['city'];
			$output['user:state'] = @lookup_state($user['state']);
			$output['user:postcode'] = @$user['postcode'];
			$output['user:country'] = @lookup_country($user['country']);
			$output['user:country-code'] = @$user['country'];
			$output['user:phone'] = @$user['phone'];
			
			// get user data
			$output['user:billing-address1'] = @$user['billingAddress1'];
			$output['user:billing-address2'] = @$user['billingAddress2'];
			$output['user:billing-address3'] = @$user['billingAddress3'];
			$output['user:billing-city'] = @$user['billingCity'];
			$output['user:billing-state'] = @lookup_state($user['billingState']);
			$output['user:billing-postcode'] = @$user['billingPostcode'];
			$output['user:billing-country'] = @lookup_country($user['billingCountry']);
			$output['user:billing-country-code'] = @$user['billingCountry'];			

			// check there is stock for all items in cart
			if ($this->site->config['shopStockControl'])
			{
				// check they aren't ordering more than there is stock
				foreach((array)$this->session->userdata('cart') as $key => $quantity)
				{
					// check there is stock for all items in cart
					if ($this->site->config['shopStockControl'])
					{
						// get ordered products
						$product = $this->shop->unpack_item($key, $quantity);
						if ($quantity > $product['stock'])
						{
							$this->form_validation->set_error('You cannot add any more of this product ("'.$product['productName'].'"). Please remove this item, or adjust the quantity.');
						}
					}
				}
				
				// get ordered products and check item hasn't gone out of stock
				$itemOrders = $this->shop->get_item_orders($transaction['transactionID']);
				foreach ($itemOrders as $order)
				{
					if ($order['stock'] == 0)
					{
						$this->form_validation->set_error('You have an item in your cart ("'.$order['productName'].'") that has gone out of stock during the checkout process. Please remove this item, or contact us for more information.');
						$errors = TRUE;
					}
				}
			}
			
			/*
			// check shipping bands
			if ($bandsResult = $this->shop->get_bands())
			{
				$bands = array();
				foreach($bandsResult as $band)
				{
					$bands[$band['bandID']] = $band['multiplier'];
				}

				// check there are no restricted items in there
				foreach($data['cart'] as $item)
				{
					if ($item['bandID'] > 0 && reset($bands) != $this->session->userdata('shippingBand'))
					{
						$this->form_validation->set_error('You have an item in your cart ("'.$item['productName'].'") that we cannot send to your selected shipping band. Please remove this item, or contact us for more information.');
					}
				}

				// check they are not selecting a shipping band that is not really theirs
				if ($this->site->config['siteCountry'] && $this->session->userdata('shippingBand') == reset($bands) && @$user['country'] != $this->site->config['siteCountry'])
				{
					$this->form_validation->set_error('Your country and your selected shipping band do not match, please amend either your country (Update Address) or your shipping band (Update Order).');
				}
			}
			*/
		}
		else
		{
			show_error('Your cart is empty! You cannot checkout, please go back.');
		}
        
        $this->session->set_userdata('cartOrderID', $transaction['orderID']);
        
		// load errors
		$output['errors'] = (validation_errors()) ? validation_errors() : FALSE;		

		// load content
		$output['cart:items'] = @$this->parser->parse('partials/cart', $data, TRUE);
		$output['shop:checkout'] = @$this->parser->parse('partials/checkout', $data, TRUE);
		
		// post to the same page if paypal pro
		if ($this->site->config['shopGateway'] == 'paypalpro') $output['shop:gateway'] = site_url($this->uri->uri_string());

		// set page title
		$output['page:title'] = 'Checkout'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
		
        // Stripe Payment
        
        //$stripe_path = $_SERVER['DOCUMENT_ROOT'].'/development/another-mother-runner-store/';
		if($_SERVER['HTTP_HOST']=='local' || $_SERVER['HTTP_HOST']=='localhost')
			$stripe_path = $_SERVER['DOCUMENT_ROOT'].'/amr/';
		else
			$stripe_path = $_SERVER['DOCUMENT_ROOT'].'/';
        

        require_once($stripe_path.'lib/Stripe.php');

        $stripe = array(
          //"secret_key"      => "sk_test_ZjpkoNcJCV5TuocqEhBXMKR1",
		  //"publishable_key" => "pk_test_xEBEnoiiJKSw545vv36KpwDM"
		  "secret_key" => "sk_live_Ychmy6ZeAfsLwFGxleIpgnke",
		  "publishable_key" => "pk_live_SmPgHPkEss83JbbJDof1YQIJ"
        );

        Stripe::setApiKey($stripe['secret_key']);
        
        $output['form:stripe_publishable_key'] = $stripe['publishable_key'];
      
        
		// display with cms layer	
		@$this->pages->view('shop_checkout', $output, TRUE);		
	}
    
    function stripe_payment()
    {
      
      $user = $this->shop->get_user();
      
      //$stripe_path = $_SERVER['DOCUMENT_ROOT'].'/development/another-mother-runner-store/';
	if($_SERVER['HTTP_HOST']=='local' || $_SERVER['HTTP_HOST']=='localhost')
		$stripe_path = $_SERVER['DOCUMENT_ROOT'].'/amr/';
	else
		$stripe_path = $_SERVER['DOCUMENT_ROOT'].'/';

      require_once($stripe_path.'lib/Stripe.php');

      $stripe = array(
        //"secret_key"      => "sk_test_ZjpkoNcJCV5TuocqEhBXMKR1",
        //"publishable_key" => "pk_test_xEBEnoiiJKSw545vv36KpwDM"
		"secret_key" => "sk_live_Ychmy6ZeAfsLwFGxleIpgnke",
		"publishable_key" => "pk_live_SmPgHPkEss83JbbJDof1YQIJ"
      );

      Stripe::setApiKey($stripe['secret_key']);
        
      $token  = $this->input->post('stripeToken');
      $data   = $this->shop->load_cart();
	  
	  $str_desc = "";
	  foreach ($dat['cart'] as $key => $item)
	  {
		  $str_desc .= $item['quantity']." x ".$item['productName']." (".$item['variation2']."), ";
	  }
	  $str_desc = trim($str_desc, ", ");
	  
    
      $customer = Stripe_Customer::create(array(
        'email' => $user['email'],
        'card'  => $token
      ));

      $charge = Stripe_Charge::create(array(
        'customer' => $customer->id,
        'amount'   => number_format($data['subtotal'] + $data['postage'] + $data['tax'], 2, '', ''),
        'currency' => 'usd',
		'description' => "Another Mother Runner: ".$str_desc
      ));
      
      if($charge)
      {
        $this->_create_order($this->session->userdata('cartOrderID'));
        redirect('/shop/stripe_payment_success');
      }
      
    }
    
    function stripe_payment_success()
    {
	  $output = array();
      $this->session->unset_userdata('cart');
      $this->session->unset_userdata('cart_ids');
      $this->session->unset_userdata('postage');
      $this->session->unset_userdata('total');	

      $this->pages->view('shop_stripe_payment_success', $output, 'shop');		
    }
    
	function create_account($redirect = '')
	{
		// get partials
		$output = $this->partials;
		
		// set default redirect
		if (!$redirect)
		{
			$redirect = $this->core->encode('/shop/checkout');
		}
		
		// required
		$this->core->required = array(
			'email' => array('label' => 'Email address', 'rules' => 'required|valid_email|unique[users.email]|trim'),
			'password' => array('label' => 'Password', 'rules' => 'required|matches[confirmPassword]'),
			'confirmPassword' => array('label' => 'Confirm Password', 'rules' => 'required'),
			'firstName' => array('label' => 'First name', 'rules' => 'required|trim|ucfirst'),
			'lastName' => array('label' => 'Last name', 'rules' => 'required|trim|ucfirst'),
			'address1' => array('label' => 'Address1', 'rules' => 'required|trim|ucfirst'),
			'address2' => array('label' => 'Address2', 'rules' => 'trim|ucfirst'),
			'address3' => array('label' => 'Town', 'rules' => 'trim|ucfirst'),
			'city' => array('label' => 'City / State', 'rules' => 'required|trim|ucfirst'),
			'postcode' => array('label' => 'ZIP/Postcode', 'rules' => 'required|trim|strtoupper'),
			'phone' => array('label' => 'Phone', 'rules' => 'required|trim')
		);	

		// security check
		if ($this->input->post('username')) $this->core->set['username'] = '';
		if ($this->input->post('premium')) $this->core->set['premium'] = '';
		if ($this->input->post('siteID')) $this->core->set['siteID'] = $this->siteID;
		if ($this->input->post('userID')) $this->core->set['userID'] = '';
		if ($this->input->post('resellerID')) $this->core->set['resellerID'] = '';
		if ($this->input->post('kudos')) $this->core->set['kudos'] = '';
		if ($this->input->post('posts')) $this->core->set['posts'] = '';		

		// set folder (making sure it's not an admin folder)
		$permissionGroupsArray = $this->permission->get_groups('admin');
		foreach((array)$permissionGroupsArray as $group)
		{
			$permissionGroups[$group['groupID']] = $group['groupName'];
		}				
		if ($this->input->post('groupID') > 0 && !@in_array($this->input->post('groupID'), $permissionGroups))
		{
			$this->core->set['groupID'] = $this->input->post('groupID');
		}

		// set date
		$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
		
		// adding APO/FPO address
		if(!empty($_POST['apofpo']))
		{
			$this->core->set['city'] = $this->input->post('city2');
			$this->core->set['state'] = $this->input->post('state2');
			$this->core->set['country'] = $this->input->post('country2');
		}
		if(!empty($_POST['sameAddress']))
		{
			$this->core->set['billingCity'] = $this->input->post('city2');
			$this->core->set['billingState'] = $this->input->post('state2');
			$this->core->set['billingCountry'] = $this->input->post('country2');
		}

		// get values
		$data = $this->core->get_values('users');

		// update table
		if (count($_POST) && $this->core->update('users'))
		{
			// optionally subscribe user to mailing list(s)
			if (is_dir(APPPATH.'modules/emailer'))
			{
				// load lib
				$this->load->module('emailer');
				
				// check they are allowing subscription
				if ($this->input->post('subscription') != 'P' && $this->input->post('subscription') != 'N')
				{
					// requires posted email, and listID
					$this->emailer->subscribe();
				}
			}
			
			// set header and footer
			$emailHeader = str_replace('{name}', trim($this->input->post('firstName').' '.$this->input->post('lastName')), $this->site->config['emailHeader']);
			$emailHeader = str_replace('{first-name}', $this->input->post('firstName'), $emailHeader);
			$emailHeader = str_replace('{last-name}', $this->input->post('lastName'), $emailHeader);
			$emailHeader = str_replace('{email}', $this->input->post('email'), $emailHeader);
			$emailFooter = str_replace('{name}', trim($this->input->post('firstName').' '.$this->input->post('lastName')), $this->site->config['emailFooter']);
			$emailFooter = str_replace('{first-name}', $this->input->post('firstName'), $emailFooter);
			$emailFooter = str_replace('{last-name}', $this->input->post('lastName'), $emailFooter);
			$emailFooter = str_replace('{email}', $this->input->post('email'), $emailFooter);
			$emailAccount = str_replace('{name}', trim($this->input->post('firstName').' '.$this->input->post('lastName')), $this->site->config['emailAccount']);
			$emailAccount = str_replace('{first-name}', $this->input->post('firstName'), $emailAccount);
			$emailAccount = str_replace('{last-name}', $this->input->post('lastName'), $emailAccount);
			$emailAccount = str_replace('{email}', $this->input->post('email'), $emailAccount);			
		
			// send email
			$this->load->library('email');
			$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
			$this->email->to($this->input->post('email'));			
			$this->email->subject('New account set up on '.$this->site->config['siteName']);
			$this->email->message($emailHeader."\n\n".$emailAccount."\n\n----------------------------------\nYour email: ".$this->input->post('email')."\nYour password: ".$this->input->post('password')."\n----------------------------------\n\n".$emailFooter);
			$this->email->send();
			
			// set login username
			$username = array('field' => 'email', 'label' => 'Email address', 'value' => $this->input->post('email'));

			// set admin session name, if given
			if (!$this->auth->login($username, $this->input->post('password'), 'session_user', $this->core->decode($redirect)))
			{
				$this->form_validation->set_error($this->auth->error);
			}
		}

		// load errors
		$output['errors'] = (validation_errors()) ? validation_errors() : FALSE;
		
		// populate template
		$output['form:email'] = ($this->session->flashdata('email')) ? $this->session->flashdata('email') : set_value('email', $this->input->post('email'));
		$output['form:displayName'] = set_value('displayName', $this->input->post('displayName'));
		$output['form:firstName'] = set_value('firstName', $this->input->post('firstName'));
		$output['form:lastName'] = set_value('lastName', $this->input->post('lastName'));
		$output['form:phone'] = set_value('phone', $this->input->post('phone'));		
		$output['form:address1'] = set_value('address1', $this->input->post('address1'));
		$output['form:address2'] = set_value('address2', $this->input->post('address2'));
		$output['form:address3'] = set_value('address3', $this->input->post('address3'));
		$output['form:city'] = set_value('city', $this->input->post('city'));
		$output['select:state'] = @display_states('state', set_value('state', $this->input->post('state')), 'id="state" class="formelement"');
		$output['form:postcode'] = set_value('postcode', $this->input->post('postcode'));
		$output['select:country'] = @display_countries('country', (($this->input->post('country')) ? $this->input->post('country') : $this->site->config['siteCountry']), 'id="country" class="formelement"');
		$output['form:billingAddress1'] = set_value('billingAddress1', $this->input->post('billingAddress1'));
		$output['form:billingAddress2'] = set_value('billingAddress2', $this->input->post('billingAddress2'));
		$output['form:billingAddress3'] = set_value('billingAddress3', $this->input->post('billingAddress3'));
		$output['form:billingCity'] = set_value('billingCity', $this->input->post('billingCity'));
		$output['select:billingState'] = @display_states('billingState', $data['billingState'], 'id="billingState" class="formelement"');
		$output['form:billingPostcode'] = set_value('billingPostcode', $this->input->post('billingPostcode'));
		$output['select:billingCountry'] = @display_countries('billingCountry', (($this->input->post('billingCountry')) ? $this->input->post('billingCountry') : $this->site->config['siteCountry']), 'id="billingCountry" class="formelement"');

		// set page title
		$output['page:title'] = 'Create Account'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

		// display with cms layer	
		@$this->pages->view('shop_create_account', $output, TRUE);			
	}

	function account($redirect = '')
	{
		// get partials
		$output = $this->partials;
		
		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_user'))
		{
			redirect('/shop/login/'.$this->core->encode($this->uri->uri_string()));
		}

		// set object ID
		$objectID = array('userID' => $this->session->userdata('userID'));		

		// required
		$this->core->required = array(
			'email' => array('label' => 'Email', 'rules' => 'valid_email|unique[users.email]|required|trim'),
			'password' => array('label' => 'Password', 'rules' => 'matches[confirmPassword]'),
			'firstName' => array('label' => 'First name', 'rules' => 'required|trim|ucfirst'),
			'lastName' => array('label' => 'Last name', 'rules' => 'required|trim|ucfirst'),
			'address1' => array('label' => 'Address1', 'rules' => 'required|required|trim|ucfirst'),
			'address2' => array('label' => 'Address2', 'rules' => 'trim|ucfirst'),
			'address3' => array('label' => 'Address3', 'rules' => 'trim|ucfirst'),
			'city' => array('label' => 'City / State', 'rules' => 'required|trim|ucfirst'),
			'postcode' => array('label' => 'ZIP/Postcode', 'rules' => 'required|trim|strtoupper'),
			'phone' => array('label' => 'Phone', 'rules' => 'required|trim')			
		);	

		// get values
		$data = $this->core->get_values('users', $objectID);

		// force postcode to upper case
		$this->core->set['postcode'] = strtoupper($this->input->post('postcode'));
		// pr($_POST);
		// adding APO/FPO address
		if(!empty($_POST['apofpo']))
		{
			$this->core->set['city'] = $this->input->post('city2');
			$this->core->set['state'] = $this->input->post('state2');
			$this->core->set['country'] = $this->input->post('country2');
		}
		if(!empty($_POST['sameAddress']))
		{
			$this->core->set['billingCity'] = $this->input->post('city2');
			$this->core->set['billingState'] = $this->input->post('state2');
			$this->core->set['billingCountry'] = $this->input->post('country2');
		}

		// security check
		if ($this->input->post('username')) $this->core->set['username'] = $data['username'];
		if ($this->input->post('premium')) $this->core->set['premium'] = $data['premium'];
		if ($this->input->post('siteID')) $this->core->set['siteID'] = $this->siteID;
		if ($this->input->post('userID')) $this->core->set['userID'] = $data['userID'];
		if ($this->input->post('resellerID')) $this->core->set['resellerID'] = $data['resellerID'];
		if ($this->input->post('kudos')) $this->core->set['kudos'] = $data['kudos'];
		if ($this->input->post('posts')) $this->core->set['posts'] = $data['posts'];
		
		// update
		if (count($_POST) && $this->core->update('users', $objectID))
		{
			// get updated row session
			$user = $this->shop->get_user();
			
			// remove the password field
			unset($user['password']);
	
			// set session data
			$this->session->set_userdata($user);
			
			if ($redirect)
			{
				redirect('/shop/'.$redirect);
			}
			else
			{
				$output['message'] = 'Your details have been updated.';
			}
		}

		// populate template
		$output['form:email'] = set_value('email', $data['email']);
		$output['form:displayName'] = set_value('displayName', $data['displayName']);
		$output['form:firstName'] = set_value('firstName', $data['firstName']);
		$output['form:lastName'] = set_value('lastName', $data['lastName']);
		$output['form:phone'] = set_value('phone', $data['phone']);
		$output['form:address1'] = set_value('address1', $data['address1']);
		$output['form:address2'] = set_value('address2', $data['address2']);
		$output['form:address3'] = set_value('address3', $data['address3']);
		$output['form:city'] = set_value('city', $data['city']);
		$output['select:state'] = @display_states('state',$data['state'], 'id="state" class="formelement"');		
		$output['form:postcode'] = set_value('postcode', $data['postcode']);
		$output['select:country'] = @display_countries('country', set_value('country', $data['country']), 'id="country" class="formelement"');
		$output['form:billingAddress1'] = set_value('billingAddress1', $data['billingAddress1']);
		$output['form:billingAddress2'] = set_value('billingAddress2', $data['billingAddress2']);
		$output['form:billingAddress3'] = set_value('billingAddress3', $data['billingAddress3']);
		$output['form:billingCity'] = set_value('billingCity', $data['billingCity']);
		$output['select:billingState'] = @display_states('billingState', $data['billingState'], 'id="billingState" class="formelement"');		
		$output['form:billingPostcode'] = set_value('billingPostcode', $data['billingPostcode']);
		$output['select:billingCountry'] = @display_countries('billingCountry', set_value('billingCountry', $data['billingCountry']), 'id="billingCountry" class="formelement"');		

		// load errors
		$output['errors'] = (validation_errors()) ? validation_errors() : FALSE;		

		// set page title
		$output['page:title'] = 'My Account'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

		// display with cms layer	
		$this->pages->view('shop_account', $output, TRUE);
	}

	function subscriptions()
	{
		// get partials
		$output = $this->partials;
		
		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_user'))
		{
			redirect('/shop/login/'.$this->core->encode($this->uri->uri_string()));
		}

		// grab data and display
		if ($payments = $this->shop->get_sub_payments($this->session->userdata('userID')))
		{
			foreach($payments as $payment)
			{
				$output['payments'][] = array(
					'payment:subscription-id' => $payment['referenceID'].((!$payment['active']) ? ' (Cancelled)' : ''),
					'payment:date' => dateFmt($payment['paymentDate']),
					'payment:amount' => currency_symbol(TRUE, $payment['currency']).number_format($payment['paymentAmount'],2),
					'payment:link' => site_url('/shop/invoice/subscription/'.$payment['paymentID'])
				);
			}
		}			

		// set pagination and breadcrumb
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

		// set page title
		$output['page:title'] = 'My Subscriptions'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

		// display with cms layer	
		$this->pages->view('shop_subscriptions', $output, TRUE);
	}

	function orders()
	{
		// get partials
		$output = $this->partials;
		
		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_user'))
		{
			redirect('/shop/login/'.$this->core->encode($this->uri->uri_string()));
		}

		// grab data and display
		if ($orders = $this->shop->get_orders('ALL', $this->session->userdata('userID')))
		{
			foreach($orders as $order)
			{
				$output['orders'][] = array(
					'order:id' => $order['transactionCode'],
					'order:date' => dateFmt($order['dateCreated']),
					'order:amount' => currency_symbol().number_format($order['amount'],2),
					'order:link' => site_url('/shop/view_order/'.$order['transactionID'])
				);
			}
		}			

		// set pagination and breadcrumb
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

		// set page title
		$output['page:title'] = 'My Orders'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

		// display with cms layer	
		$this->pages->view('shop_orders', $output, TRUE);
	}

	function view_order($transactionID)
	{
		// get partials
		$output = $this->partials;
		
		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_user'))
		{
			redirect('/shop/login/'.$this->core->encode($this->uri->uri_string()));
		}

		// grab data and display
		if (!$order = $this->shop->get_order($transactionID, $this->session->userdata('userID')))
		{
			show_error('Not a valid order!');
		}

		// grab data and display
		if ($item_orders = $this->shop->get_item_orders($transactionID))
		{
			foreach($item_orders as $item)
			{
				// check if its a file
				if ($item['fileID'])
				{
					$file = $this->shop->get_file($item['fileID']);
				}
				
				$output['items'][] = array(
					'item:id' => $item['productID'],
					'item:title' => $item['productName'],
					'item:link' => site_url('/shop/'.$item['productID'].'/'.strtolower(url_title($item['productName']))),
					'item:details' => (($item['fileID']) ? 
						'('.anchor('/files/'.$this->core->encode($file['fileRef'].'|'.$transactionID), 'Download').')' :
						(($item['variation1']) ? ' ('.$this->site->config['shopVariation1'].': '.$item['variation1'].')' : '').
						(($item['variation2']) ? ' ('.$this->site->config['shopVariation2'].': '.$item['variation2'].')' : '').
						(($item['variation3']) ? ' ('.$this->site->config['shopVariation3'].': '.$item['variation3'].')' : '')
					),				
					'item:quantity' => $item['quantity'],
					'item:amount' => currency_symbol().number_format(($item['price'] * $item['quantity']), 2)
				);
			}

			// output donation if there is any
			if ($order['donation'] > 0)
			{
				$output['items'][sizeof($output['items'])] = array(
					'item:id' => '',
					'item:title' => 'Donation',
					'item:link' => '#',
					'item:details' => '',
					'item:quantity' => 1,
					'item:amount' => currency_symbol().number_format($order['donation'], 2)
				);
			}
		}

		// populate template
		$output['order:id'] = $order['transactionCode'];
		$output['order:first-name'] = ($order['firstName']) ? $order['firstName'] : '';
		$output['order:last-name'] = ($order['lastName']) ? $order['lastName'] : '';
		$output['order:address1'] = ($order['address1']) ? $order['address1'] : '';
		$output['order:address2'] = ($order['address2']) ? $order['address2'] : '';
		$output['order:address3'] = ($order['address3']) ? $order['address3'] : '';
		$output['order:city'] = ($order['city']) ? $order['city'] : '';
		$output['order:country'] = ($order['country']) ? lookup_country($order['country']) : '';
		$output['order:postcode'] = ($order['postcode']) ? $order['postcode'] : '';
		$output['order:phone'] = ($order['phone']) ? $order['phone'] : 'N/A';
		$output['order:email'] = ($order['email']) ? $order['email'] : 'N/A';
		$output['order:discounts'] = ($order['discounts'] > 0) ? currency_symbol().number_format($order['discounts'], 2) : '';		
		$output['order:subtotal'] = currency_symbol().number_format($order['amount'] - $order['postage'] - $order['tax'], 2);
		$output['order:postage'] = currency_symbol().number_format($order['postage'], 2);
		$output['order:tax'] = ($order['tax'] > 0) ? currency_symbol().number_format($order['tax'], 2) : '';
		$output['order:total'] = currency_symbol().number_format($order['amount'], 2);
		$output['order:status'] = $order['trackingStatus'];		
		$output['order:notes'] = ($order['notes']) ? nl2br($order['notes']) : FALSE;

		// set pagination and breadcrumb
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

		// set page title
		$output['page:title'] = 'View Order'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

		// display with cms layer	
		$this->pages->view('shop_view_order', $output, TRUE);
	}
    
       
    function login($redirect = '')
	{
      
      $output      = $this->partials;
      $sessionName = 'session_user';
      
      $redirect    = $redirect ? $redirect : $this->core->encode('/shop/cart');


      if(!$this->session->userdata($sessionName))
      {
		if($this->input->post('checkoutNoRegister'))
		{
			unset($_POST['sameAddress']); unset($_POST['checkoutNoRegister']);
			$_POST['subscription'] = "Y";
			$_POST['groupID'] = 0;
			$_POST['dateCreated'] = date("Y-m-d H:i:s");
			$_POST['currency'] = "USD";
			$_POST['language'] = "english";
			$_POST['notifications'] = 1;
			$_POST['active'] = 1;
			$_POST['siteID'] = 1;
			$_POST['password'] = md5($_POST['email'].'checkoutNoRegister');
			
			$this->db->insert('ha_users', $_POST);
			$newid = $this->db->insert_id();
			if(!empty($newid))
			{
  				$this->auth->login(array('field'=>"email", 'value'=>$_POST['email']), $_POST['email'].'checkoutNoRegister', $sessionName, $this->core->decode($redirect));
			}
		}
		else
		{
			$password_entered = '';
			$email_entered    = '';
			
			if($this->input->post('password')) 
			{ 
			  $password_entered = $this->input->post('password'); 
			}
			
			if($this->input->post('email'))
			{
			  $email_entered = $this->input->post('email'); 
			}

			$username = array(
						  'field' => 'email', 
						  'label' => 'Email address', 
						  'value' => $email_entered
			);

			// set admin session name, if given
			if(!$this->auth->login($username, $password_entered, $sessionName, $this->core->decode($redirect)))
			{
			  $this->form_validation->set_error($this->auth->error);
			} 

			// look up email
			if($email = $this->input->post('email'))
			{				
			  // if registered show login form
			  if($this->shop->lookup_user_by_email($email))
			  {
				$output['registered'] = TRUE;
				$output['user:email'] = $email;
			  }
			  else // else go back to login page
			  {
				$this->session->set_flashdata('email', $email);
				$output['registered'] = FALSE;
				$output['user:email'] = $email;
			  }
			}
		}
      }
      else
      {
        redirect($this->core->decode($redirect));
      }

      // load errors
      if($_POST)
      {
        $output['errors'] = (validation_errors()) ? validation_errors() : FALSE;		
      }
      else
      {
        $output['errors'] = FALSE;
      }
	  
	  $data = $this->shop->load_cart();
	  $output['express_checkout'] = (!empty($data['cart'])) ? "yes" : "";
      
      // set page title
      $output['page:title']     = 'Login to Shop'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
      $output['signupRedirect'] = $redirect;
      
      $output['form:email']     = form_input('email',       set_value('email',    $this->input->post('email')), 'class="formelement"');
      $output['form:password']  = form_password('password', '', 'class="formelement"');
	  
	  $output['select:state'] = @display_states('state', set_value('state', ''), 'id="state" class="formelement"');
	  $output['select:billingState'] = @display_states('billingState', set_value('billingState', ''), 'id="billingState" class="formelement"');
	  
	  $output['select:country'] = @display_countries('country', $this->site->config['siteCountry'], 'id="country" class="formelement"');
	  $output['select:billingCountry'] = @display_countries('billingCountry', $this->site->config['siteCountry'], 'id="billingCountry" class="formelement"');

      // display with cms layer
      $this->pages->view('shop_login', $output, TRUE);
	}
    
//	function login($redirect = '')
//	{
//		// get partials
//		$output = $this->partials;
//		
//		$sessionName = 'session_user';
//		$redirect = ($redirect) ? $redirect: $this->core->encode('/shop/cart');
//
//		if (!$this->session->userdata($sessionName))
//		{
//			// login
//			if ($this->input->post('password'))
//			{	
//				$username = array('field' => 'email', 'label' => 'Email address', 'value' => $this->input->post('email'));
//			
//				// set admin session name, if given
//				if (!$this->auth->login($username, $this->input->post('password'), $sessionName, $this->core->decode($redirect)))
//				{
//					$this->form_validation->set_error($this->auth->error);
//				}
//			}
//
//			// look up email
//			if ($email = $this->input->post('email'))
//			{				
//				// if registered show login form
//				if ($this->shop->lookup_user_by_email($email))
//				{
//					$output['registered'] = TRUE;
//					$output['user:email'] = $email;
//				}
//
//				// else redirect to create account page
//				else
//				{
//					// set flash data for email
//					$this->session->set_flashdata('email', $email);
//					redirect('/shop/create_account');
//				}
//			}
//		}
//		else
//		{
//			redirect($this->core->decode($redirect));
//		}
//
//		// load errors
//		$output['errors'] = (validation_errors()) ? validation_errors() : FALSE;		
//
//		// set page title
//		$output['page:title'] = 'Login to Shop'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
//
//		// display with cms layer
//		$this->pages->view('shop_login', $output, TRUE);
//	}

	function logout()
	{		
		$this->auth->logout();
	}

	function forgotten()
	{
		// get partials
		$output = $this->partials;

		// load email lib
		$this->load->library('email');	

		// get image errors if there are any
		if(count($_POST))
		{
          
          $this->form_validation->set_rules('email', 'email', 'required|valid_email');
          
          if($this->form_validation->run())
          {
            
            $email = $this->input->post('email');
            
            // check user exists and send email
			if($user = $this->shop->get_user_by_email($email))
			{
              
              // set key
              $key = md5($user['userID'].time());
              $this->shop->set_reset_key($user['userID'], $key);

              // set header and footer
              $emailHeader = str_replace('{name}', trim($user['firstName'].' '.$user['lastName']), $this->site->config['emailHeader']);
              $emailHeader = str_replace('{first-name}', $user['firstName'], $emailHeader);
              $emailHeader = str_replace('{last-name}', $user['lastName'], $emailHeader);
              $emailHeader = str_replace('{email}', $user['email'], $emailHeader);
              $emailFooter = str_replace('{name}', trim($user['firstName'].' '.$user['lastName']), $this->site->config['emailFooter']);
              $emailFooter = str_replace('{first-name}', $user['firstName'], $emailFooter);
              $emailFooter = str_replace('{last-name}', $user['lastName'], $emailFooter);
              $emailFooter = str_replace('{email}', $user['email'], $emailFooter);
				
              // send email			
              $this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
              $this->email->to($user['email']);			
              $this->email->subject('Password reset request on '.$this->site->config['siteName']);
              $this->email->message($emailHeader."\n\nA password reset request has been submitted on ".$this->site->config['siteName'].". If you did not request to have your password reset please ignore this email.\n\nIf you did want to reset your password please click on the link below.\n\n".site_url('shop/reset/'.$key)."\n\n".$emailFooter);
              $this->email->send();

              $output['message'] = '<p>Thank you. An email was sent out with instructions on how to reset your password.</p>';
			}
			else
			{
              $this->form_validation->set_error('There was a problem finding that email on our database, please contact support.');
			}
          }
          
            
			
		}

		// set title
		$output['page:title']   = 'Forgotten Password'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
		$output['page:heading'] = 'Forgotten Password';
        $output['errors']       = (validation_errors() ? validation_errors() : '');
		
		// display with cms layer
		$this->pages->view('shop_forgotten', $output, 'shop');
	}

	function reset($key = '')
	{
		// get partials
		$output = $this->partials;

		// load email lib
		$this->load->library('email');	

		// required
		$this->core->required = array(
			'password' => array('label' => 'Password', 'rules' => 'required|matches[confirmPassword]'),
			'confirmPassword' => array('label' => 'Confirm Password', 'rules' => 'required'),
		);

		// check user exists and send email
		if (!$user = $this->shop->check_key($key))
		{
			show_error('That key was invalid, please contact support.');
		}
		else
		{
			// set object ID
			$objectID = array('userID' => $user['userID']);		
	
			// get values
			$data = $this->core->get_values('users', $objectID);
	
			if (count($_POST))
			{
				// unset key
				$this->core->set['resetkey'] = '';

				// security check
				if ($this->input->post('username')) $this->core->set['username'] = $data['username'];
				if ($this->input->post('premium')) $this->core->set['premium'] = $data['premium'];
				if ($this->input->post('siteID')) $this->core->set['siteID'] = $this->siteID;
				if ($this->input->post('userID')) $this->core->set['userID'] = $data['userID'];
				if ($this->input->post('resellerID')) $this->core->set['resellerID'] = $data['resellerID'];
				if ($this->input->post('kudos')) $this->core->set['kudos'] = $data['kudos'];
				if ($this->input->post('posts')) $this->core->set['posts'] = $data['posts'];

				// update			
				if ($this->core->update('users', $objectID))
				{
					// set header and footer
					$emailHeader = str_replace('{name}', trim($user['firstName'].' '.$user['lastName']), $this->site->config['emailHeader']);
					$emailHeader = str_replace('{first-name}', $user['firstName'], $emailHeader);
					$emailHeader = str_replace('{last-name}', $user['lastName'], $emailHeader);
					$emailHeader = str_replace('{email}', $user['email'], $emailHeader);
					$emailFooter = str_replace('{name}', trim($user['firstName'].' '.$user['lastName']), $this->site->config['emailFooter']);
					$emailFooter = str_replace('{first-name}', $user['firstName'], $emailFooter);
					$emailFooter = str_replace('{last-name}', $user['lastName'], $emailFooter);
					$emailFooter = str_replace('{email}', $user['email'], $emailFooter);
										
					// send email			
					$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
					$this->email->to($user['email']);			
					$this->email->subject('Your password was reset on '.$this->site->config['siteName']);
					$this->email->message($emailHeader."\n\nYour password for ".$this->site->config['siteName']." has been reset, please keep this information safe.\n\nYour new password is: ".$this->input->post('password')."\n\n".$emailFooter);
					$this->email->send();
					
					$output['message'] = 'Thank you. Your password was reset.';
				}
			}

			// load errors
			$output['errors'] = (validation_errors()) ? validation_errors() : FALSE;			
		}
	
	
		// set title
		$output['page:title'] = 'Reset Password'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
		$output['page:heading'] = 'Reset Password';
		
		// display with cms layer
		$this->pages->view('shop_reset', $output, 'shop');		
	}

	function recommend($productID)
	{
		// get partials
		$output = $this->partials;
		
		// make sure toUserID is set
		if (!$product = $this->shop->get_product($productID))
		{
			show_error('Not a valid product!');
		}

		// required
		$this->core->required = array(
			'fullName' => array('label' => 'Full Name', 'rules' => 'required|trim|ucfirst'),
			'email' => array('label' => 'Email', 'rules' => 'required|valid_email'),
			'toName' => array('label' => 'To Name', 'rules' => 'required|trim|ucfirst'),
			'toEmail' => array('label' => 'To Email', 'rules' => 'required|valid_email')
		);

		// get values
		$output = $this->core->get_values();	
		
		if (count($_POST))
		{	
			if ($this->core->check_errors())
			{
				// set header and footer
				$emailHeader = str_replace('{name}', $this->input->post('fullName'), $this->site->config['emailHeader']);
				$emailHeader = str_replace('{email}', $this->input->post('email'), $emailHeader);
				$emailFooter = str_replace('{name}', $this->input->post('toName'), $this->site->config['emailFooter']);
				$emailFooter = str_replace('{email}', $this->input->post('toEmail'), $emailFooter);
									
				// send email
				$this->load->library('email');
				$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
				$this->email->to($this->input->post('toEmail'));			
				$this->email->subject('A Friend Has Recommended a Product on '.$this->site->config['siteName']);
				$this->email->message($emailHeader."\n\nA friend thinks that you might be interested in a product on ".$this->site->config['siteName'].".\n\nYou can view the product by clicking on the link below:\n\n".site_url('shop/'.$productID.'/'.strtolower(url_title($product['productName']))).(($this->input->post('messages')) ? "They sent you a message as well:\n\n".$this->input->post('message') : '')."\n\n".$emailFooter);
				$this->email->send();

				// set success message
				$this->session->set_flashdata('success', 'Thank you, your recommendation has been sent.');
	
				// redirect
				redirect('shop/'.$productID.'/'.strtolower(url_title($product['productName'])));
			}
		}

		// populate template
		$output['product:id'] = $product['productID'];		
		$output['form:name'] = $this->input->post('fullName');
		$output['form:email'] = $this->input->post('email');		
		$output['form:to-name'] = $this->input->post('toName');
		$output['form:to-email'] = $this->input->post('toEmail');		
		$output['form:message'] = $this->input->post('message');	

		// set title
		$output['page:title'] = 'Recommend Product'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

		// load errors
		$output['errors'] = (validation_errors()) ? validation_errors() : FALSE;

		// load content into a popup
		if ($this->core->is_ajax())
		{
			// display with cms layer	
			$this->pages->view('shop_recommend_popup', $output, TRUE);
		}
		else
		{
			// display with cms layer	
			$this->pages->view('shop_recommend', $output, TRUE);
		}
	}
	
	function review($productID)
	{
		// get partials
		$output = $this->partials;
		
		// make sure toUserID is set
		if (!$product = $this->shop->get_product($productID))
		{
			show_error('Not a valid product!');
		}

		// required
		$this->core->required = array(
			'fullName' => array('label' => 'Full Name', 'rules' => 'required|trim|ucfirst'),
			'email' => array('label' => 'Email', 'rules' => 'required|valid_email'),
			'review' => 'Review'
		);

		// get values
		$output = $this->core->get_values();	
		
		// add review
		if (count($_POST))
		{
			// set date
			$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
			$this->core->set['productID'] = $productID;
			$this->core->set['active'] = 0;
			
			// update
			if ($this->core->update('shop_reviews'))
			{
				// get insertID
				$reviewID = $this->db->insert_id();

				// get details on product owner
				if (!$user = $this->shop->get_user($product['userID']))
				{
					$user['email'] = $this->site->config['siteEmail'];
				}
				
				if ($user['notifications'])
				{
					// set header and footer
					$emailHeader = str_replace('{name}', trim($user['firstName'].' '.$user['lastName']), $this->site->config['emailHeader']);
					$emailHeader = str_replace('{first-name}', $user['firstName'], $emailHeader);
					$emailHeader = str_replace('{last-name}', $user['lastName'], $emailHeader);
					$emailHeader = str_replace('{email}', $user['email'], $emailHeader);
					$emailFooter = str_replace('{name}', trim($user['firstName'].' '.$user['lastName']), $this->site->config['emailFooter']);
					$emailFooter = str_replace('{first-name}', $user['firstName'], $emailFooter);
					$emailFooter = str_replace('{last-name}', $user['lastName'], $emailFooter);
					$emailFooter = str_replace('{email}', $user['email'], $emailFooter);
					
					// send email
					$this->load->library('email');						
					$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
					$this->email->to($user['email']);			
					$this->email->subject('New Product Review on '.$this->site->config['siteName']);
					$this->email->message($emailHeader."\n\nSomeone has just reviewed your product titled \"".$product['productName']."\".\n\nYou can view and approve this review by clicking on the following URL:\n\n".site_url('/admin/shop/reviews')."\n\nThey said:\n\"".$this->input->post('review')."\"\n\n".$emailFooter);
					$this->email->send();
				}

				// set success message
				$this->session->set_flashdata('success', 'Thank you, your review has been submitted and is pending approval.');
			
				// redirect
				redirect('/shop/'.$productID.'/'.strtolower(url_title($product['productName'])));
			}
		}

		// populate template
		$output['product:id'] = $product['productID'];		
		$output['form:name'] = $this->input->post('fullName');
		$output['form:email'] = $this->input->post('email');		
		$output['form:review'] = $this->input->post('review');	

		// set title
		$output['page:title'] = 'Review Product'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

		// load errors
		$output['errors'] = (validation_errors()) ? validation_errors() : FALSE;

		// load content into a popup
		if ($this->core->is_ajax())
		{
			// display with cms layer	
			$this->pages->view('shop_review_popup', $output, TRUE);
		}
		else
		{
			// display with cms layer	
			$this->pages->view('shop_review', $output, TRUE);
		}
	}
	
	function cancel()
	{
		// get partials
		$output = $this->partials;
		
		// cancel transaction and empty cart
		$this->session->unset_userdata('cart');
		$this->session->unset_userdata('cart_ids');
		$this->session->unset_userdata('postage');
		$this->session->unset_userdata('total');

		// set page title
		$output['page:title'] = 'Cancelled'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

		// display with cms layer	
		$this->pages->view('shop_cancel', $output, TRUE);
	}
	
	function success($paypalstuff = '')
	{
		// get partials
		$output = $this->partials;
		
		// empty cart
		$this->session->unset_userdata('cart');
		$this->session->unset_userdata('cart_ids');
		$this->session->unset_userdata('postage');
		$this->session->unset_userdata('total');
		
		//unset discount code and mark GC as used
		$discount_code = $this->session->userdata('discountCode');
		if(!empty($discount_code))
		{
			$this->db->update('ha_gc_sent', array('status'=>1), array('code'=>$discount_code));
			$this->session->unset_userdata('discountCode');
		}
		
		// show success page
		$output['ipn'] = $_POST;

		// set page title
		$output['page:title'] = 'Thank You'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
	
		// display with cms layer	
		$this->pages->view('shop_success', $output, TRUE);
	}

	function donation($paypalstuff = '')
	{
		// get partials
		$output = $this->partials;
				
		// set page title
		$output['page:title'] = 'Donation'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
	
		// display with cms layer	
		$this->pages->view('shop_donation', $output, TRUE);
	}

	function invoice($type, $id)
	{
		// get partials
		$output = $this->partials;
		
		// check url
		if ($type == 'subscription')
		{
			if ($data = $this->shop->get_sub_payment($id))
			{
				if ($data['email'] == $this->session->userdata('email') || $this->session->userdata('session_admin'))
				{
					// load libs etc
					$this->load->plugin('pdf');
							
					// populate data
					$data['ref'] = 'I-'.date('Y', strtotime($data['dateCreated'])).$id;
			
					// get invoice template
					$html = $this->load->view('invoice', $data, TRUE);
			
					// create pdf
					create_pdf($html, 'I-'.date('Y').$id);
				}
				else
				{
					show_error('You do not have permission to view this invoice.');
				}
			}
			else
			{
				show_error('Not a valid invoice.');
			}
		}
		else
		{
			show_404();
		}
	}
	
	function ipn()
	{
		// handle Paypal IPN post
		if ($this->shop->validate_ipn())
		{
			if ($this->shop->validate_payment())
			{
				if (substr($this->shop->response_data['orderID'], 0, 3) == 'ORD')
				{
					// send order email
					$this->_create_order();
				}
				elseif (substr($this->shop->response_data['orderID'], 0, 3) == 'DON')
				{
					// send donation email
					$this->_donation();
				}
				elseif (substr($this->shop->response_data['orderID'], 0, 3) == 'INV')
				{
					// update invoice payment
					$this->shop->pay_invoice($this->shop->response_data['transactionID']);
				}
			}
			elseif ($this->shop->validate_subscription())
			{
				// add subscription
				$this->shop->add_subscriber();

				// send subscription email
				$this->_subscription();
			}
			elseif ($this->shop->validate_sub_payment())
			{
				// update subscription
				$this->shop->update_subscriber();
			}
		}
	}

	function response()
	{
		// handle RBS Worldpay post
		if ($this->site->config['shopGateway'] == 'rbsworldpay')
		{
			if ($this->shop->validate_rbsworldpay())
			{
				if ($this->shop->validate_payment())
				{
					if (substr($this->shop->response_data['orderID'], 0, 3) == 'ORD')
					{
						$this->_create_order();
					}
					elseif (substr($this->shop->response_data['orderID'], 0, 3) == 'DON')
					{
						// send donation email
						$this->_donation();
					}					
				}	
				elseif ($this->shop->validate_subscription())
				{
					// HALOGY ADD SITE
					if ($this->siteID == 1 && $this->shop->response_data['desc'] == 'Halogy Premium')
					{
						$this->_halogy_premium();
					}
	
					// add subscription
					$this->shop->add_subscriber();
				}
				elseif ($this->shop->validate_sub_payment())
				{
					// update subscription
					$this->shop->update_subscriber();
				}
			}
		}

		// handle SagePay post
		elseif ($this->site->config['shopGateway'] == 'sagepay')
		{
			if ($this->shop->validate_sagepay())
			{
				if ($this->shop->validate_payment())
				{
					if (substr($this->shop->response_data['orderID'], 0, 3) == 'ORD')
					{
						$this->_create_order();
						$this->output->set_output("Status=OK\nRedirectURL=".site_url('/shop/success')."\nStatusDetail=Successful\n");
					}
					elseif (substr($this->shop->response_data['orderID'], 0, 3) == 'DON')
					{	
						$this->output->set_output("Status=OK\nRedirectURL=".site_url('/shop/donation')."\nStatusDetail=Successful\n");
					}
				}
				else
				{
					$this->output->set_output("Status=OK\nRedirectURL=".site_url('/shop/cancel')."\n");
				}
			}
			else
			{
				$this->output->set_output("Status=OK\nRedirectURL=".site_url('/shop/cancel')."\n");
			}
		}
	}

	function _create_order($orderID = '')
	{
		// get order ID
		$orderID = ($orderID) ? $orderID : $this->shop->response_data['orderID'];

		// get order details
		$orderRow = $this->shop->get_order_by_order_id($orderID);
		$transactionID = $orderRow['transactionID'];		

		// get ordered products
		$itemOrders    = $this->shop->get_item_orders($transactionID);

		// set header and footer
		$emailHeader = str_replace('{name}', trim($orderRow['firstName'].' '.$orderRow['lastName']), $this->site->config['emailHeader']);
		$emailHeader = str_replace('{first-name}', $orderRow['firstName'], $emailHeader);
		$emailHeader = str_replace('{last-name}', $orderRow['lastName'], $emailHeader);
		$emailHeader = str_replace('{email}', $orderRow['email'], $emailHeader);
		$emailFooter = str_replace('{name}', trim($orderRow['firstName'].' '.$orderRow['lastName']), $this->site->config['emailFooter']);
		$emailFooter = str_replace('{first-name}', $orderRow['firstName'], $emailFooter);
		$emailFooter = str_replace('{last-name}', $orderRow['lastName'], $emailFooter);
		$emailFooter = str_replace('{email}', $orderRow['email'], $emailFooter);
		$emailOrder = str_replace('{name}', trim($orderRow['firstName'].' '.$orderRow['lastName']), $this->site->config['emailOrder']);
		$emailOrder = str_replace('{first-name}', $orderRow['firstName'], $emailOrder);
		$emailOrder = str_replace('{last-name}', $orderRow['lastName'], $emailOrder);
		$emailOrder = str_replace('{email}', $orderRow['email'], $emailOrder);
		
		// construct email to customer
		$userBody = $emailHeader."\n\n";
		$userBody .= $emailOrder."\n\n";
		$userBody .= "------------------------------------------\n";

		// construct email to admin
		$adminBody = "Dear administrator,\n\n";
		$adminBody .= "An order (#".$orderID.") has been placed on ".$this->site->config['siteName'].".\n\n";
		$adminBody .= "------------------------------------------\n";
	
		// grab order and make body
		$orderBody  = "Your order:\n\n";
		$orderBody .= "Reference ID #: ".$orderID."\n\n";

		// go through each order
		$downloadBody = '';
		foreach ($itemOrders as $order)
		{	
			// if stock control is enabled then minus the amount of stock
			if ($this->site->config['shopStockControl'])
			{
				$this->shop->minus_stock($order['productID'], $order['quantity']);
			}
		
			$variationHTML = '';
			
			$price = ($order['sale_price']!='0.00') ? $order['sale_price'] : $order['price'];
			// get variation 1
			if ($order['variation1']) $variationHTML .= ' ('.$this->site->config['shopVariation1'].': '.$order['variation1'].')';
			
			// get variations 2
			if ($order['variation2']) $variationHTML .= ' ('.$this->site->config['shopVariation2'].': '.$order['variation2'].')';
		
			// get variations 3
			if ($order['variation3']) $variationHTML .= ' ('.$this->site->config['shopVariation3'].': '.$order['variation3'].')';

			// check if its a file
			if ($order['fileID'])
			{
				$file = $this->shop->get_file($order['fileID']);
				$downloadBody .= $order['productName']."\n".site_url('/files/'.$this->core->encode($file['fileRef'].'|'.$transactionID))."\n\n";
			}
		
			$orderBody .= $order['quantity'] . "x | #" . $order['catalogueID'] . " | " . $order['productName'] . $variationHTML . " ";
			$orderBody .= "| ".currency_symbol(FALSE). number_format(($price * $order['quantity']),2)."\n";
		}

		// show tax if exists
		if ($orderRow['discounts'] > 0)
		{
			$orderBody .= "\nDiscounts: (".currency_symbol(FALSE).number_format($orderRow['discounts'],2).")";
		}

		// check for donations
		if ($orderRow['donation'] > 0)
		{
			$orderBody .= "\nDonation: ".currency_symbol(FALSE).number_format($orderRow['donation'],2);
		}

		// show subtotals
		$orderBody .= "\nSub total: ".currency_symbol(FALSE).number_format(($orderRow['amount'] - $orderRow['postage'] - $orderRow['tax']),2);
		$orderBody .= "\nShipping: ".currency_symbol(FALSE).number_format($orderRow['postage'],2);
		
		// show tax if exists
		if ($orderRow['tax'] > 0)
		{
			$orderBody .= "\nTax: ".currency_symbol(FALSE).number_format($orderRow['tax'],2);
		}
		
		// show totals
		$orderBody .= "\nTotal: ".currency_symbol(FALSE).number_format($orderRow['amount'],2)."\n\n";
		$orderBody .= "------------------------------------------\n\n";

		// show download links
		if (strlen($downloadBody) > 0)
		{
			$orderBody .= "Download Links:\n\n";
			$orderBody .= $downloadBody;
			$orderBody .= "------------------------------------------\n\n";
		}
		
		$dispatchBody = "Shipping Address:\n\n";
		$dispatchBody .= ($orderRow['firstName'] && $orderRow['lastName']) ? $orderRow['firstName']." ".$orderRow['lastName']."\n" : '';
		$dispatchBody .= ($orderRow['address1']) ? $orderRow['address1']."\n" : '';
		$dispatchBody .= ($orderRow['address2']) ? $orderRow['address2']."\n" : '';
		$dispatchBody .= ($orderRow['address3']) ? $orderRow['address3']."\n" : '';
		$dispatchBody .= ($orderRow['city']) ? $orderRow['city']."\n" : '';
		$dispatchBody .= ($orderRow['state']) ? lookup_state($orderRow['state'])."\n" : '';
		$dispatchBody .= ($orderRow['postcode']) ? $orderRow['postcode']."\n" : '';
		$dispatchBody .= ($orderRow['country']) ? lookup_country($orderRow['country'])."\n" : '';
		$dispatchBody .= ($orderRow['phone']) ? $orderRow['phone']."\n" : '';
		$dispatchBody .= $orderRow['email']."\n";
		$dispatchBody .= "------------------------------------------\n\n";

		// show billing address if set
		if ($orderRow['billingAddress1'] || $orderRow['billingAddress2'] || $orderRow['billingCity'] || $orderRow['billingPostcode'])
		{
			$dispatchBody .= "Billing Address:\n\n";
			$dispatchBody .= ($orderRow['firstName'] && $orderRow['lastName']) ? $orderRow['firstName']." ".$orderRow['lastName']."\n" : '';
			$dispatchBody .= ($orderRow['billingAddress1']) ? $orderRow['billingAddress1']."\n" : '';
			$dispatchBody .= ($orderRow['billingAddress2']) ? $orderRow['billingAddress2']."\n" : '';
			$dispatchBody .= ($orderRow['billingAddress3']) ? $orderRow['billingAddress3']."\n" : '';
			$dispatchBody .= ($orderRow['billingCity']) ? $orderRow['billingCity']."\n" : '';
			$dispatchBody .= ($orderRow['billingState']) ? lookup_state($orderRow['billingState'])."\n" : '';
			$dispatchBody .= ($orderRow['billingPostcode']) ? $orderRow['billingPostcode']."\n" : '';
			$dispatchBody .= ($orderRow['billingCountry']) ? lookup_country($orderRow['billingCountry'])."\n" : '';
			$dispatchBody .= "------------------------------------------\n\n";
		}

		// add notes
		$notesBody = ($orderRow['notes']) ? "Notes:\n\n".$orderRow['notes']."\n\n------------------------------------------\n\n" : '';
			
		$footerBody = $emailFooter;

		$this->shop->update_order($transactionID);
        log_message('error', $orderBody);
		// load email lib and email user and admin
		$this->load->library('email');

		$this->email->to($orderRow['email']);
		$this->email->subject('Thank you for your order (#'.$orderID.')');
		$this->email->message($userBody.$orderBody.$dispatchBody.$notesBody.$footerBody);
		$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);			
		$this->email->send();

		$this->email->clear();

		$this->email->to($this->site->config['siteEmail']);
		$this->email->subject('Someone has placed an order (#'.$orderID.')');
		$this->email->message($adminBody.$orderBody.$dispatchBody.$notesBody.$footerBody);
		$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);			
		$this->email->send();

		return TRUE;
	}

	function _donation()
	{
		$orderID = $this->shop->response_data['orderID'];
		
		// set header and footer
		$emailHeader = str_replace('{name}', $this->shop->response_data['fullName'], $this->site->config['emailHeader']);
		$emailHeader = str_replace('{first-name}', $this->shop->response_data['firstName'], $emailHeader);
		$emailHeader = str_replace('{last-name}', $this->shop->response_data['lastName'], $emailHeader);
		$emailHeader = str_replace('{email}', $this->shop->response_data['email'], $emailHeader);
		$emailFooter = str_replace('{name}', $this->shop->response_data['fullName'], $this->site->config['emailFooter']);
		$emailFooter = str_replace('{first-name}', $this->shop->response_data['firstName'], $emailFooter);
		$emailFooter = str_replace('{last-name}', $this->shop->response_data['lastName'], $emailFooter);
		$emailFooter = str_replace('{email}', $this->shop->response_data['email'], $emailFooter);
		$emailDonation = str_replace('{name}', $this->shop->response_data['fullName'], $this->site->config['emailDonation']);
		$emailDonation = str_replace('{first-name}', $this->shop->response_data['firstName'], $emailDonation);
		$emailDonation = str_replace('{last-name}', $this->shop->response_data['lastName'], $emailDonation);
		$emailDonation = str_replace('{email}', $this->shop->response_data['email'], $emailDonation);
		
		// construct email to customer
		$userBody = $emailHeader."\n\n";
		$userBody .= $emailDonation."\n\n";
		$footerBody = $emailFooter;

		// construct email to admin
		$adminBody = "Dear administrator,\n\n";
		$adminBody .= "Someone has made a donation on ".$this->site->config['siteName'].".\n\nThe donation reference is: #".$orderID.".\n\nYou will need to log in to your payment gateway to find out how much they gave.\n\n";

		// load email lib and email user and admin
		$this->load->library('email');

		//$this->email->to($this->shop->response_data['email']);
		//$this->email->subject('Thank you for your donation');
		//$this->email->message($userBody.$footerBody);
		//$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);			
		//$this->email->send();

		//$this->email->clear();

		$this->email->to($this->site->config['siteEmail']);
		$this->email->subject('Someone has made a donation (#'.$orderID.')');
		$this->email->message($adminBody.$footerBody);
		$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);			
		$this->email->send();

		return TRUE;
	}

	function _subscription()
	{
		// get order ID
		$orderID = $this->shop->response_data['orderID'];
		$email = $this->shop->response_data['email'];

		// set header and footer
		$emailHeader = str_replace('{name}', $this->shop->response_data['fullName'], $this->site->config['emailHeader']);
		$emailHeader = str_replace('{first-name}', $this->shop->response_data['firstName'], $emailHeader);
		$emailHeader = str_replace('{last-name}', $this->shop->response_data['lastName'], $emailHeader);
		$emailHeader = str_replace('{email}', $this->shop->response_data['email'], $emailHeader);
		$emailFooter = str_replace('{name}', $this->shop->response_data['fullName'], $this->site->config['emailFooter']);
		$emailFooter = str_replace('{first-name}', $this->shop->response_data['firstName'], $emailFooter);
		$emailFooter = str_replace('{last-name}', $this->shop->response_data['lastName'], $emailFooter);
		$emailFooter = str_replace('{email}', $this->shop->response_data['email'], $emailFooter);
		$emailSubscription = str_replace('{name}', $this->shop->response_data['fullName'], $this->site->config['emailSubscription']);
		$emailSubscription = str_replace('{first-name}', $this->shop->response_data['firstName'], $emailSubscription);
		$emailSubscription = str_replace('{last-name}', $this->shop->response_data['lastName'], $emailSubscription);
		$emailSubscription = str_replace('{email}', $this->shop->response_data['email'], $emailSubscription);
		
		// construct email to customer
		$userBody = $emailHeader."\n\n";
		$userBody .= $emailSubscription."\n\n";

		// construct email to admin
		$adminBody = "Dear administrator,\n\n";
		$adminBody .= "A subscription has been created on ".$this->site->config['siteName'].".\n\n";
	
		// grab order and make body
		$orderBody = "Your subscription reference ID #: ".$orderID."\n\n";

		// get footer			
		$footerBody = $emailFooter;

		// get subscriptionID
		$subscriptionID = substr($this->shop->response_data['item_number'], (strpos($this->shop->response_data['item_number'], '-')+1));

		// get subscription
		$subscription = $this->shop->get_subscription($subscriptionID);

		// get subscription
		$plan = ($subscription) ? $subscription['plan'] : '';

		// perform action
		$this->shop->upgrade_user($this->shop->response_data['custom'], $plan);

		// load email lib and email user and admin
		$this->load->library('email');

		$this->email->to($email);
		$this->email->subject('New subscription set up on '.$this->site->config['siteName']);
		$this->email->message($userBody.$orderBody.$footerBody);
		$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);			
		$this->email->send();

		$this->email->clear();

		$this->email->to($this->site->config['siteEmail']);
		$this->email->subject('New subscription set up on '.$this->site->config['siteName']);
		$this->email->message($adminBody.$orderBody.$footerBody);
		$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);			
		$this->email->send();

		return TRUE;
	}

	function _populate_products($products)
	{
      if($products && is_array($products))
      {
        $itemsPerRow = $this->shop->siteVars['shopItemsPerRow'];
        $i = 0;
        $x = 0;
        $t = 0;
		
		$this->load->library('image_lib');
		
		foreach($products as $product)
        {
          // get body and excerpt
          $productBody = (strlen($this->_strip_markdown($product['description'])) > 100) ? substr($this->_strip_markdown($product['description']), 0, 100).'...' : nl2br($this->_strip_markdown($product['description']));
          $productExcerpt = nl2br($this->_strip_markdown($product['excerpt']));

          // get images
          if(!$image = $this->uploads->load_image($product['productID'], false, true))
          {
            $image['src'] = $this->config->item('staticPath').'/images/nopicture.jpg';
          }
          if(!$thumb = $this->uploads->load_image($product['productID'], true, true))
          {
            $thumb['src'] = $this->config->item('staticPath').'/images/nopicture.jpg';
          }
          
          // check for sales price
          if($product['sale_price'] != '0.00')
          {
            $price = number_format($product['sale_price'], 2);
          }
          else
          {
            $price = number_format($product['price'], 2);
          }
		  $price = str_replace('.00', '', $price);
          
			$var1 = $this->shop->get_variations($product['productID'], 1);
			$var2 = $this->shop->get_variations($product['productID'], 2);
			$var3 = $this->shop->get_variations($product['productID'], 3);
			
			$var1html = "";
			if(!empty($var1))
			{
				foreach($var1 as $var1x)
				{
					$varbackorder = ($var1x['backorder']==1) ? "(Backorder)" : "";
					$varname = ($var1x['price'] > 0) ? $var1x['variation']." (+".$var1x['price'].")" : $var1x['variation'];
					$var1html .= "<option value='".$var1x['variationID']."'>".$varname." ".$varbackorder."</option>";
				}
			}
			
			$var2html = "";
			if(!empty($var2))
			{
				foreach($var2 as $var2x)
				{
					$varbackorder = ($var2x['backorder']==1) ? "(Backorder)" : "";
					$varname = ($var2x['price'] > 0) ? $var2x['variation']." (+".$var2x['price'].")" : $var2x['variation'];
					$var2html .= "<option value='".$var2x['variationID']."'>".$varname." ".$varbackorder."</option>";
				}
			}
			
			$var3html = "";
			if(!empty($var3))
			{
				foreach($var3 as $var3x)
				{
					$varbackorder = ($var3x['backorder']==1) ? "(Backorder)" : "";
					$varname = ($var3x['price'] > 0) ? $var3x['variation']." (+".$var3x['price'].")" : $var3x['variation'];
					$var3html .= "<option value='".$var3x['variationID']."'>".$varname." ".$varbackorder."</option>";
				}
			}
			
			
			$img = explode('.', $image['src']);
			$source_img = BASEPATH.'../..'.$image['src'];
			$browse_img = BASEPATH.'../..'.$img[0]."_browse.".$img[1];
			$browse_img_url = $img[0]."_browse.".$img[1];
			
			if(!file_exists($browse_img))
			{
				//exact_resize_image($source_img, $browse_img, 220, 290, 75, true);
			}
		  
          // populate template array
          $data[$x] = array(
              'product:id'         => $product['productID'],
              'product:link'       => site_url('/shop/'.$product['productID'].'/'.strtolower(url_title($product['productName']))),
              'product:title'      => $product['productName'],
              'product:tooltip'    => $product['productName'],
              'product:subtitle'   => $product['subtitle'],
              'product:body'       => $productBody,
              'product:excerpt'    => $productExcerpt,
              'product:image-path' => site_url('resize.php?src='.site_url($image['src']).'&amp;w=400&amp;s=0'),
              'product:thumb-path' => site_url('resize.php?src='.site_url($image['src']).'&amp;w=220&amp;h=290&amp;s=0'),
			  //'product:thumb-path' => site_url($browse_img_url),
              'product:cell-width' => floor(( 1 / $itemsPerRow) * 100),
              'product:price'      => currency_symbol().$price,
              'product:stock'      => $product['stock'],
              'product:is_sale'    => ($product['sale_price'] != '0.00' ? '<span class="is-sale"></span>' : ''),
              'product:is_deal'		=> ($product['deal'] == 'Y' ? '<span class="is-deal"></span>' : ''),
			  'variation1'			=> $var1html,
			  'variation2'			=> $var2html,
			  'variation3'			=> $var3html
          );
		  
		  if($product['sale_price'] != '0.00' && $product['deal'] == 'Y')
		  {
		  	$data[$x]['product:is_sale'] = '';
	  	  }

            // get tags
            if ($product['tags'])
            {
                $tags = explode(',', $product['tags']);

                $t = 0;
                foreach ($tags as $tag)
                {
                    $data[$x]['product:tags'][$t]['tag:link'] = site_url('shop/tag/'.$this->tags->make_safe_tag($tag));
                    $data[$x]['product:tags'][$t]['tag'] = $tag;

                    $t++;
                }
            }				

            if (($i % $itemsPerRow) == 0 && $i > 1)
            {
                $data[$x]['product:rowpad'] = '</tr><tr>'."\n";
                $i = 0;
            }
            else
            {
                $data[$x]['product:rowpad'] = '';
            }

            $i++;
            $x++;
        }

			return $data;
		}
		else
		{
			return FALSE;
		}
	}
	
	function _strip_markdown($string)
	{
		return preg_replace('/([*\-#]+)/i', '', preg_replace('/{(.*)}/i', '', $string));
	}
    
    function list_categories()
    {
      $list_categories = $this->shop->get_categories();
      
      $cat_arr = array();
      $cat_arr[''] = '-- Select Category --';
      foreach($list_categories as $cat)
      {
        $cat_arr[$cat['catSafe']] = $cat['catName'];
      }
      
      return $cat_arr;
    }
    
    function prod_sizes($val='')
    {
      $sizes_arr = array(
          ''            => '-- Select Sizes --',
		  'xsmall'      => 'X-Small',
          'small'       => 'Small',
          'medium'      => 'Medium',
          'large'       => 'Large',
		  'xlarge'		=> 'X-Large',
		  'xxlarge'		=> '2X-Large'
      );
      
      if($val != '')
      {
        return $sizes_arr[$val];
      }
      
      return $sizes_arr;
    }
    
    
    function addtowishlist()
    {
            
      $redirect = site_url('/shop/'.$this->uri->segment('2').'/'.$this->uri->segment('3'));
            
      $data = array(
          'userID'    => $this->session->userdata('userID'),
          'productID' => $this->uri->segment('2'),
          'siteID'    => 1
      );
      
      if($this->db->insert('shop_wishlist', $data))
      {
        $this->session->set_flashdata('success', 'Successfully added this product to your wishlist. ');
        redirect($redirect);
      }
      
    }
    
    function removefromwishlist()
    {
      $redirect = site_url('/shop/'.$this->uri->segment('2').'/'.$this->uri->segment('3'));
      
      $where = array(
          'userID'    => $this->session->userdata('userID'),
          'productID' => $this->uri->segment('2')
      );
      
      if($this->db->delete('shop_wishlist', $where))
      {
        $this->session->set_flashdata('success', 'Successfully remove this product from your wishlist. ');
        redirect($redirect);
      }
    }
    
    function wishlist()
    {
      
      $limit = 20;
      
      // check user is logged in, if not send them away from this controller
      if(!$this->session->userdata('session_user'))
      {
        redirect(site_url('/shop/login/'.$this->core->encode($this->uri->uri_string())));
      }
      
      $users_wishlist = $this->shop->users_wishlist($this->session->userdata('userID'));
      
      $products       = $this->shop->get_wishlist_products($users_wishlist);
      
      $output['shop:products']       = $this->_populate_products($products);
      $output['shop:paging']         = $limit;			
      $output['shop:total-products'] = ($products) ? $this->pagination->total_rows : 0;
      $output['pagination']          = ($pagination = $this->pagination->create_links()) ? $pagination : '';
      
      $this->pages->view('shop_wishlist', $output, TRUE);
      
    }
    
    /*
     * Process Paypal
     */
    
    function paypal()
	{
      
      $uri = $this->uri->segment('3');
      
      
      // Sandbox
//      $PayPalMode 		   = 'AMQPChannel'; // AMQPChannel or live
	  /*$PayPalMode		   = 'sandbox';
      $PayPalApiUsername   = 'rryap9-facilitator_api1.gmail.com'; //PayPal API Username
      $PayPalApiPassword   = '1397549116'; //Paypal API password
      $PayPalApiSignature  = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AeQNlH8nDgMUA4clamj.T8iww.2N'; //Paypal API Signature*/
      
      // Live
      $PayPalMode 		   = 'live'; // sandbox or live
      $PayPalApiUsername   = 'runmother_api1.gmail.com'; //PayPal API Username
      $PayPalApiPassword   = 'Z9QC8B4P8UL6H5RP'; //Paypal API password
      $PayPalApiSignature  = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AHC4KxpPaeyrWXLByRAsDS22S6vP'; //Paypal API Signature
   
      $PayPalCurrencyCode 	= 'USD'; //Paypal Currency Code
      $PayPalReturnURL 		= site_url('/shop/paypal'); //Point to process.php page
      $PayPalCancelURL 		= site_url(); //Cancel URL if user clicks cancel
		
      if($uri == "ac") //Post Data received from product list page.
      {
        
        // Mainly we need 4 variables from an item, 
        // Item Name, Item Price, Item Number 
        // and Item Quantity.

        if($dat = $this->shop->load_cart())
        {
          $amt              = number_format(($dat['subtotal'] + $dat['postage'] + $dat['tax']), 2);
          $trans_total_amt  = number_format(($dat['subtotal'] + $dat['postage'] + $dat['tax']), 2);
          $total_cost_item  = number_format($dat['subtotal'], 2);
          $total_tax        = number_format($dat['tax'], 2);
          $postage          = number_format($dat['postage'], 2);
        }
        else
        {
          $amt              = 0;
          $trans_total_amt  = 0;
          $total_cost_item  = 0;
          $total_tax        = 0;
          $postage          = 0;
        }
        
		$pp_items = ""; $running_price = 0;
		$ItemName = "";
		$x = 0; $qty = 0;
		foreach ($dat['cart'] as $key => $item)
		{
			$qty = $qty + $item['quantity'];
			$price = ($item['sale_price']!='0.00') ? $item['sale_price'] : $item['price'];
			$price = (int)$price;
			$running_price = $running_price + $price;
			$item_price_with_qty = $item['quantity'] * $price;
			$pp_items .= 
			    '&L_PAYMENTREQUEST_0_QTY'.$x.'='.urlencode($item['quantity']).
			    '&L_PAYMENTREQUEST_0_AMT'.$x.'='.urlencode($price).
                '&L_PAYMENTREQUEST_0_NAME'.$x.'='.urlencode($item['productName']." (".$item['variation2'].")");
				'&L_PAYMENTREQUEST_0_DESC'.$x.'='.urlencode($item['productName']." (".$item['variation2'].")");
				'&L_PAYMENTREQUEST_0_NUMBER'.$x.'='.urlencode($item['productID']);
			$x++;
			//pr($item, 1);
		}
		//pr($dat, 1);
		$ItemName = trim($ItemName, ", ");
		//pr($pp_items, 1);
        //$ItemName       = $this->site->config['siteName'] . " Shopping Cart"; //Item Name
		
		// Adding GC Discount
		if(!empty($dat['gc_discounts']))
		{
			if(!empty($pp_items))
			{
				//$trans_total_amt = number_format($trans_total_amt + $dat['gc_discounts'], 2);
				//$total_cost_item = number_format($total_cost_item + $dat['gc_discounts'], 2);
				//$amt = number_format($amt + $dat['gc_discounts'], 2);
				
				//apply discount if total > discount
				if(($trans_total_amt + $dat['gc_discounts']) > $dat['gc_discounts'] && ($amt + $dat['gc_discounts']) > $dat['gc_discounts'])
				{
					$pp_items .= 
					    '&L_PAYMENTREQUEST_0_QTY'.$x.'='.urlencode('1').
					    '&L_PAYMENTREQUEST_0_AMT'.$x.'='.urlencode('-'.$dat['gc_discounts']).
		                '&L_PAYMENTREQUEST_0_NAME'.$x.'='.urlencode("GC Discount");
						'&L_PAYMENTREQUEST_0_DESC'.$x.'='.urlencode("Gift Certificate Discount");
						'&L_PAYMENTREQUEST_0_NUMBER'.$x.'='.urlencode("1");
				}
				//pr($total_cost_item, 1);
			}
		}
		
        //Data to be sent to paypal
        $padata = '&CURRENCYCODE='.urlencode($PayPalCurrencyCode).
                  '&PAYMENTACTION=Sale'.
                  '&ALLOWNOTE=1'.
                  '&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($PayPalCurrencyCode).
                  '&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("Sale").
				  '&PAYMENTREQUEST_0_AMT='.urlencode($trans_total_amt). //Transactions Total Amount
                  '&PAYMENTREQUEST_0_ITEMAMT='.urlencode($total_cost_item). //The total cost of the items in the order, excluding shipping, taxes
                  '&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($postage).
                  '&PAYMENTREQUEST_0_TAXAMT='.urlencode($total_tax).
                
				  //'&L_PAYMENTREQUEST_0_QTY0='.urlencode($ItemQty).
				  //'&L_PAYMENTREQUEST_0_AMT0='.urlencode($total_cost_item).
                  //'&L_PAYMENTREQUEST_0_NAME0='.urlencode($ItemName).
				  
				  $pp_items.
                  //'&AMT='.urlencode($trans_total_amt).
                
                  '&RETURNURL='.urlencode($PayPalReturnURL ).
                  '&CANCELURL='.urlencode($PayPalCancelURL);	

          //We need to execute the "SetExpressCheckOut" method to obtain paypal token
		$httpParsedResponseAr = $this->shop->PPHttpPost('SetExpressCheckout', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
		
		//Respond according to message we receive from Paypal
        if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
        {
          if($PayPalMode == 'sandbox')
          {
            $paypalmode 	= '.sandbox';
          }
          else
          {
            $paypalmode 	= '';
          }
   
          //Redirect user to PayPal store with Token received.
          $paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'&useraction=commit';
          header('Location: '.$paypalurl);

        }
        else
        {
          $pagecontent  = '';
          $pagecontent .= '<h1>Paypal Payment</h1>';
          $pagecontent .= '<div class="error-stay"><p>Error: '.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</p></div>';
        }
      }
		
      //Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
      if($this->input->get('token') && $this->input->get('PayerID'))
      {
        //we will be using these two variables to execute the "DoExpressCheckoutPayment"
        //Note: we haven't received any payment yet.

        $token    = $this->input->get('token');
        $playerid = $this->input->get('PayerID');

        if($dat = $this->shop->load_cart())
        {
			$amt = number_format(($dat['subtotal'] + $dat['postage'] + $dat['tax']), 2);
	  		$x = 0;
			$pp_items = "";
	  		foreach ($dat['cart'] as $key => $item)
	  		{
	  			$price = ($item['sale_price']!='0.00') ? $item['sale_price'] : $item['price'];
	  			$price = (int)$price;
	  			$pp_items .= 
	  			    '&L_PAYMENTREQUEST_0_QTY'.$x.'='.urlencode($item['quantity']).
	  			    '&L_PAYMENTREQUEST_0_AMT'.$x.'='.urlencode($price).
	                '&L_PAYMENTREQUEST_0_NAME'.$x.'='.urlencode($item['productName']." (".$item['variation2'].")");
	  				'&L_PAYMENTREQUEST_0_DESC'.$x.'='.urlencode($item['productName']." (".$item['variation2'].")");
	  				'&L_PAYMENTREQUEST_0_NUMBER'.$x.'='.urlencode($item['productID']);
	  			$x++;
	  			//pr($item, 1);
	  		}
			
			if(!empty($dat['gc_discounts']))
			{
				if(!empty($pp_items))
				{
					//$amt = number_format($amt + $dat['gc_discounts'], 2);
					//$dat['subtotal'] = number_format($dat['subtotal'] + $dat['gc_discounts'], 2);
					//apply discount if total > discount
					if(($dat['subtotal']+$dat['gc_discounts']) > $dat['gc_discounts'] && ($amt+$dat['gc_discounts']) > $dat['gc_discounts'])
					{
						$pp_items .= 
						    '&L_PAYMENTREQUEST_0_QTY'.$x.'='.urlencode('1').
						    '&L_PAYMENTREQUEST_0_AMT'.$x.'='.urlencode('-'.$dat['gc_discounts']).
			                '&L_PAYMENTREQUEST_0_NAME'.$x.'='.urlencode("GC Discount");
							'&L_PAYMENTREQUEST_0_DESC'.$x.'='.urlencode("Gift Certificate Discount");
							'&L_PAYMENTREQUEST_0_NUMBER'.$x.'='.urlencode("1");
					}
				}
			}
        }
        else
        {
          $amt = 0;
		  $pp_items = ""; 
        }

          //get session variables
          $ItemTotalPrice   = $amt;
          $ItemName         = $this->site->config['siteName'] . " Shopping Cart"; //Item Name

          $padata = 	'&TOKEN='.urlencode($token).
                        '&PAYERID='.urlencode($playerid).
                        //'&PAYMENTACTION='.urlencode("SALE").
				          '&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($PayPalCurrencyCode).
				          '&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("Sale").
						  '&PAYMENTREQUEST_0_AMT='.urlencode($amt). //Transactions Total Amount
				          '&PAYMENTREQUEST_0_ITEMAMT='.urlencode($dat['subtotal']). //The total cost of the items in the order, excluding shipping, taxes
				          '&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($dat['postage']).
				          '&PAYMENTREQUEST_0_TAXAMT='.urlencode($dat['tax']).
							  $pp_items;
          //We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.
          //$paypal= new MyPayPal();
             
          $httpParsedResponseAr = $this->shop->PPHttpPost('DoExpressCheckoutPayment', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
		  //pr($httpParsedResponseAr, 1);
          //Check if everything went ok..
          if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
          {
            // page content		
            $pagecontent = '<h2>Paypal Payment</h2>';
            $note_msg    = '<div class="success-stay"><p>Your Transaction ID:'.urldecode($httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]).'</p></div>';

            //$pp_transid = urldecode($httpParsedResponseAr["TRANSACTIONID"]);
			$pp_transid = urldecode($httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]);
            $this->session->set_userdata('paypaltransID', $pp_transid);

            /*
            //Sometimes Payment are kept pending even when transaction is complete. 
            //May be because of Currency change, or user choose to review each payment etc.
            //hence we need to notify user about it and ask him manually approve the transiction
            */

            //if('Completed' == $httpParsedResponseAr["PAYMENTSTATUS"])
			if('Completed' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"])
            {
              
              
              $this->_create_order($this->session->userdata('cartOrderID'));
              
              redirect('shop/pp_success');
            }
            //elseif('Pending' == $httpParsedResponseAr["PAYMENTSTATUS"])
			elseif('Pending' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"])
            {
              $note_msg .= '<div>Transaction Complete, but payment is still pending! You need to manually authorize this payment in your <a target="_new" href="http://www.paypal.com">Paypal Account</a></div>';
            }

            $transactionID = urlencode($httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]);
            $nvpStr        = "&TRANSACTIONID=".$transactionID;
            
            $httpParsedResponseAr = $this->shop->PPHttpPost('GetTransactionDetails', $nvpStr, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);

            if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
            {} 
            else  
            {
              $note_msg .= '<div><b>GetTransactionDetails failed:</b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
            }
          }
          else
          {
            $pagecontent = '<h2>Paypal Payment</h2>';
            $note_msg    = 'Your Transaction ID :'.urldecode($httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]);
            $note_msg   .= '<div><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
          }
      }
		
      // set title
      $output['page:title']       = $this->site->config['siteName'].' | Paypal Payment';
      $output['page:heading']     = 'Paypal Payment';
      $output['page:pagecontent'] = $pagecontent;
		
      // display with cms layer
      $this->pages->view('paypal', $output, TRUE);	
	}
    
    function pp_success()
	{
      $pagecontent = "<h2>Paypal Payment</h2>";

		$approve_msg_add = '
          <p>You should have been sent an email confirming the order details. You will also have received an order number in that email which you can use in any communications that you have with us.</p>
    	  <p>We will email you once your order has been shipped.</p>';

		$approve_msg = " Transaction was Approved. Transaction ID: " . $this->session->userdata('paypaltransID') ." ".$approve_msg_add."";
		
		$pagecontent .= "
						 
						 <span class='approve_msg'>".$approve_msg."</span>
						";
		
		// get partials
        session_start();
		$output = $this->partials;
		
		// empty cart
		$this->session->unset_userdata('cart');
		$this->session->unset_userdata('cart_ids');
		$this->session->unset_userdata('postage');
		$this->session->unset_userdata('total');	
		
		$this->session->unset_userdata('bm_errormsg');	
		$this->session->unset_userdata('paypaltransID');	
		$this->session->unset_userdata('fcheckbox');	
		$this->session->unset_userdata('pickupdirection');	
		$this->session->unset_userdata('ordercomment');	
        $this->session->unset_userdata('fedex_carrier_name');
		
                if(isset($_SESSION['payment'])){
                    unset($_SESSION['payment']);
                }
                
		// show success page
		$output['ipn'] = $_POST;

		// set page title
		$output['page:title'] = 'Thank You'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
		
		$output['page:content'] = $pagecontent;
	
		// display with cms layer	
		$this->pages->view('shop_ppsuccess', $output, TRUE);
	}
    
    function cart_process()
    {
      
      header('Content-Type: application/json');
      
      $productID = $this->input->get('productID');
      $quantity  = $this->input->get('quantity');
	  $var1  = $this->input->get('variation1');
	  $var2  = $this->input->get('variation2');
	  $var3  = $this->input->get('variation3');
      
      $this->shop->add_to_cart($productID, $quantity, $var1, $var2, $var3);
      
      // get added product data
      
      $productData = $this->shop->get_product($productID);
      
      $cart_data = $this->shop->load_cart();
      
      $response_array = array(
          'productName'     => $productData['productName'],
          'productImage'    => '<img src="'.site_url('resize.php?src='.site_url('/static/uploads/'.$productData['image_1']).'&amp;w=32&amp;h=32&amp;s=0').'" />',
          'productPrice'    => '$'.($productData['sale_price'] != '0.00' ? number_format($productData['sale_price'], 2) : number_format($productData['price'], 2)),
          'cartItems'       => count($cart_data['cart']) . ' items in cart',
          'subtotal'        => 'Subtotal: $'.  number_format($cart_data['subtotal'], 2),
		  'quantity'		=> $quantity
      );
      
      echo json_encode($response_array);
    }
    
    
    function gift_card()
    {
        // check user is logged in, if not send them away from this controller
        /*
		if(!$this->session->userdata('session_user'))
        {
          redirect(site_url('/shop/login/'.$this->core->encode($this->uri->uri_string())));
        }
		*/
		
		$output = array();
		$output['sent'] = 0;
		$output['msg'] = "";
		
		$userID = $this->session->userdata('userID');
		$data = $this->core->get_values('users', array('userID'=>$userID));
		
		if(!empty($userID))
			$sender_preset = $data['firstName']." ".$data['lastName'];
		else
			$sender_preset = "";
		
		if(!empty($_POST))
		{
			$this->load->helper('email');
			if(valid_email($_POST['recipient_email']))
			{
				//$discount = $this->db->get_where('ha_shop_discounts', array('discountID'=>$_POST['gift_certificate_id']))->result_array();
				$discount = $this->db->get_where('ha_shop_gift_certificates', array('id'=>$_POST['gift_certificate_id']))->result_array();
				
				if(!empty($userID))
				{
					$sender = $data['firstName']." ".$data['lastName'];
				}
				else
				{
					$sender = $this->input->post('sender_name', true);
					$userID = 0;
				}
				
				$code = strtoupper(substr(md5(time().$_POST['recipient_email']), 0, 8).substr(md5(time().$_POST['recipient_name']), -4));
				$this->db->insert('ha_gc_sent', array('sender_id'=>$userID, 'amount'=>$discount[0]['amount'], 'recipient_email'=>$_POST['recipient_email'], 'code'=>$code, 'recipient_name'=>$_POST['recipient_name'], 'gift_certificate_id'=>$_POST['gift_certificate_id'], 'status'=>0));
				
				$this->load->library('email');

				$this->email->to($_POST['recipient_email']);
				$this->email->subject("You've recieved a Gift Certificate from Another Mother Runner");
				
				$message = str_replace('{sender_name}', trim($sender), $this->site->config['emailGiftcard']);
				//$message = str_replace('{code}', $discount[0]['code'], $message);
				$message = str_replace('{code}', $code, $message);
				$message = str_replace('{recipient_name}', $_POST['recipient_name'], $message);
				$message = str_replace('{sender_message}', $_POST['sender_message'], $message);
				//$message = str_replace('{amount}', "$".number_format($discount[0]['discount'],2), $message);
				$message = str_replace('{amount}', "$".$discount[0]['amount'], $message);
				
				//pr($message, 1);
				
				$this->email->message($message);
			
				$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);			
				$this->email->send();
				
				$output['sent'] = 1;
			}
			else
				$output['msg'] = "Sorry, you entered an invalid email. Please try again";
		}
		
		//$gc = $this->db->get_where('ha_shop_discounts', array('siteID'=>$this->siteID, 'expiryDate >'=>date("Y-m-d H:i:s")))->result_array();
		$gc = $this->db->get_where('ha_shop_gift_certificates', array('siteID'=>$this->siteID))->result_array();
		$gc_options = array();
		foreach($gc as $item)
		{
			//$disc = number_format($item['discount'], 2);
			//$gc_options[$item['discountID']] = $item['code']." ($".$disc.")";
			$gc_options[$item['id']] = $item['name']." ($".$item['amount'].")";
		}
	
		$output['gc_options'] = @form_dropdown('gift_certificate_id', $gc_options, set_value('gift_certificate_id', ''), 'id="gift_certificate_id" class="formelement"');
		$output['recipient_name'] = @form_input('recipient_name',set_value('recipient_name', ''), 'id="recipient_name" class="formelement" required');
		$output['recipient_email'] = @form_input('recipient_email',set_value('recipient_email', ''), 'id="recipient_email" class="formelement" required');
		$output['sender_message'] = @form_input('sender_message',set_value('sender_message', ''), 'id="sender_message" class="formelement"');
		$output['sender_name'] = @form_input('sender_name',set_value('sender_name', $sender_preset), 'id="sender_name" class="formelement" required');
		
		$this->pages->view('gift-card', $output, true);	
    }
    
}