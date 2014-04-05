<?php

function jquery_news_ticker( $postid='', $atts=false ) 
{
	$ticker = get_post( $postid );	
	
	if( $ticker && $ticker->post_status=='publish' )
	{
		global $wp_query;
		
		$original_query = $wp_query;
		$wp_query 		= null;
		$wp_query 		= new WP_Query();
		
		// Get all the custom data
		$custom_fields = get_post_custom( $postid );
		
		$meta_data = array();
		foreach( $custom_fields as $key => $value ) 
		{
			$meta_data[$key] = maybe_unserialize( $value[0] );
		}
		
		// Override meta data with supplied attributes
		if( is_array($atts) ) 
		{
			foreach( $atts as $key => $value ) 
			{
				$meta_data["_clrdr_post_{$key}"] = $value;
			}
		}
		
		// Extract the metadata array into variables
		extract( $meta_data );
		
		ob_start();
		
		// Create the opening div
		$tick_id 	= 'clrdr-post-'.$postid;
		
		// Add a unique id
		if( isset($_clrdr_post_unique_id) ) 
		{
			if( $_clrdr_post_unique_id != '' ) 
			{
				$tick_id = esc_attr( sanitize_html_class( $_clrdr_post_unique_id ) );
			}
		}
		
		$today = date("d-m-y"); 
		
		echo '<ul id="'. $tick_id . '" class="js-hidden">';
		foreach( $meta_data['_clrdr_post_ticks'] as $i => $tick ) 
		{
									
			if( $text = $tick['tick'] ) 
			{
					 		
				$text = convert_chars(wptexturize($text));
				
				// Get the contents
				if( $link = esc_url($tick['link']) ) 
				{
					$contents = '<a href="'.$link.'" target="'.$tick['target'].'">'.$text.'</a>';
				} 				
				else 
				{
					$contents = $text;
				}
				if( isset( $tick['st_date'] ) && !empty( $tick['st_date'] ) && isset( $tick['en_date'] ) && !empty( $tick['en_date'] ) && $today >= $tick['st_date'] && $today <= $tick['en_date'] )		
				{
					echo '<li class="news-item">'. $contents. '</li>';
				}		
				elseif ( isset( $tick['st_date'] ) && !empty( $tick['st_date'] ) && $today >= $tick['st_date'] )		
				{
					echo '<li class="news-item">'. $contents .'</li>';	
				}
				elseif ( isset( $tick['en_date'] ) && !empty( $tick['en_date'] ) && $today <= $tick['en_date'] )
				{
					echo '<li class="news-item">'. $contents . '</li>';	
				}
				elseif ( empty( $tick['st_date'] ) && empty( $tick['en_date'] ) )
				{
					echo '<li class="news-item">'. $contents .'</li>';
				}					
					
		 	}								
		}
		echo '</ul>';
		?>
		<script type="text/javascript">
			jQuery(function () {
					jQuery('#<?php echo $tick_id; ?>').ticker({
						speed: 			<?php echo $meta_data['_clrdr_post_scroll_speed']; ?>, 						// The speed of the reveal
						controls:		<?php echo $meta_data['_clrdr_post_controls']; ?>,	   						// Whether or not to show the jQuery News Ticker controls
						titleText: 		<?php echo '\''.$meta_data['_clrdr_post_titletext'].'\''; ?>,						   						// To remove the title set this to an empty String
						displayType:	<?php echo '\''.$meta_data['_clrdr_post_display_type'].'\''; ?>,							   						// Animation type - current options are 'reveal' or 'fade'
						direction: 		<?php echo '\''. $meta_data['_clrdr_post_scroll_direction'] .'\''; ?>,       // Ticker direction - current options are 'ltr' or 'rtl'
						pauseOnItems: 	<?php echo $meta_data['_clrdr_post_pause_items']; ?>,   								  						// The pause on a news item before being replaced
						fadeInSpeed: 	<?php echo $meta_data['_clrdr_post_fade_speed']; ?>,      							   						// Speed of fade in animation
        				fadeOutSpeed: 	<?php echo $meta_data['_clrdr_post_fadeout_speed']; ?>      							   						// Speed of fade out animation											
					});
				});
		</script>
		
		<?php
		// Restore the original $wp_query
		$wp_query = null;
		$wp_query = $original_query;
		wp_reset_postdata();
	}

	// Return the output
	return ob_get_clean();
}

function jquery_news_ticker_li( $text, $link='', $st_date='', $en_date='' )
{
	
}
