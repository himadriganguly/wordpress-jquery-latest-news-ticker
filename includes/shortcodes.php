<?php

add_shortcode( 'jquery_latest_news_ticker', 'clrdr_ln_shortcode_display' );

function clrdr_ln_shortcode_display( $atts, $content=null ) 
{
	extract( shortcode_atts( array(
		'postid' => ''
	), $atts ) );
	
	// Remove the id & class before passing the atts
	unset( $atts['postid'] );
	
	return jquery_news_ticker( $postid, $atts );
	
}
