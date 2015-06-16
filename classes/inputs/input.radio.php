<?php
/*
 * Followig class handling radio input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Radio extends NM_Inputs_wpcomment{
	
	/*
	 * input control settings
	 */
	var $title, $desc, $settings;
	
	/*
	 * this var is pouplated with current plugin meta
	*/
	var $plugin_meta;
	
	function __construct(){
		
		$this -> plugin_meta = get_plugin_meta_wpcomment();
		
		$this -> title 		= __ ( 'Radio Input', 'nm-wpcomments' );
		$this -> desc		= __ ( 'regular radio input', 'nm-wpcomments' );
		$this -> settings	= self::get_settings();
		
	}
	
	
	
	
	private function get_settings(){
		
		return array (
		'title' => array (
				'type' => 'text',
				'title' => __ ( 'Title', 'nm-wpcomments' ),
				'desc' => __ ( 'It will be shown as field label', 'nm-wpcomments' ) 
		),
		'data_name' => array (
				'type' => 'text',
				'title' => __ ( 'Data name', 'nm-wpcomments' ),
				'desc' => __ ( 'REQUIRED: The identification name of this field, that you can insert into body email configuration. Note:Use only lowercase characters and underscores.', 'nm-wpcomments' ) 
		),
		'description' => array (
				'type' => 'text',
				'title' => __ ( 'Description', 'nm-wpcomments' ),
				'desc' => __ ( 'Small description, it will be diplay near name title.', 'nm-wpcomments' ) 
		),
		'error_message' => array (
				'type' => 'text',
				'title' => __ ( 'Error message', 'nm-wpcomments' ),
				'desc' => __ ( 'Insert the error message for validation.', 'nm-wpcomments' ) 
		),
		
		'options' => array (
				'type' => 'textarea',
				'title' => __ ( 'Add options', 'nm-wpcomments' ),
				'desc' => __ ( 'Type each option per line', 'nm-wpcomments' ) 
		),
		'selected' => array (
				'type' => 'text',
				'title' => __ ( 'Selected option', 'nm-wpcomments' ),
				'desc' => __ ( 'Type option name (given above) if you want already radioed.', 'nm-wpcomments' ) 
		),
		
		'required' => array (
				'type' => 'checkbox',
				'title' => __ ( 'Required', 'nm-wpcomments' ),
				'desc' => __ ( 'Select this if it must be required.', 'nm-wpcomments' ) 
		),
		
		'class' => array (
				'type' => 'text',
				'title' => __ ( 'Class', 'nm-wpcomments' ),
				'desc' => __ ( 'Insert an additional class(es) (separateb by comma) for more personalization.', 'nm-wpcomments' ) 
		),
	
		);
	}
	
	
	/*
	 * @params: $options
	*/
	function render_input($args, $options="", $default=""){
		
		$_html = '';
		foreach($options as $opt)
		{
			$checked = ($opt == $default) ? 'checked="checked"' : '';
				
			$output = stripslashes(trim($opt));
			$_html .= '<label for="f-meta-'. $opt.'"> <input type="radio" ';
			
			foreach ($args as $attr => $value){
					
				$_html .= $attr.'="'.stripslashes( $value ).'"';
			}
		
			$_html .= ' value="'.$opt.'" '.$checked.'>';
			$_html .= $output;
		
			$_html .= '</label>';
		}
		
		echo $_html;
	}
}