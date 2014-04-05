<?php

if( !class_exists('ColorDrift_Post_Type') ) {
	
	class ColourDrift_Post_Type 
	{
		private $post_type_name;
		private $post_type_args;
		private $post_type_labels;	
		private $post_domain;	
		
		private $updated_message;
				
		public function __construct( $name, $args = array(), $labels = array(), $updated_message=NULL )
		{
									
			// Set some important variables
			$this->post_type_name		= uglify($name);
			$this->post_type_args 		= $args;
			$this->post_type_labels 	= $labels;	
			$this->post_domain 			= 'clrdr-'.strtolower( str_replace( ' ', '-', $name ) );				
			
			// Add action to register the post type, if the post type doesnt exist
			if( ! post_type_exists( $this->post_type_name ) )
			{
				add_action( 'init', array( $this, 'register_post_type' ) );
				
				if( $updated_message != NULL )
				{
					$this->updated_message = $updated_message;
					add_filter( 'post_updated_messages', array($this, 'post_updated_messages') );
				}
			}
			
			// Listen for the save post hook  
			self::save();		
		}
		
		/* Method which registers the post type */
		public function register_post_type()
		{		
			//Capitilize the words and make it plural
			$name 		= beautify( $this->post_type_name );			
			$plural 	= pluralize( $name );

			// We set the default labels based on the post type name and plural. We overwrite them with the given labels.
			$labels = array_merge(

				// Default
				array(
					'name' 					=> _x( $plural, 'post type general name', $this->post_domain ),
					'singular_name' 		=> _x( $name, 'post type singular name', $this->post_domain ),
					'add_new' 				=> _x( 'Add New', strtolower( $name ), $this->post_domain ),
					'add_new_item' 			=> __( 'Add New ' . $name, $this->post_domain ),
					'edit_item' 			=> __( 'Edit ' . $name, $this->post_domain ),
					'new_item' 				=> __( 'New ' . $name, $this->post_domain ),
					'all_items' 			=> __( 'All ' . $plural, $this->post_domain ),
					'view_item' 			=> __( 'View ' . $name, $this->post_domain ),
					'search_items' 			=> __( 'Search ' . $plural, $this->post_domain ),
					'not_found' 			=> __( 'No ' . strtolower( $plural ) . ' found', $this->post_domain ),
					'not_found_in_trash' 	=> __( 'No ' . strtolower( $plural ) . ' found in Trash', $this->post_domain ), 
					'parent_item_colon' 	=> '',
					'menu_name' 			=> $plural
				),

				// Given labels
				$this->post_type_labels

			);			
			
			// Same principle as the labels. We set some default and overwite them with the given arguments.
			$args = array_merge(

				// Default
				array(
					'label' 				=> $plural,
					'labels' 				=> $labels,
					'public' 				=> false,
					'publicly_queryable' 	=> true,
					'exclude_from_search' 	=> true,
					'show_ui' 				=> true,
					'supports' 				=> array( 'title', 'editor' ),
					'show_in_nav_menus' 	=> true,
					'_builtin' 				=> false,
					'query_var' 			=> true,
					'rewrite' 				=> true,
					'show_in_nav_menus' 	=> false
				),

				// Given args
				$this->post_type_args

			);
			
			// Register the post type
			register_post_type( $this->post_type_name, $args );
		}

		/* Method to change update message with custom message */
		public function post_updated_messages( $messages )
		{
			 $messages[$this->post_type_name][1] = __( $this->updated_message, $this->post_domain );

  			return $messages;
		}	
		
		/* Method to attach the taxonomy to the post type */
		public function add_taxonomy( $name, $args = array(), $labels = array() )
		{
			if( ! empty( $name ) )
			{			
				// We need to know the post type name, so the new taxonomy can be attached to it.
				$post_type_name = $this->post_type_name;

				// Taxonomy properties
				$taxonomy_name		= uglify( $name );
				$taxonomy_labels	= $labels;
				$taxonomy_args		= $args;

				if( ! taxonomy_exists( $taxonomy_name ) )
					{
						//Capitilize the words and make it plural
							$name 		= beautify( $name );
							$plural 	= pluralize( $name );

							// Default labels, overwrite them with the given labels.
							$labels = array_merge(

								// Default
								array(
									'name' 					=> _x( $plural, 'taxonomy general name', $this->post_domain ),
									'singular_name' 		=> _x( $name, 'taxonomy singular name', $this->post_domain ),
								    'search_items' 			=> __( 'Search ' . $plural, $this->post_domain ),
								    'all_items' 			=> __( 'All ' . $plural, $this->post_domain ),
								    'parent_item' 			=> __( 'Parent ' . $name, $this->post_domain ),
								    'parent_item_colon' 	=> __( 'Parent ' . $name . ':', $this->post_domain ),
								    'edit_item' 			=> __( 'Edit ' . $name, $this->post_domain ), 
								    'update_item' 			=> __( 'Update ' . $name, $this->post_domain ),
								    'add_new_item' 			=> __( 'Add New ' . $name, $this->post_domain ),
								    'new_item_name' 		=> __( 'New ' . $name . ' Name', $this->post_domain ),
								    'menu_name' 			=> __( $name, $this->post_domain ),
								),

								// Given labels
								$taxonomy_labels

							);

							// Default arguments, overwitten with the given arguments
							$args = array_merge(

								// Default
								array(
									'label'					=> $plural,
									'labels'				=> $labels,
									'public' 				=> true,
									'show_ui' 				=> true,									
									'show_in_nav_menus' 	=> true,
									'_builtin' 				=> false,
								),

								// Given
								$taxonomy_args

							);

							// Add the taxonomy to the post type
							add_action( 'init',
								function() use( $taxonomy_name, $post_type_name, $args )
								{						
									register_taxonomy( $taxonomy_name, $post_type_name, $args );
								}
							);
					}
					else
					{
						add_action( 'init',
								function() use( $taxonomy_name, $post_type_name )
								{				
									register_taxonomy_for_object_type( $taxonomy_name, $post_type_name );
								}
							);
					}
			}
		}
		
		public function meta_box( $meta_box = array() )
		{
			global $metabox_data;
			global $metabox_fields;			
			
			$post_type_name = $this->post_type_name;
			$post_domain 	= $this->post_domain;			
			
			$metabox_data 										= $meta_box;
			$metabox_fields[uglify( $metabox_data['title'] )] 	= $metabox_data['fields'];  		
			
			$field_id_name 	= $this->post_type_name . '_' . uglify( $metabox_data['title'] );
			
			$metabox_data = array_merge( $metabox_data, array( 'id'=>$field_id_name ) );
			
			$box_id 		= $metabox_data['id'];
			$box_title 		= $metabox_data['title'];
			$box_context 	= $metabox_data['context'];
			$box_priority 	= $metabox_data['priority'];
			$fields 		= $metabox_fields[uglify( $metabox_data['title'] )];
								
			add_action( 'admin_init',
							function() use( $box_id, $box_title, $post_type_name, $box_context, $box_priority, $fields, $post_domain  )
							{								
								add_meta_box(
									$box_id,
									$box_title,
									function( $post, $fields )
									{										
										global $post;											
									?>
										<table style="width:100%;" class="clrdr-post-metabox-admin-fields">
											<tbody>
												<?php 
										      		foreach( $fields['args']['fields'] as $field ) 
										      		{		
														if ( isset( $field['id'] ) ) 
														{
															// Create a nonce field
															echo'<input type="hidden" name="'.$field['id'].'_noncename"  id="'.$field['id'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
														}	
																											
														// Output the field
														metabox_container( $field, $fields['args']['box_context'], $fields['args']['post_domain'] );
													}
												?>
											</tbody>
										</table>									
										
									<?php
									},
									$post_type_name,
									$box_context,
									$box_priority,
									array('fields' => $fields, 'box_context' => $box_context, 'post_domain' => $post_domain )
								);
							}
						);		
		}
		
		/* Method to save the post type */
		public function save() 
		{
			// Need the post type name again
			$post_type_name = $this->post_type_name;
			
			// Action to save the post
			add_action( 'save_post',
				function() use( $post_type_name )
				{
					global $post;
					global $metabox_fields;
									
					foreach( $metabox_fields as $title )
					{
						foreach( $title as $field )
						{		
							//Deny the wordpress autosave function
							if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
								return $post->ID;
							
							if ( isset($_POST[$field['id'].'_noncename']) ) 
							{
								//Verify the nonce and return if false
								if ( !wp_verify_nonce($_POST[$field['id'].'_noncename'], plugin_basename(__FILE__)) ) 
								{
									return  $post->ID;
								}
								
								if( isset( $_POST ) && isset( $post->ID ) && get_post_type( $post->ID ) == $post_type_name && current_user_can( 'edit_page', $post->ID ) && current_user_can( 'edit_post', $post->ID ) )
								{
												
									// Store the user data or set as empty string
									$data = ( isset($_POST[$field['id']]) ) ? $_POST[$field['id']] : '';					
														
									//update_post_meta( $post->ID, $field['id'], $data );	
																
									update_meta_data( $post->ID, $field['id'], $field['type'], $data );
									
									// Save appended fields
									save_appended( $post->ID, $field );
									
									// Save row fields
									save_rows( $post->ID, $field );
									
								}
							}
						}
					}
				}
			);			
		}	
		
	} // end of Class
	
	/* Method to update individual field */
		function update_meta_data( $post_id, $id, $type, $data ) 
		{  	
		  	// Update the post meta
		  	update_post_meta( $post_id, $id, $data );		 
			
		}   
		
		/* Method to update row fields */
		function save_rows( $post_id, $field ) 
		{			
			if( isset($field['rows']) ) 
			{
				foreach( $field['rows'] as $id => $row ) 
				{	
					$row_id = $row['id'];
						
					// Store the user data or set as empty string
					$data = ( isset($_POST[$row_id]) ) ? $_POST[$row_id] : '';
					
					// Update the meta
					$this->update_meta_data( $post_id, $row_id, $row['type'], $data );
					
					// Save appended fields
					$this->save_appended( $post_id, $row );
					
				}
			}
		}
		
		/* Method to update appended fields */
		function save_appended( $post_id, $field ) 
		{

			if( isset($field['append']) ) 
			{
				foreach( $field['append'] as $id => $append ) 
				{
					// Store the user data or set as empty string
					$data = ( isset($_POST[$id]) ) ? $_POST[$id] : '';
					
					// Update the meta
					update_meta_data( $post_id, $id, $append['type'], $data );
				}
			}
		}
	
} // end of if condition