<?php

$meatGeneral = array(
						'thumb-size'	=> array(	'label'		=> __('Images thumb size', 'nm-wpcomments'),
							'desc'		=> __('Enter integer value for thumb size for images', 'nm-wpcomments'),
							'id'			=> 'wpcomments'.'_thumb_size',
							'type'			=> 'text',
							'default'		=> '75',
							'help'			=> __('type size in px like: <strong>100</strong>', 'nm-wpcomments')
					),
);
					


$meat_comment_meta = array('comment-meta'	=> array(	
									'desc'		=> $proFeatures,
									'type'		=> 'file',
									'id'		=> 'file-meta.php',
									),
								);


$this -> the_options = array(
					
					'comment-meta'	=> array(	'name'		=> __('Comment Fields', 'nm-wpcomments'),
							'type'	=> 'tab',
							'desc'	=> __('More field can be attached to Comments', 'nm-wpcomments'),
							'meat'	=> $meat_comment_meta,
							'class'	=> 'pro',
					
					),
	
);

//print_r($repo_options);