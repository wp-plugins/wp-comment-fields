<?php
/*
 * this is main plugin class
*/


/* ======= the model main class =========== */
if(!class_exists('NM_Framwork_V1_WPComment')){
	$_framework = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'nm-framework.php';
	if( file_exists($_framework))
		include_once($_framework);
	else
		die('Reen, Reen, BUMP! not found '.$_framework);
}


/*
 * [1]
 * TODO: change the class name of your plugin
 */
class NM_PLUGIN_WPComments extends NM_Framwork_V1_WPComment{
	
	private static $ins = null;
	
	public static function init()
	{
		add_action('plugins_loaded', array(self::get_instance(), '_setup'));
	}
	
	public static function get_instance()
	{
		// create a new object if it doesn't exist.
		is_null(self::$ins) && self::$ins = new self;
		return self::$ins;
	}
	

	var $inputs;

	/*
	 * plugin constructur
	 */
	function _setup(){
		
		//setting plugin meta saved in config.php
		$this -> plugin_meta = get_plugin_meta_wpcomment();

		//getting saved settings
		$this -> plugin_settings = get_option($this->plugin_meta['shortname'].'_settings');


		// populating $inputs with NM_Inputs object
		$this->inputs = $this->get_all_inputs ();
		
		
		/*
		 * [2]
		 * TODO: update scripts array for SHIPPED scripts
		 * only use handlers
		 */
		//setting shipped scripts
		$this -> wp_shipped_scripts = array('jquery');
		
		
		/*
		 * [3]
		* TODO: update scripts array for custom scripts/styles
		*/
		//setting plugin settings
		$this -> plugin_scripts =  array(array(	'script_name'	=> 'scripts',
												'script_source'	=> '/js/script.js',
												'localized'		=> true,
												'type'			=> 'js'
										),
												array(	'script_name'	=> 'styles',
														'script_source'	=> '/plugin.styles.css',
														'localized'		=> false,
														'type'			=> 'style'
												),
										);
		
		/*
		 * [4]
		* TODO: localized array that will be used in JS files
		* Localized object will always be your pluginshortname_vars
		* e.g: pluginshortname_vars.ajaxurl
		*/
		$this -> localized_vars = array('ajaxurl' => admin_url( 'admin-ajax.php' ),
				'plugin_url' 		=> $this->plugin_meta['url'],
				'settings'			=> $this -> plugin_settings);
		
		
		/*
		 * [5]
		 * TODO: this array will grow as plugin grow
		 * all functions which need to be called back MUST be in this array
		 * setting callbacks
		 */
		//following array are functions name and ajax callback handlers
		$this -> ajax_callbacks = array('save_settings',		//do not change this action, is for admin
										'save_file_meta',);
		
		/*
		 * plugin localization being initiated here
		 */
		add_action('init', array($this, 'wpp_textdomain'));
		
		
		
		/*
		 * hooking up scripts for front-end
		*/
		add_action('wp_enqueue_scripts', array($this, 'load_scripts'));
		
		
		/*
		 * registering callbacks
		*/
		$this -> do_callbacks();



		/*
		 * Action hooks for comment fields
		*/
		add_action('comment_form_after_fields', array($this, 'render_comments_meta_fields'));
		add_action('comment_form_logged_in_after', array($this, 'render_comments_meta_fields'));
		
		/*
		 * Saving comment meta
		 */
		add_action( 'comment_post', array($this, 'save_comment_meta_fields') );
		
		
		/*
		 * adding meta box in comments edit page
		 */
		add_action('add_meta_boxes_comment', array($this, 'render_comment_meta_admin_box'), 1);
		
		
		/**
		 * adding comment meta in front view
		 */
		add_filter('comment_text', array($this, 'render_comment_meta_front'),100);
		
		/**
		 * validating comments
		 */
		add_filter( 'preprocess_comment', array($this, 'verify_comment_meta_data') );
	}
	
	
	
	// i18n and l10n support here
	// plugin localization
	function wpp_textdomain() {
		$locale_dir = $this->plugin_meta['path'] . '/locale/';
		load_plugin_textdomain('nm-wpcomments', false, $locale_dir);
	}
	
	
	/*
	 * =============== NOW do your JOB ===========================
	 * 
	 */

	/*
	 * Callbacks for comments fields
	*/

	function render_comments_meta_fields(){

		$this -> load_template('render.comment.meta.php');
	}

	function save_comment_meta_fields($comment_id){
		
		$comment_meta = get_option('wpcomment_meta');
		//wpcomment_pa($_POST); exit;
			
		foreach ($comment_meta as $index => $meta){
			
			$comment_meta_key = $meta['data_name'];
			$comment_meta_val = esc_attr($_POST[$comment_meta_key]);
			add_comment_meta( $comment_id, $comment_meta_key, $comment_meta_val );
			
		}

	}


	
	/*
	 * Verify comment meta
	 */	
	function verify_comment_meta_data( $commentdata ) {

		$comment_meta = get_option('wpcomment_meta');
		//wpcomment_pa($_POST); exit;
		
		if($comment_meta){
			foreach ($comment_meta as $index => $meta){
			
				$comment_meta_key = $meta['data_name'];
				$comment_meta_val = esc_attr($_POST[$comment_meta_key]);
					
				if($meta['required'] == 'on' && $comment_meta_val == ''){
					
					$default_message = sprintf(__('%s is a required field'), esc_attr($meta['title']) );
					$message = ($meta['error_message'] != '' ? esc_attr($meta['error_message']) : $default_message);
					wp_die( sprintf(__('%s'), $message) );
				}
			}
		}
		
		
		return $commentdata;
		
		
	}	
	
	/**
	 * rendering comment meta BOX in comments admin page
	 */
	function render_comment_meta_admin_box($comment)
	{
		
		add_meta_box('nm_comment_meta_box', __('Comment Meta'), array($this, 'render_comment_meta_admin'), 'comment', 'normal');
	}
	
	/**
	 * rendering comment META in comments admin page
	 */
	function render_comment_meta_admin($comment) {
		
		$comment_meta = get_option('wpcomment_meta');
		?>
	        <table class="form-table editcomment comment_xtra">
	        <tbody>
	        <?php foreach ($comment_meta as $index => $meta){
	        	
	        	$comment_meta_key = $meta['data_name'];
	        	$comment_meta_val = get_comment_meta($comment -> comment_ID, $comment_meta_key, true);
	        	
	        	if($comment_meta_val != ''){
	        ?>
		        <tr valign="top">
		            <td class="first"><?php printf(__('%s'), esc_attr($meta['title'])); ?></td>
		            <td><?php echo esc_attr($comment_meta_val); ?></td>
		        </tr>
	        
	        <?php
	        	} 
	        }
	        ?>
	        
	       </tbody>
	       </table>
	    <?php
	}
	
	
	/**
	 * adding comment meta in front view
	 */
	function render_comment_meta_front($comment){

		$comment_meta = get_option('wpcomment_meta');
		
		if($comment_meta){
			$comment_meta_heading = 'Comment meta';		//should be get by option
			$comment .= '<p><strong>'.sprintf(__('%s'), $comment_meta_heading).'</strong></p>';
			foreach ($comment_meta as $index => $meta){
			
				$comment_meta_key = $meta['data_name'];
				$comment_meta_val = get_comment_meta(get_comment_ID(), $comment_meta_key, true);
				
				if($comment_meta_val != ''){
					$comment .= sprintf(__('%s: '), esc_attr($meta['title']));
					$comment .= '<i>'.sprintf(__('%s'), esc_attr($comment_meta_val)).'</i>';
					$comment .= ', ';
				}			
			}
			
			$comment = substr($comment, 0, -1);
		}
		
		return $comment;
	
	}

	/*
	 * returning NM_Inputs object
	 */
	private function get_all_inputs() {
		if (! class_exists ( 'NM_Inputs_wpcomment' )) {
			$_inputs = $this->plugin_meta ['path'] . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'input.class.php';
			if (file_exists ( $_inputs ))
				include_once ($_inputs);
			else
				die ( 'Reen, Reen, BUMP! not found ' . $_inputs );
		}
		
		$nm_inputs = new NM_Inputs_wpcomment ();
		// filemanager_pa($this->plugin_meta);
		
		// registering all inputs here
		
		return array (
				
				'text' 		=> $nm_inputs->get_input ( 'text' ),
				'select' 	=> $nm_inputs->get_input ( 'select' ),
				'radio' 	=> $nm_inputs->get_input ( 'radio' ),
				'file' 		=> $nm_inputs->get_input ( 'file' ),
				'checkbox' 		=> $nm_inputs->get_input ( 'checkbox' ),
		);
		
		// return new NM_Inputs($this->plugin_meta);
	}

	/*
	 * saving admin setting in wp option data table
	 */
	function save_settings(){
	
		//pa($_REQUEST);
		$existingOptions = get_option($this->plugin_meta['shortname'].'_settings');
		//pa($existingOptions);
	
		update_option($this->plugin_meta['shortname'].'_settings', $_REQUEST);
		_e('All options are updated', $this->plugin_meta['shortname']);
		die(0);
	}


	/*
	 * saving form meta in admin call
	 */
	function save_file_meta() {
		
		//print_r($_REQUEST); exit;
		global $wpdb;
		
		update_option('wpcomment_meta', $_REQUEST['file_meta']);
		
		$resp = array (
					'message' => __ ( 'Form added successfully', 'nm-wpcomments' ),
					'status' => 'success',
					'form_id' => $res_id 
			);
		
		echo json_encode ( $resp );
		
		
		die ( 0 );
	}


	/*
	 * rendering template against shortcode
	*/
	function render_shortcode_template($atts){

		extract(shortcode_atts(array(
				'foo' => 'no foo',
				'baz' => 'default baz',
		), $atts));


		ob_start();

		$template_vars = array('user_name'	=> 'Testing the vars');
		$this -> load_template('contact-form.php', $template_vars);

		$output_string = ob_get_contents();
		ob_end_clean();
			
		return $output_string;
	}



	// ================================ SOME HELPER FUNCTIONS =========================================

	
	


	function activate_plugin(){

		//do nothing so far.

	}

	function deactivate_plugin(){

		//do nothing so far.
	}
}