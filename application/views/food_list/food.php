<section id="food_detail">
	<h1 class="title center editable-full">
		<a id="input_name" data-type="text" data-onblur="ignore"><?php echo prevent_xss(strtoupper($food->food_name))?></a>
		<?php if ($is_my_profile || $food->food_alt_name != ""):?> | <a id="input_altname" data-type="text" data-onblur="ignore"><?php echo prevent_xss($food->food_alt_name)?></a><?php endif?>
	</h1>
	<h3 class="center"><a href="/vendor/profile/id/<?php echo $food->vendor_id?>"><?php echo prevent_xss($food->vendor_name)?></a></h3>
	
	<label for="input_food_pic" class="food_pic_container <?php if ($is_my_profile):?>editable_pic<?php endif?>">
		<a class="food_pic" food_id="<?php echo $food->food_id?>" <?php if ($food_picture !== false):?>style="background-image:url('<?php echo $food_picture->path?>')"<?php endif?>></a>
		<div class="btn_update_picture">Edit photo</div>
	</label>
	<?php if ($is_my_profile):?>
	<input id="input_food_pic" type="file" accept="image/*">
	<?php endif?>
	
	<div class="tight_cluster">
		<div class="order_info">
			<?php if (!$is_my_profile):?>
			<!-- display price and order button -->
			<h3 class="price">$<?php echo $food->food_price?></h3>
			<button class="action_button btn_add_order" <?php if (!$enable_order):?>disabled<?php endif?>>ADD ORDER +</button>
			<?php else:?>
			<!-- display price and rating -->
			<h3 class="price">$<a id="input_price" data-type="text" data-onblur="ignore"><?php echo $food->food_price?></a></h3>
			<button class="action_button btn_remove_order">Remove</button>
			<?php endif?>
		</div>
		
		<div class="order_info">
			<?php if (!$is_my_profile):?>
			<!-- display total historical orders, average rating, and prep time -->
			<p class="orders"><?php echo $food->total_orders?> orders</p>
			<?php if ($food->rating > 0):?>
			<p class="rating center"><span class="no_mobile">Average rating: </span>&hearts; <?php echo $food->rating?>%</p>
			<?php endif?>
			<p class="time right-align"><span class="no_mobile">Preparation time: </span><?php echo $food->prep_time?></p>
			<?php else:?>
			<!-- display total orders and current orders -->
			<p class="orders"><?php echo $food->total_orders?> total orders | <?php echo $unfilled_orders?> current orders</p>
			<p class="rating right-align"><span class="no_mobile">Average rating: </span>&hearts; <?php echo $food->rating?>%</p>
			<?php endif?>
		</div>
		
		<?php if (!$is_my_profile && $food->is_open):?>
		<h3 class="is_open">KITCHEN OPEN</h3>
		<?php elseif (!$is_my_profile):?>
		<h3 class="is_closed">KITCHEN CLOSED</h3>
		<?php endif?>
	</div>
	
	<?php if ($is_my_profile):?>
	<h2 class="title center">PREPARATION TIME</h2>
	<div>
		<p class="prep_time_container editable-full"><span class="prep_time_head">Time to prepare (hrs):</span><a id="edit_prep_time" data-type="text" data-onblur="ignore"><?php echo $food->prep_time?></a></p>
		<p>Pickup method:</p>
		<div id="prep_time_buttonset">
			<input type="radio" id="radio_immediate" name="pickup_method" <?php if ($food->pickup_method==Food_model::$PICKUP_ANYTIME):?>checked<?php endif?> class="prep_time_radio" value="immediate"/>
			<label for="radio_immediate">Immediate after prep</label>
			<input type="radio" id="radio_regular" name="pickup_method" <?php if ($food->pickup_method==Food_model::$PICKUP_DESIGNATED):?>checked<?php endif?> class="prep_time_radio" value="regular"/>
			<label for="radio_regular">Designated times</label>
		</div>
	</div>
	<?php endif?>
	
	<h2 class="title center">ABOUT THE DISH</h2>
	<div class="about_dish">
		<div class="about_dish_section">
			<h2>Description</h2>
			<p class="editable-full">
			<?php if (!$is_my_profile):?>
				<?php if ($food->descr == ""):?>
				N/A
				<?php else:?>
				<?php echo prevent_xss($food->descr)?>
				<?php endif?>
			<?php else:?>
				<a id="input_descr" data-type="textarea" data-rows="3" data-onblur="ignore"><?php echo prevent_xss($food->descr)?></a>
			<?php endif?>
			</p>
			
			<?php if (!$is_my_profile):?>
			<div class="categories_container">
				<?php foreach ($categories as $category):?>
				<a class="category_tag"><?php echo prevent_xss(ucfirst($category->name))?></a>
				<?php endforeach?>
			</div>
			<?php endif?>
		</div>
		
		<?php if ($is_my_profile):?>
		<div class="about_dish_section">
			<h2>Categories</h2>
			<ul id="input_category" class="explore">
				<?php foreach ($categories as $category):?>
				<li><?php echo prevent_xss(ucfirst($category->name))?></li>
				<?php endforeach?>
			</ul>
		</div>
		<?php endif?>
		
		<div class="about_dish_section">
			<h2>Ingredients</h2>
			<p class="editable-full">
			<?php if (!$is_my_profile):?>
				<?php if ($food->ingredients == ""):?>
				N/A
				<?php else:?>
				<?php echo prevent_xss($food->ingredients)?>
				<?php endif?>
			<?php else:?>
				<a id="input_ingredients" data-type="textarea" data-rows="3" data-onblur="ignore"><?php echo prevent_xss($food->ingredients)?></a>
			<?php endif?>
			</p>
		</div>
		
		<div class="about_dish_section">
			<h2>Health Benefits</h2>
			<p class="editable-full">
			<?php if (!$is_my_profile):?>
				<?php if ($food->health_benefits == ""):?>
				N/A
				<?php else:?>
				<?php echo prevent_xss($food->health_benefits)?>
				<?php endif?>
			<?php else:?>
				<a id="input_benefits" data-type="textarea" data-rows="3" data-onblur="ignore"><?php echo prevent_xss($food->health_benefits)?></a>
			<?php endif?>
			</p>
		</div>
		
		<div class="about_dish_section">
			<h2>Eating Instructions</h2>
			<p class="editable-full">
			<?php if (!$is_my_profile):?>
				<?php if ($food->eating_instructions == ""):?>
				N/A
				<?php else:?>
				<?php echo prevent_xss($food->eating_instructions)?>
				<?php endif?>
			<?php else:?>
				<a id="input_instructions" data-type="textarea" data-rows="3" data-onblur="ignore"><?php echo prevent_xss($food->eating_instructions)?></a>
			<?php endif?>
			</p>
		</div>
	</div>
	
	<h2 class="title center">REVIEWS</h2>
	<ul class="reviews">
		<?php if ($review_display == ""):?>
		<li class="no_review_info">
			<p>No review for this item.</p>
		</li>
		<?php else:?>
		<?php echo $review_display?>
		<?php endif?>
		
		<?php if ($can_add_review):?>
		<li class="btn_new_review_container">
			<button class="action_button">ADD REVIEW</button>
		</li>
		
		<li class="new_review_container">
			<a class="profile_pic" style="background-image:url('<?php echo $current_user->picture?>')"></a>
			<div class="review_info">
				<span><?php echo prevent_xss($current_user->name)?></span>
				<?php echo form_open("", array("id"=>"new_review_form"))?>
					<p>Rating:
						<span class="rating_buttonset">
							<input type="radio" id="radio_rating_1" name="rating" value="1"/>
							<label for="radio_rating_1">1</label>
							<input type="radio" id="radio_rating_2" name="rating" value="2"/>
							<label for="radio_rating_2">2</label>
							<input type="radio" id="radio_rating_3" checked name="rating" value="3"/>
							<label for="radio_rating_3">3</label>
							<input type="radio" id="radio_rating_4" name="rating" value="4"/>
							<label for="radio_rating_4">4</label>
							<input type="radio" id="radio_rating_5" name="rating" value="5"/>
							<label for="radio_rating_5">5</label>
						</span>
					</p>
					<p><textarea placeholder="Comment" rows="2" name="review" id="new_comment"></textarea></p>
				<?php echo form_close()?>
				
				<div class="absolute_parent">
					<div class="ui-dialog new_review_buttons">
						<button type="button" class="ui-button-dialog" id="btn_new_review_ok"></button>
						<button type="button" class="ui-button-dialog" id="btn_new_review_cancel"></button>
					</div>
				</div>
			</div>
		</li>
		<?php endif?>
	</ul>
</section>

<script>
	<?php if (!$is_my_profile):?>
	
	$("button.btn_add_order").button().click(function(e){
		<?php if ($current_user === false):?>
		window.location.href = "/login?redirect=<?php echo urlencode($_SERVER['REQUEST_URI'])?>";
		<?php else:?>
		$.ajax({
			type: 		"post",
			url: 		"/customer/order/add/<?php echo $food->food_id?>",
			data:		csrfData,
			success:	function(data){
				var respArr = $.parseJSON(data);
				if ("success" in respArr){
					$("#order_count").html(respArr["order_count"]);
					
					// display message
					successMessage("Dish added");
					
					// enable/disable further orders
					if (!respArr["enable_order"]){
						$("button.btn_add_order").button("disable");
					}
				} else {
					// error
					errorMessage(respArr["error"]);
				}
			},
			error:		function(){
				// error
				errorMessage("Unable to process");
			}
		});
		<?php endif?>
	});
	
	$(".rating_buttonset").buttonset();
	
	$("li.new_review_container").hide();
	
	$("li.btn_new_review_container button").button().click(function(){
		$("li.btn_new_review_container").hide();
		$("li.new_review_container").show();
	});
	
	$("#btn_new_review_ok").button({
		icons: {
			primary: "ui-icon-check"
		}
	}).click(function(e){
		$("#new_review_form").submit(function(e){
			$.ajax({
				type:		"post",
				url:		"/customer/menu/add_review/<?php echo $food->food_id?>",
				data: 		$("#new_review_form").serialize(),
				success:	function(response){
					var respArr = $.parseJSON(response);
				
					if ("success" in respArr){
						successMessage("Saved");

						// add to list and display the modal for further editing
						$(respArr["li_display"]).prependTo("ul.reviews");
						
						// remove no review info
						$("li.no_review_info").hide();
						
						// reset form
						$("li.new_review_container").hide();
						if (respArr["can_add_more"])
							$("li.btn_new_review_container").show();
					} else {
						errorMessage(respArr["error"]);
						return respArr["error"];
					}
				},
				error:		function(){
					errorMessage("Unable to process");
				}
			});
			
			// prevent actual form submission
			e.preventDefault();
		}(e));
	});
	
	$("#btn_new_review_cancel").button({
		icons: {
			primary: "ui-icon-closethick"
		}
	}).click(function(){
		$("li.new_review_container").hide();
		$("li.btn_new_review_container").show();
	});
	
	<?php else:?>
	
	$("button.btn_remove_order").button().click(function(e){
		$('<div class="dialog-confirm-food" title="Delete dish?"/>')
			.prependTo("#global")
			.html("<p>You sure you want to delete this dish?</p>")
			.dialog({
				autoOpen: true,
				modal: true,
				resizable: false,
				dialogClass: 'explore',
				close:	function(){
					$(".dialog-confirm-food").dialog("destroy").remove();
				},
				buttons:[
					{
						icons: {
							primary: "ui-icon-check"
						},
						'class':	'ui-button-dialog',
						click:		function(){
							$.ajax({
								type: 		"post",
								url: 		"/vendor/food/remove_food/<?php echo $food->food_id?>",
								data:		csrfData,
								success:	function(data){
									var respArr = $.parseJSON(data);
									if ("success" in respArr){
										successMessage("Dish removed");
										
										// check if we're in a modal
										var $modal = $(".food_modal_container");
										
										if ($modal.length){
											// remove modal
											$modal.remove();
											// remove the list element
											$("li.food_item[food_id=<?php echo $food->food_id?>]").remove();
										} else {
											// redirect
											window.location.href = "/vendor/profile";
										}
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
	});
	
	$("#input_food_pic").change(function(){
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
				url:			'/vendor/food/change_foodpic/<?php echo $food->food_id?>',
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
						$('.food_pic[food_id="<?php echo $food->food_id?>"]').css("background-image", "url("+respArr["filepath"]+")");
					} else {
						errorMessage(respArr["error"]);
						return respArr["error"];
					}
				}
			});
		}
	});
	
	$("#input_name").editable({
		url:		"/vendor/food/change_name/<?php echo $food->food_id?>",
		send:		"always",
		params:		csrfData,
		placeholder:"Food name",
		inputclass:	"input_name",
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
	
	$("#input_altname").editable({
		url:		"/vendor/food/change_altname/<?php echo $food->food_id?>",
		send:		"always",
		params:		csrfData,
		emptytext:	"Ethnic name",
		clear:		true,
		inputclass:	"input_name",
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
	
	$("#input_price").editable({
		url:		"/vendor/food/change_price/<?php echo $food->food_id?>",
		send:		"always",
		params:		csrfData,
		inputclass:	"input_price",
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
	
	$("#input_descr").editable({
		url:		"/vendor/food/change_description/<?php echo $food->food_id?>",
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
	
	$("#input_ingredients").editable({
		url:		"/vendor/food/change_ingredients/<?php echo $food->food_id?>",
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
	
	$("#input_benefits").editable({
		url:		"/vendor/food/change_benefits/<?php echo $food->food_id?>",
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
	
	$("#input_instructions").editable({
		url:		"/vendor/food/change_instructions/<?php echo $food->food_id?>",
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
	
	$("#input_category").tagit({
		placeholderText:	"tag",
		removeConfirmation:	true,
		caseSensitive:		false,
		singleField:		true,
		beforeTagAdded:		function(event, ui){
			//If true, it means it's just the initialization which does not need saving
			if (ui.duringInitialization) {
				return;
			}
			// clone dictionary
			var inputs = $.extend({}, csrfData);
			inputs["category_name"] = ui.tagLabel;
			
			var tagSuccess = true;
			
			$.ajax({
				url:			'/vendor/food/add_category/<?php echo $food->food_id?>',
				data:			inputs,
				type:			'post',
				error:		function(response){
					errorMessage("Unable to process");
					tagSuccess = false;
				},
				success:		function(response){
					var respArr = $.parseJSON(response);
			
					if ("success" in respArr){
						successMessage("Tag saved");
					} else {
						errorMessage(respArr["error"]);
						tagSuccess = false;
					}
				}
			});
			
			if (!tagSuccess) {
				return false;
			}
		},
		beforeTagRemoved:	function(event, ui){
			// clone dictionary
			var inputs = $.extend({}, csrfData);
			inputs["category_name"] = ui.tagLabel;
			
			var tagSuccess = true;
			
			$.ajax({
				url:			'/vendor/food/remove_category/<?php echo $food->food_id?>',
				data:			inputs,
				type:			'post',
				error:		function(response){
					errorMessage("Unable to process");
					tagSuccess = false;
				},
				success:		function(response){
					var respArr = $.parseJSON(response);
			
					if ("success" in respArr){
						successMessage("Tag removed");
					} else {
						errorMessage(respArr["error"]);
						tagSuccess = false;
					}
				}
			});
			
			if (!tagSuccess) {
				return false;
			}
		}
	});
	
	$('#edit_prep_time').editable({
		url:		"/vendor/food/change_preptime/<?php echo $food->food_id?>",
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
			
			if (isNaN(value) || value<=0){
				errorMessage("Incorrect time specified");
				return "cannot be blank";
			}
			
			return {newValue: value};
		}
	});
	
	$('#prep_time_buttonset').buttonset({
		items: "input[type=radio]"
	});
	
	$('input[type=radio][name=pickup_method]').change(function(){
		var method;
		if (this.value == 'immediate'){
			method = <?php echo Food_model::$PICKUP_ANYTIME?>;
		} else {
			method = <?php echo Food_model::$PICKUP_DESIGNATED?>;
		}
		
		var inputs = $.extend({}, csrfData);
		inputs["method"] = method;
		
		$.ajax({
			url:			'/vendor/food/change_pickup_method/<?php echo $food->food_id?>',
			data:			inputs,
			type:			'post',
			error:		function(response){
				errorMessage("Unable to process");
				tagSuccess = false;
			},
			success:		function(response){
				var respArr = $.parseJSON(response);
		
				if ("success" in respArr){
					successMessage("Pickup method changed");
				} else {
					errorMessage(respArr["error"]);
					tagSuccess = false;
				}
			}
		});
	});
	
	<?php endif?>
</script>