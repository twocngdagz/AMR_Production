<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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

class Emailer_model extends CI_Model {

	// init vars
	var $table = 'emails';					// default table
	var $userTable = 'users';				// default user table

	function __construct()
	{
		parent::__construct();

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}		
	}

	function get_emails($campaignID)
	{ 	
	    // set where
	    $where['deleted'] = 0;
		$where['campaignID'] = $campaignID;
		$where['status'] = 'S';

		// set campaign ID and get campaign name
		$output['campaignID'] = $campaignID ? $campaignID : 0;
		$query = $this->db->get_where('email_campaigns', array('campaignID' => $output['campaignID']));
		$output['campaign'] = $query->row_array();

		$this->db->order_by('dateSent', 'desc');
		$query = $this->db->get_where('emails', $where, $this->site->config['paging'], $this->pagination->offset);
		$output['emails'] = $query->result_array();

		$query_total = $this->db->get_where('emails', $where); 
		$config['total_rows'] = $query_total->num_rows();	
		$config['per_page'] = $this->site->config['paging'];
		$config['full_tag_open'] = '<div class="pagination"><p>';
		$config['full_tag_close'] = '</p></div>';
		$config['num_links'] = 3;
		$this->pagination->initialize($config);

		$where['status'] = 'D';
		$where['deploy'] = 0;
		$this->db->order_by('dateCreated', 'desc');
		$query = $this->db->get_where('emails', $where);
		$output['emailDrafts'] = $query->result_array();
		
		$where['status'] = 'D';
		$where['deploy'] = 1;
		$this->db->order_by('dateCreated', 'desc');
		$query = $this->db->get_where('emails', $where);
		$output['pendingEmails'] = $query->result_array();

		return $output;
	}
	
	function get_latest_deployed_email()
	{ 	
	    // set where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));
		$this->db->where('status', 'S');
		$this->db->where('deploy', 1);
		$this->db->order_by('dateCreated', 'desc');

		$query = $this->db->get('emails');
		
		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}
	
	function get_email($emailID)
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));
		
		// where email
		$this->db->where('emailID', $emailID);
		
		// get
		$query = $this->db->get('emails');
		
		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_campaigns()
	{
		$this->db->where('siteID', $this->siteID);
		$query = $this->db->get('email_campaigns');
		return $query->result_array();	
	}

	function get_templates($templateID = '')
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));
		
		// get based on template ID
		if ($templateID)
		{
			$query = $this->db->get_where('email_templates', array('templateID' => $templateID), 1);
			
			if ($query->num_rows())
			{
				return $query->row_array();
			}
			else
			{
				return FALSE;
			}	
		}
		// or just get all of em
		else
		{
			// template type
			$query = $this->db->get('email_templates');
			
			if ($query->num_rows())
			{
				return $query->result_array();
			}
			else
			{
				return FALSE;
			}
		}
	}
	
	function get_user_by_email($email)
	{
		$this->db->where(array('siteID' => $this->siteID));

		$this->db->where('email', $email);
		
		$this->db->order_by('name', 'desc');
			
		$query = $this->db->get('email_list_subscribers', 1);

		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}
	
	function search_users($q = '')
	{
		// default wheres
		$this->db->where(array('siteID' => $this->siteID));

		// tidy query
		$q = $this->db->escape_like_str($q);

		$this->db->where('(email LIKE "%'.$q.'%" OR name LIKE "%'.$q.'%")');
			
		$query = $this->db->get('email_list_subscribers', 30);

		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}
	
	function get_lists()
	{
		// default where
		$this->db->where(array('email_lists.siteID' => $this->siteID, 'deleted' => 0));
		
		// count subscribers
		$this->db->select(' (SELECT COUNT(*) FROM '.$this->db->dbprefix.'email_list_subscribers WHERE listID = '.$this->db->dbprefix.'email_lists.listID) AS numSubscribers');
		
		// select
		$this->db->select('email_lists.*');
		
		// template type
		$query = $this->db->get('email_lists');
		
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}
	
	function get_list($listID)
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));	
		
		// where email
		$this->db->where('listID', $listID);
		
		// template type
		$query = $this->db->get('email_lists', 1);
		
		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}
	
	function get_lists_by_email($email)
	{
		// default where
		$this->db->where(array('email_lists.siteID' => $this->siteID, 'deleted' => 0));
		
		// where email
		$this->db->where('email', $email);
		
		// join
		$this->db->join('email_list_subscribers', 'email_list_subscribers.listID = email_lists.listID');
						
		// template type
		$query = $this->db->get('email_lists');
		
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_list_subscribers($listID)
	{
		// default wheres
		$this->db->where(array('siteID' => $this->siteID));		
		
		// where email
		$this->db->where('listID', $listID);
		
		// select
		$this->db->select('email, name');
		
		// order
		$this->db->order_by('email', 'asc');
						
		// template type
		$query = $this->db->get('email_list_subscribers');
		
		if ($query->num_rows())
		{
			foreach($query->result_array() as $row)
			{
				$subscribers[$row['email']] = $row['name'];
			}
			
			return $subscribers;
		}
		else
		{
			return FALSE;
		}
	}

	function get_blocks($emailID)
	{ 
		$this->db->select('MAX(blockID) as blockID');
		$this->db->where('siteID', $this->siteID);
		$this->db->where('emailID', $emailID);
		$this->db->group_by('blockRef');
		$this->db->order_by('dateCreated','DESC');
		$query = $this->db->get('email_blocks');
		$result = $query->result_array();
		$numBlocks = $query->num_rows();
		
		// get data
		if ($numBlocks > 0)
		{
			foreach($result as $row)
			{
				$blockIDs[] = $row['blockID'];
			}

			$this->db->where('blockID IN ('.join(',',$blockIDs).') AND emailID = \''.$emailID.'\'');
			$this->db->order_by('blockID');
			$query = $this->db->get('email_blocks', $numBlocks);
			
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}		
	}
	
	function generate_template($pagedata, $file = FALSE)
	{
		// either build template from a file or from db
		if ($file)
		{
			$template['body'] = $file;
		}
		else
		{
			$templateData = $this->get_templates($pagedata['templateID']);
			$template['body'] = $templateData['body'];
		}
		
		// get includes
		preg_match_all('/include-([a-z0-9]+)/i', $template['body'], $includes);
		if ($includes)
		{
			foreach($includes[1] as $include => $value)
			{
				$template['include-'.$value] = $this->get_include($value);
			}
		}

		return $template;
	}

	function generate_page($pagedata, $file = FALSE, $admin = FALSE)
	{
		$page = $this->generate_template($pagedata, $file);

		// populate blocks from db (if they exist)			
		if ($blocksResult = $this->get_blocks($pagedata['emailID']))
		{
			foreach($blocksResult as $blockRow)
			{
				// set bodies and get images for mkdn view
				$body[$blockRow['blockRef']] = form_prep($blockRow['body']);
				
				$mkdnBody[$blockRow['blockRef']] = $this->template->parse_body($blockRow['body']);
			}
		}

		// parse for blocks
		preg_match_all('/block([a-z0-9\-_]+)/i', $page['body'], $blocks);
		if ($blocks)
		{
			foreach($blocks[1] as $block => $value)
			{
				$blockRef = 'block'.$value;
				if ($admin)
				{
					$page[$blockRef] = '
						<div class="halogycms_container">
							<div id="'.$blockRef.'" class="halogycms_edit">
								<div class="halogycms_buttons">
									<a href="#" class="halogycms_boldbutton"><img src="'.$this->config->item('staticPath').'/images/btn_bold.png" alt="Bold" title="Bold" class="halogycms_helper" /></a>
									<a href="#" class="halogycms_italicbutton"><img src="'.$this->config->item('staticPath').'/images/btn_italic.png" alt="Italic" title="Italic" class="halogycms_helper" /></a>
									<a href="#" class="halogycms_h1button"><img src="'.$this->config->item('staticPath').'/images/btn_h1.png" alt="Heading 1" title="Heading 1" class="halogycms_helper" /></a>
									<a href="#" class="halogycms_h2button"><img src="'.$this->config->item('staticPath').'/images/btn_h2.png" alt="Heading 2" title="Heading 2" class="halogycms_helper" /></a>
									<a href="#" class="halogycms_h3button"><img src="'.$this->config->item('staticPath').'/images/btn_h3.png" alt="Heading 3" title="Heading 3" class="halogycms_helper" /></a>
									<a href="#" class="halogycms_urlbutton"><img src="'.$this->config->item('staticPath').'/images/btn_url.png" alt="Insert Link" title="Insert Link" class="halogycms_helper" /></a>
									<a href="#" class="halogycms_imagebutton"><img src="'.$this->config->item('staticPath').'/images/btn_image.png" alt="Insert Image" title="Insert Image" class="halogycms_helper" /></a>
									<a href="#" class="halogycms_filebutton"><img src="'.$this->config->item('staticPath').'/images/btn_file.png" alt="Insert File" title="Insert File" class="halogycms_helper" /></a>									
									<a href="#" class="halogycms_cancelbutton"><img src="'.$this->config->item('staticPath').'/images/btn_cancel.png" alt="Cancel" title="Cancel Changes" class="halogycms_helper" /></a>
									<a href="'.site_url('/admin/emailer/add_block/'.$pagedata['emailID'].'/'.$blockRef).'" class="halogycms_savebutton"><img src="'.$this->config->item('staticPath').'/images/btn_save.png" alt="Save" title="Save Changes" class="halogycms_helper" /></a>
									<a href="#" class="halogycms_editbutton"><img src="'.$this->config->item('staticPath').'/images/btn_edit_block_grey.png" alt="Edit" /></a>									
								</div>
								<div class="halogycms_blockelement">'.@$mkdnBody[$blockRef].'</div>
								<div class="halogycms_editblock"><textarea rows="8" cols="10" class="code">'.@$body[$blockRef].'</textarea></div>
							</div>
						</div>
					';
				}
				else
				{
					$page[$blockRef] = @$mkdnBody[$blockRef];
				}				
			}
		}

		return $page;
	}	

	function get_include($includeRef)
	{ 
		$this->db->where('includeRef', $includeRef);
		$query = $this->db->get('includes');
		
		// get data
		if ($query->num_rows())
		{
			$result = $query->row_array();
			return $result['body'];
		}
		else
		{
			return FALSE;
		}		
	}

	function parse_images($body)
	{
		// parse for images
		preg_match_all('/image\:([a-z0-9\-_]+)/i', $body, $images);
		if ($images)
		{
			foreach($images[1] as $image => $value)
			{
				$imageData = $this->get_image($value);
				$body = str_replace('{image:'.$value.'}', '<img src="'.$imageData['src'].'" alt="'.$imageData['imageName'].'" title="'.$imageData['imageName'].'" class="pic" />', $body);
			}
		}
		
		return $body;
	}

	function get_image($imageRef)
	{
		$this->load->library('uploads');
		
		$this->db->where('siteID', $this->siteID);
		$this->db->where('imageRef', $imageRef);
		$query = $this->db->get('images');
		
		// get data
		if ($query->num_rows())
		{
			$row = $query->row_array();
			$image = $this->uploads->load_image($row['filename']);
			$row['src'] = $image['src'];
			return $row;
		}
		else
		{
			return FALSE;
		}		
	}
	
	function add_list_subscribers($listID, $subscribers)
	{
		if (!$list = $this->get_list($listID))
		{
			return FALSE;
		}

		foreach ($subscribers as $email => $name)
		{
			$this->db->set('listID', $listID);
			$this->db->set('email', $email);
			$this->db->set('name', $name);
			$this->db->set('siteID', $this->siteID);
			$this->db->insert('email_list_subscribers');
		}
		
		return TRUE;
	}
	
	function add_list_subscriber($listID, $email = '', $name = '')
	{
		if ((!$list = $this->get_list($listID)) || !$email)
		{
			return FALSE;
		}
		
		$emails = array();
		
		if ($existingSubscribers = $this->get_list_subscribers($listID))
		{
			foreach($existingSubscribers as $existingEmail => $existingName)
			{
				$emails[] = $existingEmail;
			}
		}
		
		if (in_array($email, $emails) && !$existingSubscribers[$email] && $name)
		{
			$this->db->set('name', $name);
			$this->db->where('email', $email);
			$this->db->where('listID', $listID);
			$this->db->where('siteID', $this->siteID);
			$this->db->update('email_list_subscribers');
		}
		elseif (!in_array($email, $emails))
		{
			$this->db->set('listID', $listID);
			$this->db->set('email', $email);
			$this->db->set('name', $name);
			$this->db->set('siteID', $this->siteID);
			$this->db->insert('email_list_subscribers');
		}
		
		return TRUE;
	}
	
	function reset_list_subscribers($listID)
	{
		$this->db->where('listID', $listID);
		$this->db->where('siteID', $this->siteID);
		$this->db->delete('email_list_subscribers');
		
		return TRUE;
	}

	function reset_list_subscriber($email)
	{
		$this->db->where('email', $email);
		$this->db->where('siteID', $this->siteID);
		$this->db->delete('email_list_subscribers');
		
		return TRUE;
	}
	
	function remove_list_subscriber($listID, $email = '')
	{
		if ((!$list = $this->get_list($listID)) || !$email)
		{
			return FALSE;
		}
		
		$emails = array();
		
		if ($existingSubscribers = $this->get_list_subscribers($listID))
		{
			foreach($existingSubscribers as $email => $name)
			{
				$emails[] = $email;
			}
		}
		
		if (in_array($email, $emails))
		{
			$this->db->where('listID', $listID);
			$this->db->where('email', $email);
			$this->db->where('siteID', $this->siteID);
			$this->db->delete('email_list_subscribers');
		}
		
		return TRUE;
	}

	function add_block($body, $objectID, $blockRef = 'block')
	{
		// delete blocks for this version
		$this->db->where('email_blocks.siteID', $this->siteID);
		$this->db->where('email_blocks.emailID', $objectID['emailID']);
		$this->db->where('email_blocks.blockRef', $blockRef);
		$this->db->delete('email_blocks');

		// add block
		$this->db->query("SET NAMES 'utf8'");
		$this->db->set($objectID);
		$this->db->set('dateCreated', date("Y-m-d H:i:s"));
		$this->db->set('siteID', $this->siteID);
		$this->db->set('blockRef', $blockRef);
		$this->db->set('body', $body);
		$this->db->insert('email_blocks');

		return TRUE;
	}

	function add_temp_email()
	{
		$this->db->set('siteID', $this->siteID);		
		$this->db->set('deleted', 1);
		$this->db->set('emailName', '');
		$this->db->set('dateCreated', date("Y-m-d H:i:s"));
		$this->db->insert('emails');
		return $this->db->insert_id();
	}

	function setdeploy($objectID)
	{
		$this->db->set('deploy', 1);
		$this->db->set('sent', 0);
		$this->db->set('views', 0);
		$this->db->set('clicks', 0);
		$this->db->set('unsubscribed', 0);
		$this->db->set('dateSent', date("Y-m-d H:i:s"));		
		$this->db->where($objectID);
		$this->db->update($this->table); 
	}

	function addview($trackingID)
	{
		$this->db->set('views', 'views+1', FALSE);
		$this->db->where('emailID', $trackingID);
		$this->db->update('emails');	
	}

	function viewsend($emailID)
	{
		$where = array();
		if ($emailID > 0)
		{
			$where['emailID'] = $emailID;
		}
		if ($where) $this->db->where($where);
		
		$numToDeploy = $this->db->count_all_results('email_deploy');

		$where['sent'] = 1;
		$this->db->where($where);
		$numDeployed = $this->db->count_all_results('email_deploy');

		return @floor(($numDeployed / $numToDeploy) * 100);
	}

	function parse_body($body, $emailID, $type = '', $autolink = FALSE)
	{	
		// get body		
		$parsedBody = $body;
		$matches = array();

		// autolink
		if ($autolink)
		{
			$parsedBody = auto_link($body);
		}

		// get link style if set
		$this->db->join('email_templates','email_templates.templateID = emails.templateID', 'left');
		$this->db->where('emails.emailID',$emailID);
		$query = $this->db->get('emails',1);
		$templateRow = $query->row_array();
		$linkStyle = $templateRow['linkStyle'];

		// parse body for dynamics
		$parsedBody = str_ireplace('{BASE_URL}', base_url(),$parsedBody);
		$parsedBody = str_ireplace('{VIEWONLINE}', base_url().'viewemail/'.$emailID, $parsedBody);

		if ($type == 'html')
		{
			// parse HTML for links
			preg_match_all('/href\s*=\s*(?:(?:\"(?<url>[^\"]*)\"))/i',$parsedBody, $matches);
			foreach($matches[1] as $key => $value)
			{
				$matches[1][$key] = str_replace($value, base_url().'goto/'.$this->url_encode($emailID.'|'.$value), $value);
			}
			foreach($matches[0] as $key => $value)
			{
				$parsedBody = str_replace($matches[0][$key], 'href="'.$matches[1][$key].'" style="'.$linkStyle.'"', $parsedBody);
			}
			
			// parse images for relative links
			preg_match_all('/src=\"([a-z0-9\-_.\/]+)\"/i',$parsedBody, $matches);
			foreach($matches[1] as $key => $value)
			{
				$matches[1][$key] = str_replace($value, site_url($value), $value);
			}
			foreach($matches[0] as $key => $value)
			{
				$parsedBody = str_replace($matches[0][$key], 'src="'.$matches[1][$key].'"', $parsedBody);
			}

			// add tracking image and html / body
			//$trackingImg = '<img src="'.base_url().'emailer/tracker/trackviews/'.$emailID.'">';
			$trackingImg = '';
			$parsedBody = '<html><body>'."\n".$parsedBody."\n".'</body></html>'."\n".$trackingImg;
		}
		if ($type == 'text')
		{
			// parse text for links
			preg_match_all('#(^|\s|\()((http(s?)://)|(www\.))(\w+[^\s\)\<]+)#i',$parsedBody, $matches);
	
			for ($i = 0; $i < sizeof($matches['0']); $i++)
			{
				$period = '';
				if (preg_match("|\.$|", $matches['6'][$i]))
				{
					$period = '.';
					$matches['6'][$i] = substr($matches['6'][$i], 0, -1);
				}
		
				$parsedBody = str_replace($matches['0'][$i],' '.base_url().'goto/'.$this->url_encode($emailID.'|http'.$matches['4'][$i].'://'.$matches['5'][$i].$matches['6'][$i]).$period, $parsedBody);
			}
		}

		return $parsedBody;
	}

	function deploymail($emailID)
	{
		$email = $this->get_email($emailID);
		
		$query = $this->db->get_where('email_deploy', array('emailID' => $emailID));
		
		if ($subscribers = $this->get_list_subscribers($email['listID']))
		{
			foreach ($subscribers as $email => $name)
			{
				$this->db->set('emailID', $emailID);
				$this->db->set('email', $email);
				$this->db->set('name', $name);
				$this->db->set('siteID', $this->siteID);
				$this->db->insert('email_deploy');
			}
			
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function tracklink($key)
	{	
		$pipe = $this->url_decode($key);
		$array = explode('|',$pipe);

		$trackingID = @$array[0];
		$path = @$array[1];

		if (preg_match('/\/subs\//i',$path))
		{
			$unsubPipe = substr($path,strpos($path,'/unsub/')+7);

			$this->db->where('emailID', $trackingID);
			$this->db->set('unsubscribed', 'unsubscribed+1', FALSE);
			$this->db->update($this->table);

			return $path;
		}
		elseif ($trackingID)
		{
			$this->db->where('emailID', $trackingID);
			$this->db->set('clicks', 'clicks+1', FALSE);
			$this->db->update($this->table);

			return $path;
		}
		else
		{
			return FALSE;
		}
	}

	function parse_body_user($body, $emailID, $email, $name = '')

	{
		// get first name and last name
		$names = explode(' ', $name);
		$firstName = (sizeof($names) > 1 && $names[0]) ? ucfirst(trim($names[0])) : $name;
		$lastName = (sizeof($names) > 1) ? ucfirst(end($names)) : '';
		
		// unsubscribe link
		$userkey = $this->core->encode($email);
		$parsedBody = str_ireplace('{unsubscribe}', base_url().'subs/'.$userkey, $body);
		$parsedBody = str_ireplace('{name}', trim($name), $parsedBody);
		$parsedBody = str_ireplace('{first-name}', $firstName, $parsedBody);
		$parsedBody = str_ireplace('{last-name}', $lastName, $parsedBody);

		return $parsedBody;
	}

	function batchmail()
	{	
		$this->db->select('deployID, email_deploy.emailID, bodyText, bodyHTML, emailSubject, email, name, templateID');
		$this->db->join('emails', 'emails.emailID = email_deploy.emailID');
		$this->db->where('email_deploy.sent', 0);
		$this->db->where('emails.deployDate <= ', date("Y-m-d H:i:s", strtotime('now')));
		$this->db->where('email_deploy.siteID', $this->siteID);
		
		$query = $this->db->get('email_deploy', 2);

		$numSent = 0;
		$numFailed = 0;

		// deploy email
		if ($query->num_rows())
		{
			$result = $query->result_array();			
			
			// go through each email that's in the outbox and send
			foreach($result as $deploy)
			{	
				// parse bodies for user stuff
				$parsedText = $this->parse_body_user($deploy['bodyText'], $deploy['emailID'], $deploy['email'], $deploy['name']);
				$parsedEmail = $this->parse_email($deploy['emailID']);
				$parsedHTML = $this->parse_body_user($parsedEmail, $deploy['emailID'], $deploy['email'], $deploy['name']);
	
				// parse bodies
				$parsedText = $this->parse_body($parsedText, $deploy['emailID'], 'text');
				$parsedHTML = $this->parse_body($parsedHTML, $deploy['emailID'], 'html');
			
				// set config
				$fromEmail = (@$this->site->config['emailerEmail']) ? $this->site->config['emailerEmail'] : $this->site->config['siteEmail'];
				$fromName = (@$this->site->config['emailerName']) ? $this->site->config['emailerName'] : $this->site->config['siteName'];
				
				
				//get listID
				$this->db->select('listID');
				$this->db->where('emailID', $deploy['emailID']);
				$listidquery = $this->db->get('emails');
				if ($listidquery->num_rows())
				{
					$listidqueryresult = $listidquery->result_array();			
					foreach($listidqueryresult as $listidqueryvar)
					{	
					
					$listID_val = $listidqueryvar['listID'];
					}
					
				}
				
				
				//encrypt email and list id
				$deployedemail = $deploy['email'];
				$encrypted_email = $this->core->encode($deployedemail);
				
				$listIDval = $listID_val;
				$encrypted_listIDval = $this->core->encode($listIDval);
				
				//links					
				$unsubscribe_link = "<br><br> Stop receiving these emails - ".base_url()."emailer/unsubscribe_newsletter?er=".$encrypted_email."&lst=".$encrypted_listIDval."";					
				$unsubscribe_link_text = "\n \n Stop receiving these emails - ".base_url()."emailer/unsubscribe_newsletter?er=".$encrypted_email."&lst=".$encrypted_listIDval."";					
				
		
				// check html message exists
				if ($deploy['templateID'] != 0)
				{
					
					$this->email->message($parsedHTML.$unsubscribe_link);
					$this->email->set_alt_message($parsedText);
				}
				
				// or send plain text
				else
				{
					$config['mailtype'] = 'text';
					$this->email->initialize($config);
					$this->email->message($parsedText.$unsubscribe_link_text);
				}
		
				// prepare email
				$this->email->to($deploy['email']);
				$this->email->from($fromEmail, $fromName);
				$this->email->reply_to($this->site->config['siteEmail']); 
				$this->email->subject($deploy['emailSubject']);
			
				// now check if the email gets sent
				if ($this->email->send())
				{
					$this->db->where(array('deployID' => $deploy['deployID']));
					$this->db->update('email_deploy', array('sent' => 1));
					$this->db->where(array('emailID' => $deploy['emailID']));
					$this->db->set('sent', 'sent+1', FALSE);
					$this->db->update($this->table);
					$numSent ++;
				}
				else
				{
					$this->db->where(array('deployID' => $deploy['deployID']));
					$this->db->update('email_deploy', array('failed' => 1));
					$numFailed ++;
				}
			}

			$this->db->select('emailID, AVG(sent) as avgSent');
			$this->db->group_by('emailID');
			$query = $this->db->get_where('email_deploy', array('sent' => '1'));
			if ($query->num_rows())
			{
				$result = $query->result_array();
				foreach ($result as $row)
				{
					if ($row['avgSent'] == 1)
					{
						$this->db->set('status', 'S');
						$this->db->where('emailID', $row['emailID']);
						$this->db->update('emails');
					}
				}
			}		
	
			return 'Number sent: '.$numSent.'<br>Number failed: '.$numFailed;
		}
		else
		{
			return FALSE;
		}
	}

	function sendtest($emailID)
	{
		if (!$this->input->post('testEmails'))
		{
			return FALSE;
		}
		
		// get emails
		foreach (explode("\n", $this->input->post('testEmails')) as $subscriber)
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
			
			// get email
			$query = $this->db->get_where($this->table, array('emailID' => $emailID));
			$emailRow = $query->row_array();
		
			// parse bodies for user stuff
			$parsedText = $this->parse_body_user($emailRow['bodyText'], $emailRow['emailID'], $email, $name);
			$parsedEmail = $this->parse_email($emailID);
			$parsedHTML = $this->parse_body_user($parsedEmail, $emailRow['emailID'], $email, $name);
	
			// parse bodies for links
			$parsedText = $this->parse_body($parsedText, $emailRow['emailID'], 'text');
			$parsedHTML = $this->parse_body($parsedHTML, $emailRow['emailID'], 'html');
	
			// set config
			$fromEmail = (@$this->site->config['emailerEmail']) ? $this->site->config['emailerEmail'] : $this->site->config['siteEmail'];
			$fromName = (@$this->site->config['emailerName']) ? $this->site->config['emailerName'] : $this->site->config['siteName'];
			
			// check html message exists
			if (strlen($parsedHTML) > 100)
			{
				$this->email->message($parsedHTML);
				$this->email->set_alt_message($parsedText);
			}
			
			// or send plain text
			else
			{
				$config['mailtype'] = 'text';
				$this->email->initialize($config);
				$this->email->message($parsedText);
			}
	
			// prepare email
			$this->email->to($email);
			$this->email->from($fromEmail, $fromName);
			$this->email->reply_to($this->site->config['siteEmail']); 
			$this->email->subject($emailRow['emailSubject']);
							
			// now check if the email gets sent
			if ($this->email->send())
			{
				continue;
			}
			else
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	function parse_email($emailID = '')
	{
		if ($emailID)
		{
			$this->load->library('parser');
			$this->load->library('mkdn');

			$where['emailID'] = $emailID;
			$query = $this->db->get_where('emails', $where);
			$email = $query->row_array();
		
			$templateData = $this->get_templates($email['templateID']);
			$output['body'] = @$templateData['body'];
						
			if ($emailID)
			{
				if ($blocksResult = $this->get_blocks($emailID))
				{
					$blocksArray = array();
					foreach($blocksResult as $blockRow)
					{
						$blocksArray[$blockRow['blockRef']] = $this->template->parse_body($blockRow['body']);
					}
				}		
			}		
	
			// populate blocks
			preg_match_all('/block([a-z0-9\-_]+)/i', $output['body'], $blocks);
			if ($blocks)
			{
				$i=0;
				foreach($blocks[0] as $block => $value)
				{
					$output[$value] = '<div id="'.$value.'">'.@$blocksArray[$value].'</div>';
					$i++;
				}
			}
	
			// populate includes
			preg_match_all('/include-([a-z0-9]+)/i', $output['body'], $includes);
			if ($includes)
			{
				foreach($includes[1] as $include => $value)
				{
					$output['include-'.$value] = '<div class="include">INCLUDE</div>';
				}
			}
	
			$parsedHTML = $this->parser->parse('default',$output,true);
	
			return $parsedHTML;
		}
		else
		{
			return FALSE;
		}
	}
	
	function url_encode($data)
	{
		return strtr(rtrim(base64_encode($data), '='), '+/', '-_');
	}
	
	function url_decode($base64)
	{
		return base64_decode(strtr($base64, '-_', '+/'));
	}
		
}