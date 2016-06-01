<script src="/js/editable-address.js"></script>

<section id="profile_intro">
	<div class="pic_wrapper">
		<label for="input_profile_pic" id="profile_pic" <?php if ($is_my_profile):?>class="editable_pic"<?php endif?>
			<?php if ($user_picture !== false):?>style="background-image:url('<?php echo $user_picture?>')"<?php endif?>>
			<?php if ($is_my_profile):?>
			<div class="btn_update_picture">Edit photo</div>
			<?php endif?>
		</label>
		<?php if ($is_my_profile):?>
		<input id="input_profile_pic" type="file" accept="image/*">
		<?php endif?>
		<p style="overflow:auto">
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
		<p><a id="edit_user_addr" data-type="address" data-onblur="ignore"></a></p>
		<?php endif?>
		
		<?php if (!$is_my_profile && $user->descr != "" || $is_my_profile):?>
		<h3>ABOUT ME</h3>
		<p><a id="edit_user_descr" data-type="textarea" data-onblur="ignore"><?php echo prevent_xss($user->descr)?></a></p>
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
	
	<?php if ($is_my_profile):?>
	<div id="dialog-confirm" title="Remove dish?">
		<p>Confirm remove this dish?</p>
	</div>
	<?php endif?>
</section>

<?php if ($is_my_profile):?>
<!-- modal for editing fod -->
<div id="food_modal_container">

</div>
<?php endif?>

<script>
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
				value = $.trim(value);
				
				if (value == ""){
					errorMessage("Cannot be blank");
					return "cannot be blank";
				}
				
				return {newValue: value};
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
				value = $.trim(value);
				
				if (value == ""){
					errorMessage("Cannot be blank");
					return "cannot be blank";
				}
				
				return {newValue: value};
			}
		});
		
		$("#edit_user_addr").editable({
			url:		"/vendor/profile/change_address",
			value:		{
				address:		html_decode("<?php echo prevent_xss($user->address)?>"),
				city: 			html_decode("<?php echo prevent_xss($user->city)?>"), 
				province:		html_decode("<?php echo prevent_xss($user->province)?>"), 
				country:		html_decode("<?php echo prevent_xss($user->country)?>"), 
				postal_code:	html_decode("<?php echo prevent_xss($user->postal_code)?>"), 
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
			validate:	function(value){
				value.address = $.trim(value.address);
				value.city = $.trim(value.city);
				value.province = $.trim(value.province);
				value.country = $.trim(value.country);
				value.postal_code = $.trim(value.postal_code);
				
				if (value.address == ""){
					errorMessage("Address cannot be empty");
					return "error";
				}
				
				return {newValue: value};
			}
		});
		
		$("#edit_user_descr").editable({
			rows: 		3,
			url:		"/vendor/profile/change_userdescr",
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
			}
		});
		
		$("#input_profile_pic").change(function(){
			var file = this.files[0];
			var size = file.size;
			var type = file.type;
			
			if (size > 10000000){
				errorMessage("Must be less than 10MB");
			} else if (type.indexOf("image/") != 0){
				errorMessage("Only an image is allowed");
			} else {
				// make formData to be submitted
				var formData = new FormData();
				formData.append('photo', file);
				$.each(csrfData, function(index, value){
					formData.append(index, value);
				});
				
				statusMessageOn("Uploading, please wait...");
				
				$.ajax({
					url:			'/vendor/profile/change_displaypic',
					data:			formData,
					type:			'post',
					processData:	false,
					contentType:	false,
					error:		function(response){
						errorMessage("Unable to process");
					},
					success:		function(response){
						var respArr = $.parseJSON(response);
				
						if ("success" in respArr){
							successMessage("Saved");
							
							// change picture
							$("#profile_pic").css("background-image", "url("+respArr["filepath"]+")");
						} else {
							errorMessage(respArr["error"]);
							return respArr["error"];
						}
					}
				});
			}
		});
		
		
		$("#dialog-confirm").dialog({
			autoOpen: false,
			modal: true,
			resizable: false,
			dialogClass: 'explore',
			height:140,
			buttons:[
				{
					icons: {
						primary: "ui-icon-check"
					},
					'class':	'ui-button-dialog',
					click:		function(){
						$.ajax({
							type: 		"post",
							url: 		"/vendor/food/remove_food/"+$("#dialog-confirm").data('food_id'),
							data:		csrfData,
							success:	function(data){
								var respArr = $.parseJSON(data);
								if ("success" in respArr){
									successMessage("Dish removed");
									$parent = $("#dialog-confirm").data('parent');
									$parent.remove();
								} else {
									// error
									errorMessage(respArr["error"]);
								}
							},
							error: 		function(){
								errorMessage("Unable to process");
							}
						});
						
						$(this).dialog("close");
					}
				},
				{
					icons: {
						primary: "ui-icon-closethick"
					},
					'class': 'ui-button-dialog',
					click: function(){
						$(this).dialog("close");
					}
				}
			]
		});
		<?php endif?>
	});
</script>