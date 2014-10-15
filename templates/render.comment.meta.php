<?php
/**
 * Rendering comment extra fields
 * generated with N-Media plugin
 */

global $nmwpcomment;

$existing_meta = get_option('wpcomment_meta');

//$existing_meta = json_encode($existing_meta);

//wpcomment_pa($existing_meta);

if($existing_meta){


	foreach($existing_meta as $key => $meta)
		{
			
			//filemanager_pa($meta);
			$type 			= $meta['type'];
			$name 			= strtolower( preg_replace("![^a-z0-9]+!i", "_", $meta['data_name']) );
			$field_class 	= (isset($meta['class']) ? $meta['class'] : '');
			$title 			= (isset($meta['title']) ? $meta['title'] : '');
			$p_class		= 'comment-'.$name;

			if($meta['required'] == 'on'){
				$required = 'true';
				$label = sprintf(__('%s <span class="required">*</span>', 'wp-comments'), $title);
			}else{
				$required = 'false';
				$label = sprintf(__('%s', 'wp-comments'), $title);
			}
			
			

			switch ($type) {

				case 'text':
					
					echo '<p class="'.$p_class.'">';
					echo '<label for="'.$name.'">'.$label.'</label>';
					echo '<input id="'.$name.'" name="'.$name.'" type="text" aria-required="'.$required.'" class="'.$field_class.'">';
					echo '</p>';

					break;

				case 'select':
					
					$options 	= (isset($meta['options']) ? $meta['options'] : '');
					$options 	= explode("\n", $options);

					$selected 	= (isset($meta['selected']) ? $meta['selected'] : '');

					echo '<p class="'.$p_class.'">';
					echo '<label for="'.$name.'">'.$label.'</label>';
					echo '<select name="'.$name.'">';

						foreach ($options as $key) {
							
							$selected_option = ($selected == $key ? 'selected="selected"' : '');
							echo '<option value="'.esc_attr($key).'" '.$selected_option.'>'.esc_attr($key).'</option>';
						}

					echo '</select>';
					echo '</p>';

					break;

				case 'radio':
					
					$options 	= (isset($meta['options']) ? $meta['options'] : '');
					$options 	= explode("\n", $options);

					$selected 	= (isset($meta['selected']) ? $meta['selected'] : '');

					echo '<p class="'.$p_class.'">';
					echo '<label for="'.$name.'">'.$label.'</label>';

						foreach ($options as $key) {
							
							$selected_option = ($selected == $key ? 'selected="selected"' : '');
							echo '<input name="'.$name.'" type="radio" value="'.esc_attr($key).'" '.$selected_option.' />'.esc_attr($key).'<br />';
						}

					echo '</p>';

					break;

				case 'checkbox':
					
					$options 	= (isset($meta['options']) ? $meta['options'] : '');
					$options 	= explode("\n", $options);

					$selected 	= (isset($meta['selected']) ? $meta['selected'] : '');

					echo '<p class="'.$p_class.'">';
					echo '<label for="'.$name.'">'.$label.'</label>';

						foreach ($options as $key) {
							
							$selected_option = ($selected == $key ? 'selected="selected"' : '');
							echo '<input name="'.$name.'" type="checkbox" value="'.esc_attr($key).'" '.$selected_option.' />'.esc_attr($key).'<br />';
						}

					echo '</p>';

					break;
			}
		}

}

