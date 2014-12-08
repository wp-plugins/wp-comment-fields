<?php
/*
 * working behind the seen
*/


class NM_PLUGIN_WPComments_Admin extends NM_PLUGIN_WPComments{


	var $menu_pages, $plugin_scripts_admin, $plugin_settings;


	function __construct(){


		//setting plugin meta saved in config.php
		$this -> plugin_meta = get_plugin_meta_wpcomment();

		//getting saved settings
		$this -> plugin_settings = get_option($this->plugin_meta['shortname'].'_settings');


		/*
		 * [1]
		* TODO: change this for plugin admin pages
		*/
		$this -> menu_pages		= array(array('page_title'	=> $this->plugin_meta['name'],
				'menu_title'	=> $this->plugin_meta['name'],
				'cap'			=> 'manage_options',
				'slug'			=> $this->plugin_meta['shortname'],
				'callback'		=> 'main_settings',
				'parent_slug'		=> '',),
		);


		/*
		 * [2]
		* TODO: Change this for admin related scripts
		* JS scripts and styles to loaded
		* ADMIN
		*/
		$this -> plugin_scripts_admin =  array(array(	'script_name'	=> 'scripts-global',
				'script_source'	=> '/js/nm-global.js',
				'localized'		=> false,
				'type'			=> 'js',
				'page_slug'		=> $this->plugin_meta['shortname']
		),
				array(	'script_name'	=> 'scripts-admin',
						'script_source'	=> '/js/admin.js',
						'localized'		=> true,
						'type'			=> 'js',
						'page_slug'		=> $this->plugin_meta['shortname'],
						'depends' => array (
								'jquery',
								'jquery-ui-accordion',
								'jquery-ui-draggable',
								'jquery-ui-droppable',
								'jquery-ui-sortable',
								'jquery-ui-slider',
								'jquery-ui-dialog',
								'jquery-ui-tabs',
								'media-upload',
								'thickbox'
						)
				),
				array (
						'script_name' => 'ui-style',
						'script_source' => '/js/ui/css/smoothness/jquery-ui-1.10.3.custom.min.css',
						'localized' => false,
						'type' => 'style',
						'page_slug' => $this->plugin_meta ['shortname'],
						'depends' => '',
				),
					
		);


		add_action('admin_menu', array($this, 'add_menu_pages'));
	}

	function load_scripts_admin(){

		//localized vars in js
		$arrLocalizedVars = array(	'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'plugin_url' 		=> $this -> plugin_meta['url']
		);

		//admin end scripts

		if($this -> plugin_scripts_admin){
			foreach($this -> plugin_scripts_admin as $script){

				//checking if it is style
				if( $script['type'] == 'js'){
					$depends = (isset($script['depends']) ? $script['depends'] : NULL);
					wp_enqueue_script ( $this->plugin_meta ['shortname'] . '-' . $script ['script_name'], $this->plugin_meta ['url'] . $script ['script_source'], $depends );

					//if localized
					if( $script['localized'] )
						wp_localize_script( $this -> plugin_meta['shortname'].'-'.$script['script_name'], $this -> plugin_meta['shortname'].'_vars', $arrLocalizedVars);
				}else{

					wp_enqueue_style($this -> plugin_meta['shortname'].'-'.$script['script_name'], $this -> plugin_meta['url'].$script['script_source'], __FILE__);
				}
			}
		}

	}



	/*
	 * creating menu page for this plugin
	*/

	function add_menu_pages(){

		foreach ($this -> menu_pages as $page){
				
			if ($page['parent_slug'] == ''){

				$menu = add_menu_page(sprintf(__("%s", 'nm-wpcomments'), $page['page_title']),
						sprintf(__("%s", 'nm-wpcomments'), $page['menu_title']),
						$page['cap'],
						$page['slug'],
						array($this, $page['callback']),
						$this->plugin_meta['logo'],
						$this->plugin_meta['menu_position']);
			}else{

				$menu = add_submenu_page($page['parent_slug'],
						sprintf(__("%s", 'nm-wpcomments'), $page['page_title']),
						sprintf(__("%s", 'nm-wpcomments'), $page['menu_title']),
						$page['cap'],
						$page['slug'],
						array($this, $page['callback'])
				);

			}
				
			//loading script for only plugin optios pages
			// page_slug is key in $plugin_scripts_admin which determine the page
			foreach ($this -> plugin_scripts_admin as $script){

				if (is_array($script['page_slug'])){
						
					if (in_array($page['slug'], $script['page_slug']))
						add_action('admin_print_scripts-'.$menu, array($this, 'load_scripts_admin'));
				}else if ($script['page_slug'] == $page['slug']){
					add_action('admin_print_scripts-'.$menu, array($this, 'load_scripts_admin'));
				}
			}
		}


	}


	//====================== CALLBACKS =================================
	function main_settings(){

		$this -> load_template('admin/settings.php');

	}

	function list_users(){


	}
}