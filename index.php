<?php 

/*
Plugin Name: WP Comments Fields Manager
Plugin URI: http://www.najeebmedia.com
Description: This plugin allow users to add custom fields in post comments area.
Version: 1.0
Author: Najeeb Ahmad
Author URI: http://www.najeebmedia.com/
Text Domain: nm-wpcomments
Domain Path: 
*/


/*
 * Lets start from here
*/

/*
 * loading plugin config file
 */
$_config = dirname(__FILE__).'/config.php';
if( file_exists($_config))
	include_once($_config);
else
	die('Reen, Reen, BUMP! not found '.$_config);


/* ======= the plugin main class =========== */
$_plugin = dirname(__FILE__).'/classes/plugin.class.php';
if( file_exists($_plugin))
	include_once($_plugin);
else
	die('Reen, Reen, BUMP! not found '.$_plugin);

/*
 * [1]
 * TODO: just replace class name with your plugin
 */
$nmwpcomment  = NM_PLUGIN_WPComments::get_instance();
NM_PLUGIN_WPComments::init();


if( is_admin() ){

	$_admin = dirname(__FILE__).'/classes/admin.class.php';
	if( file_exists($_admin))
		include_once($_admin );
	else
		die('file not found! '.$_admin);

	$nmwpcomment_admin = new NM_PLUGIN_WPComments_Admin();
}

/*
 * activation/install the plugin data
*/
register_activation_hook( __FILE__, array('NM_PLUGIN_WPComments', 'activate_plugin'));
register_deactivation_hook( __FILE__, array('NM_PLUGIN_WPComments', 'deactivate_plugin'));


