<?php
/*
 * this file contains pluing meta information and then shared
 * between pluging and admin classes
 * 
 * [1]
 * TODO: change this meta as plugin needs
 */

$plugin_dir = 'wp-comment-fields';

$plugin_meta		= array('name'			=> 'Comments Fields',
							'shortname'		=> 'wpcomments',
							'path'			=> WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin_dir,
							'url'			=> plugins_url( $plugin_dir , dirname(__FILE__) ),
							'db_version'	=> 3.0,
							'logo'			=> plugins_url( $plugin_dir.'/images/logo.png' , dirname(__FILE__) ),
							'men_position'	=> 78);

/*
 * TODO: change the function name
*/
function get_plugin_meta_wpcomment(){
	
	global $plugin_meta;
	
	//print_r($plugin_meta);
	
	return $plugin_meta;
}

function wpcomment_pa($arr){

	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}