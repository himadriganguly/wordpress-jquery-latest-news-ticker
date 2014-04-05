<?php

   $post_domain = 'clrdr-latest-news-ticker';

	$latest_news = new ColourDrift_Post_Type( 'Latest News Ticker',array('supports'=>array('title')),array(), "jQuery News Ticker Updated!" );  	
	
	// Create an array to store the default item structure
	$tick_structure = array(
								'tick' 		=> array(
														'header' 	=> __( 'Ticker Text', $post_domain ),
														'width' 	=> '30%',
														'type' 		=> 'textarea',			
														'rows' 		=> 2
					  							),
								'link' 		=> array(
														'header'	=> __( 'Link', $post_domain ),
														'type' 		=> 'text'
												),
								'st_date'	=>	array(
														'header'	=> __( 'Start Date', $post_domain ),
														'type'		=> 'date',														
												),
								'en_date'	=>	array(
														'header'	=> __( 'End Date', $post_domain ),
														'type'		=> 'date'
												),	
								'target' 	=> array(
														'header' 	=> __('Target', $post_domain ),
														'type' 		=> 'select',
														'options' 	=> array( '_self', '_blank' )
											   )
						);

	// Create an array to store the fields
	$default_fields = array();
	
	// Add the items field
	$default_fields['ticks'] = array(
										'id' => '_clrdr_post_ticks',
										'type' => 'list',	
										'structure' => apply_filters('clrdr_post_default_tick_structure', $tick_structure)
								);

	// Create the metabox
	$default_data = array(
						  	'title' => __( 'Default Ticker Items', $post_domain ),
							'context' => 'normal',
							'priority' => 'high',
							'fields' => apply_filters( 'clrdr_post_type_fields_default', $default_fields )
					);
	
	$latest_news->meta_box( $default_data );
	
	// Create an array to store the fields
	$scroll_fields = array();

	// Add the dimensions field
	$scroll_fields['direction'] = array(
											'id' 			=> '_clrdr_post_scroll_direction',
											'type' 			=> 'radio',
											'name' 			=> __( 'Scroll direction', $post_domain ),
											'options' 		=> array(
																	'ltr' => __( 'Left to Right', $post_domain ),
																	'rtl' => __( 'Right to Left', $post_domain ),
														 		),
											'default' 		=> 'ltr',
											'description' 	=> __( 'Set the scroll direction of the jQuery News Ticker.', $post_domain ),		
											'display'		=> 'inline' 		
								);
								
	// Add the slide speed field
	$scroll_fields['scroll_speed'] = array(
											'id' 			=> '_clrdr_post_scroll_speed',
											'type' 			=> 'number',
											'name' 			=> __( 'Scroll speed', $post_domain ),
											'default' 		=> 0.10,																		
											'description' 	=> __( 'Set the speed of the scrolling data.', $post_domain )
									);
	
	//Add the pause on items
	$scroll_fields['pause_items'] = array(
											'id'			=> '_clrdr_post_pause_items',
											'type'			=> 'number',
											'name'			=> __( 'Pause On Items', $post_domain ),
											'default'		=> '2000',
											'description'	=> 'The pause on a news item before being replaced.'
									);
									
	//Add the fade speed on items
	$scroll_fields['fade_speed'] = array(
											'id'			=> '_clrdr_post_fade_speed',
											'type'			=> 'number',
											'name'			=> __( 'Fade Speed', $post_domain ),
											'before' 		=> __('Fade In Speed', $post_domain ),
											'default'		=> '600',
											'description'	=> 'Speed of fade animation.',											
											'append'		=> array(
																		'_clrdr_post_fadeout_speed'	=> array(
																												'type'		=> 'number',
																												'default'	=> '300',
																												'before' 	=> __('Fade Out Speed', $post_domain ),
																										)
																)
									);
	
	//Add the Title text field
	$scroll_fields['title_text'] = array(
								         	'id'			=> '_clrdr_post_titletext',
								         	'type'			=> 'text',
								         	'name'			=> __('Set Title Text', $post_domain ),
								         	'default'		=> 'Latest News',
								         	'description'	=> __( 'Add the title text to be displayed before the news. To remove the title set this to an empty String.', $post_domain ),
								         	'size' 			=> '20',
									);
	
	//Add the control buttons field
	$scroll_fields['controls'] = array(
											'id'			=> '_clrdr_post_controls',
											'type'			=> 'radio',
											'name'			=> __('Show Control Buttons', $post_domain ),
											'options'		=> array(
														   				'true'	=> __( 'Yes', $post_domain ),
														   				'false'	=> __( 'No', $post_domain )
														   	   ),
											'default'		=> 'false',
											'description'	=> __( 'Whether or not to show the jQuery News Ticker controls', $post_domain ),
											'display'		=> 'inline'
								);

	// Add the slide spacing field
	$scroll_fields['display_type'] = array(
											'id' 			=> '_clrdr_post_display_type',
											'type' 			=> 'select',
											'name' 			=> __( 'Display Type', $post_domain ),
											'default' 		=> 'reveal',											
											'description' 	=> __( 'Set the animation type.', $post_domain ),
											'options' 		=> array( 'reveal', 'fade' )
									);
	
	// Create the metabox
	$clrdr_post_scroll_settings = array(
											'id' 		=> 'clrdr_post_scroll',
											'title' 	=> __( 'Scroll Settings', $post_domain ),
											'context' 	=> 'normal',
											'priority' 	=> 'high',
											'fields' 	=> apply_filters( 'clrdr_post_scroll', $scroll_fields )
								  );	
		
	$latest_news->meta_box( $clrdr_post_scroll_settings );
	
	// Create an array to store the fields
	$display_fields = array();

	// Add the shortcode field
	$display_fields['shortcode'] = array(
											'id' 			=> '_clrdr_post_shortcode',
											'type' 			=> 'code',
											'name' 			=> __( 'Shortcode', $post_domain ),
											'shortcode'		=> 'jquery_latest_news_ticker',
											'button' 		=> __( 'Select Shortcode', $post_domain ),
											'description' 	=> __( 'Use this shortcode to insert the jQuery News Ticker into a post/page.', $post_domain ),
									);
	
	// Create the metabox
	$clrdr_post_display = array(
									'id' 		=> 'clrdr_post_display',
									'title' 	=> __( 'Ticker Display', $post_domain ),
									'context' 	=> 'side',
									'priority' 	=> 'default',
									'fields' 	=> apply_filters('clrdr_post_display_fields', $display_fields)
							);
	
	$latest_news->meta_box( $clrdr_post_display );