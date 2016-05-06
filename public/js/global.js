$(document).ready(function(){
	// global click action
	$('html').click(function() {
		// Hide wide screen user menu
		$('#user_menu').hide();
		$('#user_menu_trigger').removeClass("selected");
		
		// Hide small screen user menu
		$('#mobile_user_menu').hide();
		$('#mobile_user_menu_trigger').removeClass("selected");
	});
});

