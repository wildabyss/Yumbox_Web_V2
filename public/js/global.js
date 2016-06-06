// Set X-editable to inline mode
$.fn.editable.defaults.mode = 'inline';


// Shows mobile side menu
var showMobileMenu = function(){
	$('#mobile_user_menu').show("slide", {direction:"left"},300);
	$('#haze').show();
	disableScroll();
};
// Hides mobile side menu
var hideMobileMenu = function(){
	$('#mobile_user_menu').hide("slide", {direction:"left"},300);
	$('#haze').hide();
	restoreScroll();
};


// shows dropdown user menu in desktop view
var showUserMenu = function(){
	$('#user_menu').slideDown(300);
	$('#user_menu_trigger').addClass("selected");
};
// hides dropdown user menu in desktop view
var hideUserMenu = function(){
	$('#user_menu').slideUp(300);
	$('#user_menu_trigger').removeClass("selected");
	$('#haze').hide();
};


// disable page scrolling
var disableScroll = function(){
	$('html, body').css({
		'overflow': 'hidden',
		'height': '100%'
	});
};
// enable page scrolling
var restoreScroll = function(){
	$('html, body').css({
		'overflow': 'auto',
		'height': 'auto'
	});
};


// html encode and decode, relies on jquery
var html_encode = function(value){
	return $('<div>').text(value).html();
}
var html_decode = function(value){
	return $('<div>').html(value).text();
}


// Display success message at top of screen
// Auto dissappear in 1.5 sec
var successMessage = function(msg){
	$("#top_status")
		.clearQueue()
		.attr("class", "success")
		.html(msg)
		.fadeIn(300)
		.delay(1500)
		.fadeOut(300);
};
// Display warning message at top of screen
// Auto dissappear in 4 sec
var errorMessage = function(msg){
	$("#top_status")
		.stop()
		.attr("class", "warning")
		.html(msg)
		.fadeIn(300)
		.delay(4000)
		.fadeOut(300);
};
// Display status (non-colored) message at top of screen
// The message persists until statusMessageOff is called
var statusMessageOn = function(msg){
	$("#top_status")
		.stop()
		.attr("class", "")
		.html(msg)
		.fadeIn(300)
};
var statusMessageOff = function(){
	$("#top_status")
		.stop()
		.fadeOut(300)
};

// A helper function to prevent event attacks
var throttle = function(fn, threshhold, scope) {
    threshhold || (threshhold = 250);
    var last,
        deferTimer;
    return function () {
        var context = scope || this;

        var now = +new Date,
            args = arguments;
        if (last && now < last + threshhold) {
            // hold on to it
            clearTimeout(deferTimer);
            deferTimer = setTimeout(function () {
                last = now;
                fn.apply(context, args);
            }, threshhold);
        } else {
            last = now;
            fn.apply(context, args);
        }
    };
};


$(document).ready(function(){
	// global click action
	$('html').click(function() {
		hideUserMenu();
		hideMobileMenu();
	});
	
	// textarea autosize
	autosize($('textarea'));
});

