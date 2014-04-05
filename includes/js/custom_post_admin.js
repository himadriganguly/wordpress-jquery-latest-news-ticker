/**
 * @author Himadri Ganguly
 */
jQuery(document).ready( function($) {	
	
	$('.clrdr_datepicker').datepicker({ dateFormat: 'dd-mm-yy' });
	
	// List - set the list item order
	function metabox_lists_set_order( $list ) {
	
		// Set the order of the items
		$list.find('.clrdr-post-metabox-list-item').each( function(i) {
	
			$(this).find('.clrdr-post-metabox-list-structure-item').each( function(e) {
	
				var base = $(this).attr('base');
				var field = $(this).attr('field');
				$(this).find('input,textarea,select').attr('name', base+'['+i+']['+field+']');
				$(this).find('input,textarea,select').attr('id', base+'['+i+']['+field+']');
			});
		});	
				
		// Hide the delete if only one element
		if( $list.find('.clrdr-post-metabox-list-item').length == 1 ) {
	
			$list.find('.clrdr-post-metabox-list-item-handle,.clrdr-post-metabox-list-item-delete').hide();
		}
	}
	
	// List - add sorting to the items
	function metabox_lists_set_sortable( $list ) {
	
		$list.sortable( {
			handle: '.clrdr-post-metabox-list-item-handle',
			items: '.clrdr-post-metabox-list-item',
			axis: 'y',
			cursor: 'move',
			opacity: 0.6,  
			helper: function(e, tr) {
		    var $originals = tr.children();
		    var $helper = tr.clone();
		    $helper.children().each(function(index) {
		      // Set helper cell sizes to match the original sizes
		      $(this).width($originals.eq(index).width())
		    });
		    return $helper;
		  },
		  update: function( event, ui ) {
	
				// Set the field order
				metabox_lists_set_order( $(this) );
			}
		});
	}
	
	// List -  handle hover
	$('.clrdr-post-metabox-list-item-handle').live( 'hover', function() {
		metabox_lists_set_sortable( $(this).parents('.clrdr-post-metabox-list') );
	});

	// List - add item click
	$('.clrdr-post-metabox-list-item-add').on( 'click', function(e) {
		e.preventDefault();
		
		var $parent = $(this).parents('.clrdr-post-metabox-list-item');
		var $new 	= $parent.clone(true).hide();
		
		$new.find('input,textarea,select').removeAttr('value').removeAttr('checked').removeAttr('selected');
		$parent.after($new);
		$new.fadeIn().css('display', 'table-row');
				
		// Set the field order
		metabox_lists_set_order( $(this).parents('.clrdr-post-metabox-list') );	
		$new.find('.clrdr_datepicker').removeClass('hasDatepicker');	
		$('.clrdr_datepicker').datepicker({ dateFormat: 'dd-mm-yy' });
		
		// Show the handles
		$(this).parents('.clrdr-post-metabox-list').find('.clrdr-post-metabox-list-item-handle,.clrdr-post-metabox-list-item-delete').show();
		
		// Set the focus to the new input
		var inputs = $new.find('input,textarea,select');
		$(inputs[0]).focus();
		
	});
	
	// List - delete item click
	$('.clrdr-post-metabox-list-item-delete').on( 'click', function(e) {
		e.preventDefault();
	
		// Fade out the item
		$(this).parents('.clrdr-post-metabox-list-item').fadeOut( function() {
	
			// Get the list
			var $list = $(this).parents('.clrdr-post-metabox-list');
	
			// Remove the item
			$(this).remove();
	
			// Set the field order
			metabox_lists_set_order( $list );
		});
	});	
	
});