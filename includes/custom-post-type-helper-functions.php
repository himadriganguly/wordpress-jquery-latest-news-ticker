<?php

/**
 * Create a field container and switch.
 *
 * @since 1.0.0
 */
function metabox_container( $field, $context='side', $post_domain ) 
{
	global $post;
	
	// Get the default value of the field
	$default = isset( $field['default'] ) ? $field['default'] : '';
	
	// Get the value of the field if previously saved in databse
	$value = ( get_post_meta( $post->ID, $field['id'], true ) != '' ) ? get_post_meta( $post->ID, $field['id'], true ) : $default;
	
	// Get the disply style 
	$display = isset( $field['display'] ) ? $field['display'] : '';
	?>
	
	<tr class="clrdr-post-metabox-field clrdr-post-metabox-field-<?php echo $field['type']; ?> clrdr-post-metabox<?php echo $field['id']; ?><?php if( isset($field['class']) ) { echo ' '.$field['class']; } ?>">
		<?php
		$content_class = 'clrdr-post-metabox-field-content clrdr-post-metabox-field-content-full clrdr-post-metabox-'.$field['type'].' clearfix';
		//$content_span = ' colspan="2"';
		$label = false;
		
		if ( isset($field['name']) || isset($field['description']) ) 
		{

			$content_class = 'clrdr-post-metabox-field-content clrdr-post-metabox-'.$field['type'].' clearfix';
			//$content_span = '';
			$label = true;
			?>
			
			<?php if( $context == 'side' || $display == 'vertical' ) { ?><td><table><tr><?php } ?>
				
			<td class="clrdr-post-metabox-label">
				<?php if( isset($field['name']) ) { ?><label for="<?php echo $field['id']; ?>"><?php echo $field['name']; ?></label><?php } ?>
				<?php if( isset($field['description']) ) { ?><small><?php echo $field['description']; ?></small><?php } ?>
			</td>
			
			<?php if( $context == 'side' || $display == 'vertical' ) { echo '</tr>'; } ?>
			
			<?php			
		}
		
		if( $label ) 
		{
			if( $context == 'side' || $display == 'vertical' ) 
			{
				echo '<tr>'; 
			} 
		} 
		?>
		
		<td<?php //echo $content_span; ?> class="<?php echo $content_class; ?>" id="<?php echo $post->ID; ?>">
			
			<?php
				// Call the function to display the field
				if ( function_exists('metabox_'.$field['type']) ) 
				{
					call_user_func( 'metabox_'.$field['type'], $field, $value, $post_domain );
				}
			?>
			
		</td>

		<?php 
			if( $label ) 
			{
				if( $context == 'side' || $display == 'vertical' ) 
				{
					echo '</tr></table></td>'; 
				} 
			} 
		?>

	</tr>
		
<?php
}


/**
 * Append fields
 *
 * @since 1.0.0
 */
function metabox_append_field( $field, $post_domain ) 
{

	// Add appended fields
	if( isset($field['append']) ) {

		$fields = $field['append'];
		$settings = ( isset($field['option'] ) ) ? $field['option'] : false;

		if( is_array($fields) ) {

			foreach( $fields as $id => $field ) {

				// Get the value
				if( $settings) 
				{
					$options = get_option( $settings );
					$value = isset( $options[$id] ) ? $options[$id] : get_option( $id );
				} 
				else 
				{
					global $post;
					$value = get_post_meta( $post->ID, $id, true );
				}

				// Set the default if no value
				if( $value == '' && isset($field['default']) ) 
				{
					$value = $field['default'];
				}

				if( isset($field['type']) ) 
				{
					if( $settings ) 
					{
						$field['id'] = $settings.'['.$id.']';
						$field['option'] = $settings;
					} 
					else 
					{
						$field['id'] = $id;
					}

					// Call the function to display the field
					if ( function_exists('metabox_'.$field['type']) ) 
					{
						echo '<div class="clrdr-post-metabox-appended clrdr-post-metabox'.$field['id'].'">';
							call_user_func( 'metabox_'.$field['type'], $field, $value, $post_domain );
						echo '</div>';
					}
				}
			}
		}
	}
}


/**
 * Renders a list
 *
 * @since 1.0.0
**/ 
function metabox_list( $field, $values='', $post_domain ) 
{
	$output = '<table><thead>';

	$headers = false;
	$header_str = '';
	
	foreach( $field['structure'] as $id => $str ) 
	{

		$header_str .= '<th>';
		if( isset($str['header']) ) {
			$headers = true;
			$header_str .= $str['header'];
		}
		$header_str .= '</th>';
	}
	if( $headers ) {
		$output .= '<tr><th class="clrdr-post-metabox-list-item-handle"></th>'.$header_str.'</tr></thead>';
	}

	$buttons = '<td class="clrdr-post-metabox-list-item-delete"><a class="button" href="#">-</a></td><td class="clrdr-post-metabox-list-item-add"><a class="button" href="#">+</a></td>';
	if( is_array($values) ) {
		foreach( $values as $index=>$value ) {
			$structure = metabox_list_structure( $index, $field, $value, $post_domain );
			$output .= '<tr class="clrdr-post-metabox-list-item"><td class="clrdr-post-metabox-list-item-handle"><span>|||</span></td>'.$structure.$buttons.'</tr>';
		}
	}

	// If nothing is being output make sure one field is showing
	if( $values == '' || count($values) == 0 ) {
		$structure = metabox_list_structure( 0, $field, $values, $post_domain );
		$output .= '<tbody><tr class="clrdr-post-metabox-list-item"><td class="clrdr-post-metabox-list-item-handle"><span class="sort hndle">|||</span></td>'.$structure.$buttons.'</tr></tbody>';
	}

	$output .= '</table>';

	echo $output;
	
	if( isset($field['append']) )
	{
		// Add appended fields
		metabox_append_field( $field, $post_domain );
	}
	
}


/**
 * Adds the list structure
 *
 * @since 1.0.0
**/ 
function metabox_list_structure( $pos, $fields, $m_value='', $post_domain ) {

	$main_id = $fields['id'];
	
	// Add appended fields
	if( isset( $fields['structure'] ) ) {

		$fields = $fields['structure'];
		$settings = ( isset($fields['option'] ) ) ? $fields['option'] : false;

		if( is_array($fields) ) {

			ob_start();

			foreach( $fields as $id => $field ) {
								
				// Get the value
				$value = isset( $m_value[$id] ) ? $m_value[$id] : '';

				// Get the width
				$width = isset( $field['width'] ) ? ' style="width:'.$field['width'].'"' : '';

				if( isset($field['type']) ) {

					$field['id'] = $main_id.'['.$pos.']['.$id.']';					

					// Call the function to display the field
					if ( function_exists('metabox_'.$field['type']) ) 
					{
						echo '<td'.$width.' class="clrdr-post-metabox-list-structure-item clrdr-post-metabox'.$main_id.'-'.$id.'" base="'.$main_id.'" field="'.$id.'">';
						call_user_func( 'metabox_'.$field['type'], $field, $value, $post_domain );
						echo '</td>';
					}
				}
			}

			return ob_get_clean();
		}
	}
}


/**
 * Renders a text field.
 *
 * @since 1.0.0
**/ 
function metabox_text( $field, $value='', $post_domain ) 
{
	$size = ( isset($field['size']) ) ? $field['size'] : 40;
	
	$before = ( isset($field['before']) ) ? '<span>'.$field['before'].' </span>' : '';
	
	$after = ( isset($field['after']) ) ? '<span> '.$field['after'].'</span>' : '';
	
	$text_align = ( isset($field['text_align']) ) ? ' style="text-align:'.$field['text_align'].'"' : '' ;
	
	$output = $before.'<input name="'.$field['id'].'" id="'.$field['id'].'" type="text" value="'.$value.'" size="'.$size.'"'.$text_align.'>'.$after;
	
	echo $output;

	if( isset($field['append']) )
	{
		// Add appended fields
		metabox_append_field( $field, $post_domain );
	}
}


/**
 * Renders a textarea.
 *
 * @since 1.0.0
**/
function metabox_textarea( $field, $value='', $post_domain ) 
{
	$rows = ( isset($field['rows']) ) ? $field['rows'] : 5;
	
	$cols = ( isset($field['cols']) ) ? $field['cols'] : 40;
	
	$output = '<textarea name="'.$field['id'].'" id="'.$field['id'].'" rows="'.$rows.'" cols="'.$cols.'">'.$value.'</textarea>';
	
	echo $output;

	if( isset($field['append']) )
	{
		// Add appended fields
		metabox_append_field( $field, $post_domain );
	}
	
}


/**
 * Renders a select field.
 *
 * @since 1.0.0
**/
function metabox_select( $field, $value='', $post_domain ) 
{

	$before = ( isset($field['before']) ) ? '<span>'.$field['before'].' </span>' : '';
	$after = ( isset($field['after']) ) ? '<span> '.$field['after'].'</span>' : '';

	$output = $before.'<select name="'.$field['id'].'" id="'.$field['id'].'">';

  	if( $field['options'] )
	{

  		$key_val = isset( $field['key_val'] ) ? true : false;

	  	foreach ( $field['options'] as $key => $option ) 
	  	{
	  		if( is_numeric($key) && !$key_val ) 
	  		{
				$name = ( is_array( $option ) ) ? $option['name'] : $option;
				$val = ( is_array( $option ) ) ? $option['value'] : $option;
			} 
			else 
			{
				$name = $option;
				$val = $key;
			}
			$selected = ( $val == $value ) ? 'selected="selected"' : '';
			$output .= '<option value="'.$val.'" '.$selected.'>'.stripslashes( $name ).'</option>';
		}
	}
  	
  	$output .= '</select>'.$after;

	echo $output;

	if( isset($field['append']) )
	{
		// Add appended fields
		metabox_append_field( $field, $post_domain );
	}
}


/**
 * Renders a checkbox.
 *
 * @since 1.0.0
 */
function metabox_checkbox( $field, $value='', $post_domain ) 
{

	$output = '';
	$before = ( isset($field['before']) ) ? '<span>'.$field['before'].' </span>' : '';
	$after = ( isset($field['after']) ) ? '<span> '.$field['after'].'</span>' : '';

	if( isset($field['options']) ) 
	{
		$break = '<br/>';
		
		if ( isset($field['display']) ) 
		{
			if( $field['display'] == 'inline' ) 
			{
				$break = '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
		}
		foreach( $field['options'] as $i => $option ) 
		{
			$checked = ( isset($value[$i]) ) ? 'checked="checked"' : '';
			$output .= '<label><input name="'.$field['id'].'['.$i.']" id="'.$field['id'].'['.$i.']" type="checkbox" value="1" '.$checked.' /> '.$option.'</label>'.$break;
		}

	} 
	else 
	{

		$checked = ( $value == 1 ) ? 'checked="checked"' : '';
		$output .= '<label><input name="'.$field['id'].'" id="'.$field['id'].'" type="checkbox" value="1" '.$checked.' />';
		if( isset($field['label']) ) 
		{
			$output .= ' '.$field['label'];
		}
		$output .= '</label>';
	}

	echo $before.$output.$after;

	if( isset($field['append']) )
	{
		// Add appended fields
		metabox_append_field( $field, $post_domain );
	}
}


/**
 * Renders a radio field.
 *
 * @since 1.0.0
 */
function metabox_radio( $field, $value='', $post_domain ) 
{

	if( isset( $field['options'] ) ) 
	{
		$output = '';
		$break = '<br/>';
		 
		if ( isset( $field['display'] ) ) 
		{
			if( $field['display'] == 'inline' ) 
			{
				$break = '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
		}
		foreach( $field['options'] as $i => $option ) 
		{
			$checked = ( $value == $i ) ? 'checked="checked"' : '';
			$output .= '<label><input name="'.$field['id'].'" id="'.$field['id'].'" type="radio" value="'.$i.'" '.$checked.' /> '.$option.'</label>'.$break;
		}
	}

	echo $output;

	if( isset($field['append']) )
	{
		// Add appended fields
		metabox_append_field( $field, $post_domain );
	}
}


/**
 * Renders a number field.
 *
 * @since 1.0.0
 */
function metabox_number( $field, $value='', $post_domain ) 
{
	$style = ( isset($field['style']) ) ? ' style="'.$field['style'].'"' : '';
	$before = ( isset($field['before']) ) ? '<span>'.$field['before'].' </span>' : '';
	$after = ( isset($field['after']) ) ? '<span> '.$field['after'].'</span>' : '';
	$output = $before.'<input name="'.$field['id'].'" id="'.$field['id'].'" type="number" value="'.$value.'" class="small-text"'.$style.'>'.$after;
	
	echo $output;

	if( isset($field['append']) )
	{
		// Add appended fields
		metabox_append_field( $field, $post_domain );
	}
}

function metabox_date ( $field, $value='', $post_domain ) 
{
	$style = ( isset($field['style']) ) ? ' style="'.$field['style'].'"' : '';
	$before = ( isset($field['before']) ) ? '<span>'.$field['before'].' </span>' : '';
	$after = ( isset($field['after']) ) ? '<span> '.$field['after'].'</span>' : '';
	
	$output = $before.'<input class="clrdr_datepicker"'.$style.'type="text" name="'. $field['id']. '" id="'. $field['id']. '" value="'.$value.'" placeholder="dd-mm-yyyy" />'.$after;
	
	echo $output;
	
	if( isset($field['append']) )
	{
		// Add appended fields
		metabox_append_field( $field, $post_domain );
	}
}

/**
 * Renders an html field.
 *
 * @since 1.0.0
 */
function metabox_html( $field, $value='', $post_domain ) 
{

	// Echo the html
	echo $value;

	if( isset($field['append']) )
	{
		// Add appended fields
		metabox_append_field( $field, $post_domain );
	}
}


/**
 * Renders a metabox toggle.
 *
 * @since 1.0.0
 */
function metabox_toggle( $field, $value='', $post_domain ) 
{

	if( isset($field['options']) ) {

		$output = '';
		$output .= '<input type="hidden" id="'.$field['id'].'" name="'.$field['id'].'" value="'.$value.'" />';

		foreach( $field['options'] as $i => $option ) {

			$button = $option['button'];
			$metaboxes = $option['metaboxes'];
			$metabox_list = join( ',', $metaboxes );

			// Create a button
			$selected = ( $value == $i ) ? ' button-primary' : '';
			$output .= '<a href="'.$i.'" metaboxes="'.$metabox_list.'" class="clrdr-post-metabox-toggle button'.$selected.'">'.$button.'</a>&nbsp;';
		}

		echo $output;
	}

	if( isset($field['append']) )
	{
		// Add appended fields
		metabox_append_field( $field, $post_domain );
	}
}


/**
 * Renders a sort.
 *
 * @since 1.0.0
 */
function metabox_sort( $field, $value='', $post_domain ) 
{

	global $post;

	$rows = array();
	if( is_array($value) ) {
		foreach( $value as $id ) {
			if( isset($field['rows'][$id]) ) {
				$rows[$id] = $field['rows'][$id];
			}
		}
	} else {
		$rows = $field['rows'];
	}

	foreach( $field['rows'] as $id=>$data ) {
		if( !isset($rows[$id]) ) {
			$rows[$id] = $data;
		}
	}

	$output = '<table>';

	foreach( $rows as $id => $data ) {

		$output .= '<tr class="clrdr-post-metabox-sort-item"><td class="clrdr-post-metabox-sort-item-handle"><span></span></td>';
		if( isset($data['name']) ) {
			$output .= '<td class="clrdr-post-metabox-sort-name">'.$data['name'].'</td>';
		}
		$output .= '<td><input name="'.$field['id'].'[]" id="'.$field['id'].'[]" type="hidden" value="'.$id.'">';

		// Find the value
		$data_value = get_post_meta( $post->ID, $data['id'], true );
		if( $data_value == '' && isset($data['default']) ) {
			$data_value = $data['default'];
		}

		ob_start();
		// Call the function to display the field
		if ( function_exists('metabox_'.$data['type']) ) {
			call_user_func( 'metabox_'.$data['type'], $data, $data_value, $post_domain );
		}
		$output .= ob_get_clean();

		$output .= '</td>';

		$output .= '</tr>';
	}

	$output .= '</table>';

	echo $output;

	if( isset($field['append']) )
	{
		// Add appended fields
		metabox_append_field( $field, $post_domain );
	}
}


/**
 * Renders a wysiwyg field.
 *
 * @since 1.0.0
 */
function metabox_wysiwyg( $field, $value='', $post_domain ) 
{
	$settings = array();
	$settings['media_buttons'] = true;
	$settings['textarea_rows'] = ( isset($field['rows']) ) ? $field['rows'] : 12;
	wp_editor( $value, $field['id'], $settings );

	if( isset($field['append']) )
	{
		// Add appended fields
		metabox_append_field( $field, $post_domain );
	}
}


/**
 * Renders the code fields.
 *
 * @since 1.0.0
 */
function metabox_code( $field, $value='', $post_domain )
{

	global $post;

	// Display the shortcode code
	if( $field['id'] == '_clrdr_post_shortcode' ) 
	{
		
		echo '<pre><p>[' . $field['shortcode'] .' postid="'.$post->ID.'"]</p></pre>';
		
	// Display the function code
	} 
	elseif( $field['id'] == '_clrdr_post_function' ) 
	{

		echo '<pre><p>&lt;?php if(function_exists(\''.$field['function'].'\')){'.$field['function'].'('.$post->ID.');} ?&gt;</p></pre>';
	}

	// Display a "Select All" button
	// $button = isset($field['button']) ? $field['button'] : __( 'Select Code', $post_domain );
	// echo '<a href="#" class="button clrdr-post-metabox-code-select">'.$button.'</a>';
}