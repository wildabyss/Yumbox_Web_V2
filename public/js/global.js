var showMobileMenu = function(){
	$('#mobile_user_menu').show("slide", {direction:"left"},300);
	$('#haze').show();
	disableScroll();
};

var hideMobileMenu = function(){
	$('#mobile_user_menu').hide("slide", {direction:"left"},300);
	$('#haze').hide();
	restoreScroll();
};

var showUserMenu = function(){
	$('#user_menu').slideDown(300);
	$('#user_menu_trigger').addClass("selected");
};

var hideUserMenu = function(){
	$('#user_menu').slideUp(300);
	$('#user_menu_trigger').removeClass("selected");
	$('#haze').hide();
};

var disableScroll = function(){
	$('html, body').css({
		'overflow': 'hidden',
		'height': '100%'
	});
};

var restoreScroll = function(){
	$('html, body').css({
		'overflow': 'auto',
		'height': 'auto'
	});
};

var successMessage = function(msg){
	$("#top_status")
		.clearQueue()
		.attr("class", "success")
		.html(msg)
		.fadeIn(300)
		.delay(1500)
		.fadeOut(300);
};

var errorMessage = function(msg){
	$("#top_status")
		.stop()
		.attr("class", "warning")
		.html(msg)
		.fadeIn(300)
		.delay(4000)
		.fadeOut(300);
};

var statusMessageOn = function(msg){
	$("#top_status")
		.stop()
		.attr("class", "")
		.html(msg)
		.fadeIn(300)
}
var statusMessageOff = function(){
	$("#top_status")
		.stop()
		.fadeOut(300)
}

$(document).ready(function(){
	// global click action
	$('html').click(function() {
		hideUserMenu();
		hideMobileMenu();
	});
});

