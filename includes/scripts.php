<?php

	function admin_styles_scripts() 
	{					
		wp_register_style( 'custom-post-type', plugins_url( 'css/custom_post_admin.css', __FILE__ ) );
		wp_enqueue_style( 'custom-post-type' );
		
		wp_register_script( 'custom-post-type', plugins_url( 'js/custom_post_admin.js', __FILE__ ), array( 'jquery','jquery-ui-core','jquery-ui-sortable' ), CLRDR_LN_VERSION, true );
		wp_enqueue_script( 'custom-post-type' );
			
	}
	
	add_action('admin_enqueue_scripts', 'admin_styles_scripts');
	
	function frontend_styles_scripts()
	{
		wp_register_style( 'custom-post-type', CLRDR_LN_URL . '/assets/css/ticker-style.css', false, CLRDR_LN_VERSION );
		wp_enqueue_style( 'custom-post-type' );
		
		wp_register_script( 'custom-post-type', CLRDR_LN_URL . '/assets/js/jquery.ticker.js', array( 'jquery' ), CLRDR_LN_VERSION, true );
		wp_enqueue_script( 'custom-post-type' );
	} 
	
	add_action( 'wp_enqueue_scripts', 'frontend_styles_scripts' );
	
	