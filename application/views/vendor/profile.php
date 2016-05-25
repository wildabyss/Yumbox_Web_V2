<script src="/js/editable-address.js"></script>
<section id="profile_intro">
	<div class="pic_wrapper">
		<a id="profile_pic"></a>
		<p>
			<a id="followers"><?php echo $num_followers?> followers</a>
			<?php if ($my_id !== false  && !$is_my_profile):?>
				<a id="follow_btn">+ Follow</a>
			<?php endif?>
		</p>
	</div>
	<div class="intro_wrapper">
		<h1 class="title"><a id="edit_user_name" data-type="text" data-onblur="ignore"><?php echo prevent_xss($user->name)?></a></h1>
		<?php if (!$user->is_open):?>
		<h3 class="is_closed">KITCHEN CLOSED</h3>
		<?php else:?>
		<h3 class="is_open">KITCHEN OPEN</h3>
		<?php endif?>
		
		<?php if ($is_my_profile):?>
		<h3>EMAIL</h3>
		<p><a id="edit_user_email" data-type="text" data-onblur="ignore"><?php echo prevent_xss($user->email)?></a></p>
		
		<h3>ADDRESS</h3>
		<p><a id="edit_user_addr" data-type="address" data-onblur="ignore">
			<?php if ($user->address != ""):?>
			<?php echo prevent_xss($user->address)?><br/>
			<?php echo prevent_xss($user->city)?>, <?php echo prevent_xss($user->province)?>, <?php echo prevent_xss($user->country)?><br/>
			<?php echo prevent_xss($user->postal_code)?>
			<?php endif?>
		</a></p>
		<?php endif?>
		
		<?php if (!$is_my_profile && $user->descr != "" || $is_my_profile):?>
		<h3>ABOUT ME</h3>
		<p class="edit_descr_container"><a id="edit_user_descr" data-type="textarea" data-onblur="ignore"><?php echo prevent_xss($user->descr)?></a></p>
		<?php endif?>
	</div>
</section>

<section id="menu_listing">
	<?php if (count($foods)==0):?>
	<div id="no_food_info">
		<?php if ($is_my_profile):?>
		<p class="center">You are currently not selling any dishes.<br/>List your secret dishes and become a home chef today!</p>
		<?php else:?>
		<p class="center">This kitchen is empty.</p>
		<?php endif?>
	</div>
	<?php endif?>

	<?php echo $food_list_display?>
</section>

<script>
	$("#btn_add_new").click(function(e){
		
	});

	$(document).ready(function(){
		<?php if ($is_my_profile):?>
		$("#edit_user_name").editable({
			url:		"/vendor/profile/change_username",
			send:		"always",
			params:		csrfData,
			error:		function(response){
				errorMessage("Unable to process");
			},
			success:	function(response){
				var respArr = $.parseJSON(response);
				
				if ("success" in respArr){
					successMessage("Saved");
				} else {
					errorMessage(respArr["error"]);
					return respArr["error"];
				}
			},
			validate:	function(value){
				if ($.trim(value) == ""){
					errorMessage("Cannot be blank");
					return "cannot be blank";
				}
			}
		});
		
		$("#edit_user_email").editable({
			url:		"/vendor/profile/change_email",
			send:		"always",
			params:		csrfData,
			error:		function(response){
				errorMessage("Unable to process");
			},
			success:	function(response){
				var respArr = $.parseJSON(response);
				
				if ("success" in respArr){
					successMessage("Saved");
				} else {
					errorMessage(respArr["error"]);
					return respArr["error"];
				}
			},
			validate:	function(value){
				if ($.trim(value) == ""){
					errorMessage("Cannot be blank");
					return "cannot be blank";
				}
			}
		});
		
		$("#edit_user_addr").editable({
			url:		"/vendor/profile/change_address",
			value:		{
				 city: "Moscow", 
            street: "Lenina", 
            building: "12"
			},
			send:		"always",
			params:		csrfData,
			error:		function(response){
				errorMessage("Unable to process");
			},
			success:	function(response){
				var respArr = $.parseJSON(response);
				
				if ("success" in respArr){
					successMessage("Saved");
				} else {
					errorMessage(respArr["error"]);
					return respArr["error"];
				}
			},
			display: function(value){
				if (!value){
					$(this).empty();
					return;
				}
				var html = '<b>' + $('<div>').text(value.city).html() + '</b>, ' + $('<div>').text(value.street).html() + ' st., bld. ' + $('<div>').text(value.building).html();
				$(this).html(html); 
				//$(this).html(value.address+"<br/>"+value.city+", "+value.province+", "+value.country+"<br/>"+value.postal_code);
			}
		});
		
		$("#edit_user_city").editable();
		$("#edit_user_province").editable();
		$("#edit_user_postal").editable();
		
		$("#edit_user_descr").editable({
			rows: 3
		});
		
		<?php endif?>
	});
</script>