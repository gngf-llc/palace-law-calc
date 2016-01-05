// js file for palace-law-calc plugin

jQuery(function ($) {

	//add first injury set
	if($('.injury_block').length == 0) {
		$('#injury_block_template').cloneTemplate().show().prependTo('.injury_list');
	}

	//add new injury set
	$('#add_injury').click(function(event) {
		event.preventDefault();
		$('#injury_block_template').cloneTemplate().appendTo('.injury_list').slideDown('slow');
	});

	//delete injury set
	$('.delete_injury').click(function(event) {
		event.preventDefault();
		if($('.injury_block').length > 1) //dont remove last set
			$(this).parent().remove();
	});

	//get ratings options from db depending on injury
	$('.injury_select').change( function() {
		jQuery.ajax({
			context : this,
			url : plc_ajax_url.ajax_url,
			type : 'post',
			data : {
				action : 'get_injury_rating_options',
				injury_id : 'blar'
			},
			success : function( response ) {
				$(this).parent().siblings('.rating_select_block').html( response );
			}
		});
	});


});

jQuery.fn.cloneTemplate= function() {
	// .clone(true) clones the event handlers as well
	return this.clone(true).addClass('injury_block').removeAttr('id');
}
