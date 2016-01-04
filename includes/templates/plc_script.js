// js file for palace-law-calc plugin

jQuery(function ($) {

	$(".injury_block .delete_injury").first().hide(); //hide delete link on first element

	//add new injury set
	$("#add_injury").click(function(event) {
		event.preventDefault();
		// .hide() necessary for slideDown effect
		// .clone(true) clones the event handlers as well
		$(".injury_block").first().clone(true).hide().appendTo(".injury_list").slideDown('slow');
		$(".injury_block .delete_injury").last().show(); //show delete link on subsequent elements
	});

	//delete injury set
	$(".delete_injury").click(function(event) {
		event.preventDefault();
		if($(".injury_block").length > 1) //dont remove last element
			$(this).parent().remove();
	});


});

