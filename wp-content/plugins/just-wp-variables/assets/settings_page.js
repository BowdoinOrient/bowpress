jQuery(document).ready(function(){

	// init sortable
	jQuery('#jv_settings tbody').sortable({
		handle: 'span.drag-handle',
		opacity:0.7,
		placeholder: 'sortable_placeholder',
		start: function (event, ui) {
			ui.placeholder.html('<td colspan="6"><br>&nbsp;</td>');
		},
		stop: function(event, ui){
		}
	});
	
	// init theme variable add
	jQuery('#jv_settings input#jv_var_more').click(function(){
		var tbody = jQuery('#jv_settings tbody');
		tbody
			.append( tbody.find('tr:first').clone() )
			.find('tr:last').removeClass('new_row');
		return false;
	});
	
	// init delete button
	jQuery('#jv_settings a.delete_variable').live('click', function(){
		if( confirm( text_just_variables.confirm_delete ) ){
			jQuery(this).parents('tr:first').remove();
		}
		return false;
	});
	
});