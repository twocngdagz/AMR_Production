<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

// assets
$route['images/(:any)/(:any)/(:any)'] = 'pages/files/images/$3';
$route['images/(:any)/(:any)'] = 'pages/files/images/$2';
$route['images/(:any)'] = 'pages/files/images/$1';
$route['gfx/(:any)/(:any)/(:any)'] = 'pages/files/gfx/$3';
$route['gfx/(:any)/(:any)'] = 'pages/files/gfx/$2';
$route['gfx/(:any)'] = 'pages/files/gfx/$1';
$route['css/(:any)/(:any)/(:any)'] = 'pages/files/css/$3';
$route['css/(:any)/(:any)'] = 'pages/files/css/$2';
$route['css/(:any)'] = 'pages/files/css/$1';
$route['js/(:any)/(:any)/(:any)'] = 'pages/files/js/$3';
$route['js/(:any)/(:any)'] = 'pages/files/js/$2';
$route['js/(:any)'] = 'pages/files/js/$1';

// admin and modules
$handle = opendir(APPPATH.'modules');

if ($handle)
{
	while ( false !== ($module = readdir($handle)) )
	{
		// make sure we don't map silly dirs like .svn, or . or ..
		//pr('test');
		if (substr($module, 0, 1) != ".")
		{
			if ( file_exists(APPPATH.'modules/'.$module.'/'.$module.'_routes.php') )
			{
				include(APPPATH.'modules/'.$module.'/'.$module.'_routes.php');
			}

			if ( file_exists(APPPATH.'modules/'.$module.'/controllers/admin.php') )
			{
				$route['admin/'.$module] = $module.'/admin';
				$route['admin/'.$module.'/(.*)'] = $module.'/admin/$1';
			}

			if ( file_exists(APPPATH.'modules/'.$module.'/controllers/'.$module.'.php') )
			{
				$route[$module] = $module;
				$route[$module.'/(.*)'] = $module.'/$1';
			}
		}
	}
}

// defaults
$route['default_controller'] = 'pages';
$route['scaffolding_trigger'] = 'scaf';
$route['404_override'] = '';

// cms
$route['(.*)'] = 'pages/index/$1';



/* End of file routes.php */
/* Location: ./application/config/routes.php */