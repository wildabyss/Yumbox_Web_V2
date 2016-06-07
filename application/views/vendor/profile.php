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
		<h1 class="title editable-full"><a id="edit_user_name" data-type="text" data-onblur="ignore"><?php echo prevent_xss($user->name)?></a></h1>
		<?php if (!$user->is_open):?>
		<h3 class="is_closed btn_kitchen_open <?php if ($is_my_profile):?>my_profile<?php endif?>">KITCHEN CLOSED</h3>
		<?php else:?>
		<h3 class="is_open btn_kitchen_open <?php if ($is_my_profile):?>my_profile<?php endif?>">KITCHEN OPEN</h3>
		<?php endif?>
		
		<?php if ($is_my_profile):?>
		<h3>EMAIL</h3>
		<p class="editable-full"><a id="edit_user_email" data-type="text" data-onblur="ignore"><?php echo prevent_xss($user->email)?></a></p>
		
		<h3>ADDRESS</h3>
		<p class="editable-full address_container"><a id="edit_user_addr" data-type="address" data-onblur="ignore"></a></p>

		<h3>FINANCIAL INFORMATION</h3>
		<p class="editable-full financial_info_container"><a id="account_info" data-type="stripe_account" data-onblur="ignore"></a></p>
		<?php endif?>
		
		<?php if (!$is_my_profile && $user->descr != "" || $is_my_profile):?>
		<h3>ABOUT ME</h3>
		<p class="editable-full"><a id="edit_user_descr" data-type="textarea" data-onblur="ignore"><?php echo prevent_xss($user->descr)?></a></p>
		<?php endif?>
		
		<?php if ($is_my_profile):?>
		<h3>REGULAR PICKUP TIME</h3>
		<div id="pickup_buttonset">
			<div class="pickup_item explore">
				<input type="checkbox" id="pickup_mon" weekday="mon" <?php if ($user->pickup_mon["enable"]):?>checked<?php endif?> name="pickup_weekday"/><label for="pickup_mon">Monday</label>
				<a class="edit_user_time" weekday="mon" <?php if (!$user->pickup_mon["enable"]):?>style="display:none"<?php endif?> data-type="combodate" data-onblur="ignore">
					<?php echo date("H:i", strtotime($user->pickup_mon["time"]))?>
				</a>
			</div>
			<div class="pickup_item explore">
				<input type="checkbox" id="pickup_tue" weekday="tue" <?php if ($user->pickup_tue["enable"]):?>checked<?php endif?> name="pickup_weekday"/><label for="pickup_tue">Tuesday</label>
				<a class="edit_user_time" weekday="tue" <?php if (!$user->pickup_tue["enable"]):?>style="display:none"<?php endif?> data-type="combodate" data-onblur="ignore">
					<?php echo date("H:i", strtotime($user->pickup_tue["time"]))?>
				</a>
			</div>
			<div class="pickup_item explore">
				<input type="checkbox" id="pickup_wed" weekday="wed" <?php if ($user->pickup_wed["enable"]):?>checked<?php endif?> name="pickup_weekday"/><label for="pickup_wed">Wednesday</label>
				<a class="edit_user_time" weekday="wed" <?php if (!$user->pickup_wed["enable"]):?>style="display:none"<?php endif?> data-type="combodate" data-onblur="ignore">
					<?php echo date("H:i", strtotime($user->pickup_wed["time"]))?>
				</a>
			</div>
			<div class="pickup_item explore">
				<input type="checkbox" id="pickup_thu" weekday="thu" <?php if ($user->pickup_thu["enable"]):?>checked<?php endif?> name="pickup_weekday"/><label for="pickup_thu">Thursday</label>
				<a class="edit_user_time" weekday="thu" <?php if (!$user->pickup_thu["enable"]):?>style="display:none"<?php endif?> data-type="combodate" data-onblur="ignore">
					<?php echo date("H:i", strtotime($user->pickup_thu["time"]))?>
				</a>
			</div>
			<div class="pickup_item explore">
				<input type="checkbox" id="pickup_fri" weekday="fri" <?php if ($user->pickup_fri["enable"]):?>checked<?php endif?> name="pickup_weekday"/><label for="pickup_fri">Friday</label>
				<a class="edit_user_time" weekday="fri" <?php if (!$user->pickup_fri["enable"]):?>style="display:none"<?php endif?> data-type="combodate" data-onblur="ignore">
					<?php echo date("H:i", strtotime($user->pickup_fri["time"]))?>
				</a>
			</div>
			<div class="pickup_item explore">
				<input type="checkbox" id="pickup_sat" weekday="sat" <?php if ($user->pickup_sat["enable"]):?>checked<?php endif?> name="pickup_weekday"/><label for="pickup_sat">Saturday</label>
				<a class="edit_user_time" weekday="sat" <?php if (!$user->pickup_sat["enable"]):?>style="display:none"<?php endif?> data-type="combodate" data-onblur="ignore">
					<?php echo date("H:i", strtotime($user->pickup_sat["time"]))?>
				</a>
			</div>
			<div class="pickup_item explore">
				<input type="checkbox" id="pickup_sun" weekday="sun" <?php if ($user->pickup_sun["enable"]):?>checked<?php endif?> name="pickup_weekday"/><label for="pickup_sun">Sunday</label>
				<a class="edit_user_time" weekday="sun" <?php if (!$user->pickup_sun["enable"]):?>style="display:none"<?php endif?> data-type="combodate" data-onblur="ignore">
					<?php echo date("H:i", strtotime($user->pickup_sun["time"]))?>
				</a>
			</div>
		</div>
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

<script src="/js/moment.min.js"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script src="/js/editable-address.js"></script>
<script src="/js/editable-stripe-account.js"></script>
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
		
		$(".btn_kitchen_open").click(function(){
			if ($(this).hasClass("is_open")){
				// set to close
				set_kitchen_status(0);
			} else if ($(this).hasClass("is_closed")) {
				// set to open
				set_kitchen_status(1);
			}
		});
		
		var set_kitchen_status = function(set_open){
			$.ajax({
				url:			'/vendor/profile/open_kitchen/'+set_open,
				data:			csrfData,
				type:			'post',
				error:		function(response){
					errorMessage("Unable to set kitchen");
				},
				success:		function(response){
					var respArr = $.parseJSON(response);
			
					if ("success" in respArr){
						if (set_open){
							successMessage("Kitchen is now open! Ready to take orders.");
							$(".btn_kitchen_open").removeClass("is_closed").addClass("is_open").text("KITCHEN OPEN");
						} else {
							successMessage("Kitchen is now closed!");
							$(".btn_kitchen_open").removeClass("is_open").addClass("is_closed").text("KITCHEN CLOSED");
						}
					} else {
						errorMessage(respArr["error"]);
					}
				}
			});
		}
		
		$('#pickup_buttonset').buttonset({
			items: "input[type=checkbox]"
		});
		
		var set_pickup_time = function(weekday, time){
			var inputs = $.extend({}, csrfData);
			inputs["weekday"] = weekday;
			inputs["time"] = time;
			
			$.ajax({
				url:			'/vendor/profile/change_pickuptime',
				data:			inputs,
				type:			'post',
				error:		function(response){
					errorMessage("Unable to change time");
				},
				success:		function(response){
					var respArr = $.parseJSON(response);
			
					if ("success" in respArr){
						successMessage("Saved");
					} else {
						errorMessage(respArr["error"]);
						return respArr["error"];
					}
				}
			});
		}

		$("input[type=checkbox][name=pickup_weekday]").change(function(){
			var weekday = $(this).attr("weekday");
			var default_time;
			
			if ($(this).is(":checked")){
				default_time = "20:00";
				$(".edit_user_time[weekday="+weekday+"]").show();
			} else {
				default_time = "00:00";
				$(".edit_user_time[weekday="+weekday+"]").hide();
			}
			
			set_pickup_time(weekday, default_time);
			$(".edit_user_time[weekday="+weekday+"]").editable("setValue", default_time, true);
		});
		
		$(".edit_user_time").editable({
			format: "HH:mm",
			template: "HH:mm",
			combodate:{
				minuteStep: 15,
				firstItem: 'none'
			},
			validate:	function(time){
				var weekday = $(this).attr("weekday");
				// get time
				time = new Date(time);
				time = time.getHours()+":"+time.getMinutes();
				set_pickup_time(weekday, time);
			}
		});

		// set Stripe public key
		Stripe.setPublishableKey('<?php echo $this->config->item("stripe_public_key")?>');

		$("#account_info").editable({
			// Using a function gives us the opportunity to verify form fields with Ajax-Async
			url:		function(params) {
				var d = new $.Deferred();

				// This function is called once both Ajax-Async verifications are approved
				var success = function() {
					var inputs = $.extend({
						type: params.value.type,
						country: params.value.country,
						address_country: params.value.country,
						state: params.value.state,
						city: params.value.city,
						line_1: params.value.line_1,
						line_2: params.value.line_2,
						postal_code: params.value.postal_code,

						first_name: params.value.first_name,
						last_name: params.value.last_name,
						email: params.value.email,
						year: params.value.year,
						month: params.value.month,
						day: params.value.day,

						bank_account_token_id: params.value.bank_account_token_id,
						pii_token_id: params.value.pii_token_id
					}, csrfData);

					$.ajax({
						type: 		"post",
						url: 		"/vendor/profile/stripe_account",
						data:		inputs,
						success:	function(data){
							var respArr = $.parseJSON(data);
							if ("success" in respArr){
								d.resolve(data);
							} else {
								// error
								d.reject(respArr["error"]);
							}
						},
						error: 		function(){
							d.reject("Unable to process");
						}
					});
				};

				// Creating a token based on bank account info
				Stripe.bankAccount.createToken({
					country: params.value.bank_country,
					currency: params.value.currency,
					routing_number: params.value.routing_number,
					account_number: params.value.account_number,
					account_holder_name: params.value.account_holder_name,
					account_holder_type: params.value.account_holder_type
				}, function(status, response) {
					if (response.error) {
						// Show the errors
						d.reject(response.error.message);
					} else { // Token created
						params.value.bank_account_token_id = response.id;
						// Check and see if this is the second callback called
						if (params.value.pii_token_id) {
							success();
						}
					}

				});

				// Creating a token based on SIN number
				Stripe.piiData.createToken({
					personal_id_number: params.value.pii
				}, function(status, response) {
					if (response.error) {
						// Show the errors
						d.reject(response.error.message);

					} else { // Token created
						params.value.pii_token_id = response.id;
						// Check and see if this is the second callback called
						if (params.value.bank_account_token_id) {
							success();
						}
					}

				});

				// Returning a `promise` means even though the function is returned, but process is
				//waiting for .reject or .resolve to carry on.
				return d.promise();
			},
			value:		{
				country: html_decode("<?php echo $stripe_account['country']?>"),
				email: html_decode("<?php echo $stripe_account['email']?>"),
				day: html_decode("<?php echo $stripe_account['day']?>"),
				month: html_decode("<?php echo $stripe_account['month']?>"),
				year: html_decode("<?php echo $stripe_account['year']?>"),
				first_name: html_decode("<?php echo $stripe_account['first_name']?>"),
				last_name: html_decode("<?php echo $stripe_account['last_name']?>"),
				type: html_decode("<?php echo $stripe_account['type']?>"),

				address_country: html_decode("<?php echo $stripe_account['address_country']?>"),
				state: html_decode("<?php echo $stripe_account['state']?>"),
				city: html_decode("<?php echo $stripe_account['city']?>"),
				line_1: html_decode("<?php echo $stripe_account['line_1']?>"),
				line_2: html_decode("<?php echo $stripe_account['line_2']?>"),
				postal_code: html_decode("<?php echo $stripe_account['postal_code']?>"),

				bank_country: html_decode("<?php echo $stripe_account['bank_country']?>"),
				currency: html_decode("<?php echo $stripe_account['currency']?>"),
				account_holder_type: html_decode("<?php echo $stripe_account['account_holder_type']?>"),
				routing_number: html_decode("<?php echo $stripe_account['routing_number']?>"),
				account_holder_name: html_decode("<?php echo $stripe_account['account_holder_name']?>"),

				has_account: <?php echo $stripe_account['has_account'] ? 'true' : 'false'?>,
				charges_enabled: <?php echo $stripe_account['charges_enabled'] ? 'true' : 'false'?>,
				transfers_enabled: <?php echo $stripe_account['transfers_enabled'] ? 'true' : 'false'?>,
				representer_verification: "<?php echo $stripe_account['representer_verification']?>",
				bank_account_verification: "<?php echo $stripe_account['bank_account_verification']?>"
			},
			send:		"always",
			params:		csrfData,
			error:		function(response){
				errorMessage(response);
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
				value.type = $.trim(value.type);
				value.country = $.trim(value.country);
				value.state = $.trim(value.state);
				value.city = $.trim(value.city);
				value.line_1 = $.trim(value.line_1);
				value.line_2 = $.trim(value.line_2);
				value.postal_code = $.trim(value.postal_code);

				value.first_name = $.trim(value.first_name);
				value.last_name = $.trim(value.last_name);
				value.email = $.trim(value.email);
				value.year = parseInt($.trim(value.year));
				value.month = parseInt($.trim(value.month));
				value.day = parseInt($.trim(value.day));

				value.pii = $.trim(value.pii);

				value.bank_country = $.trim(value.bank_country);
				value.currency = $.trim(value.currency);
				value.account_holder_type = $.trim(value.account_holder_type);
				value.routing_number = $.trim(value.routing_number);
				value.account_number = $.trim(value.account_number);
				value.account_holder_name = $.trim(value.account_holder_name);

				// Checking for incomplete form
				var error = false;
				// Mapping from field names to their labels to show to user in case of error
				var mandatory_field_labels = {
					type: "Account type",
					country: "Country",
					state: "Province / State",
					city: "City",
					line_1 : "Line 1",
					postal_code: "Postal Code",

					first_name: "First name",
					last_name: "Last name",
					email: "Email",

					pii: "SIN number",

					bank_country: "Bank's country",
					currency: "Currency",
					account_holder_type: "Account holder type",
					routing_number: "Routing number",
					account_number: "Account number",
					account_holder_name: "Account holder name"
				};
				for (var key in value) {
					switch (key) {
						case "day":
							if (isNaN(value.day) || value.day < 1 || value.day > 31) {
								error = "Invalid day value";
							}
							break;

						case "month":
							if (isNaN(value.month) || value.month < 1 || value.month > 12) {
								error = "Invalid month value";
							}
							break;

						case "year":
							if (isNaN(value.year) || value.year < 1900 || value.year > 2100) {
								error = "Invalid year value";
							}
							break;

						default:
							if (!value[key] && mandatory_field_labels[key]) {
								error = "All fields are mandatory, please enter '" + mandatory_field_labels[key] + "'";
							}
							break;
					}
					if (error !== false) {
						break;
					}
				}
				if (error !== false){
					errorMessage(error);
					return "error";
				}

				return { newValue: value };
			}
		});
		<?php endif?>
	});
</script>