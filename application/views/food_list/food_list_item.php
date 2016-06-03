<li class="food_item absolute_parent" food_id="<?php echo $food->food_id?>">
	<?php if (isset($is_my_profile) && $is_my_profile):?>
	<button class="btn_remove" food_id="<?php echo $food->food_id?>">X</button>
	<?php endif?>
	
	<a class="food_pic toggle_food_detail" food_id="<?php echo $food->food_id?>"
		<?php if (!isset($is_my_profile) || !$is_my_profile):?>href="/menu/item/<?php echo $food->food_id?>"<?php endif?>
		<?php if ($food->pic_path != ''):?>
		style="background-image: url('<?php echo $food->pic_path?>')"
		<?php endif?>>
	</a>
	<div>
		<a class="food_name toggle_food_detail" food_id="<?php echo $food->food_id?>"
			<?php if (!isset($is_my_profile) || !$is_my_profile):?>href="/menu/item/<?php echo $food->food_id?>"<?php endif?>>
			<h3><?php echo prevent_xss($food->food_name)?></h3>
		</a>
		<a class="food_price"><h3>$<?php echo $food->food_price?></h3></a>
		
		<?php if ($food->food_alt_name != ""):?>
		<a class="alt_name toggle_food_detail" food_id="<?php echo $food->food_id?>"
			<?php if (!isset($is_my_profile) || !$is_my_profile):?>href="/menu/item/<?php echo $food->food_id?>"<?php endif?>>
			<h3><?php echo prevent_xss($food->food_alt_name)?></h3>
		</a>
		<?php endif ?>
		
		<?php if (!isset($is_my_profile)):?>
		<a class="food_maker" href="/vendor/profile/id/<?php echo $food->vendor_id?>">
			<h4><?php echo prevent_xss($food->vendor_name)?></h4>
		</a>
		<?php endif?>
		
		<div class="order_rating_time_container">
			<span><?php echo $food->total_orders?> orders</span>
			
			<?php if ($food->rating > 0):?>
			<span class="rating">&hearts; <?php echo $food->rating?>%</span>
			<?php endif?>
			
			<span class="prep_time"><?php echo $food->prep_time?></span>
		</div>
		
		<?php if (isset($categories)):?>
		<div class="categories_container">
		<?php foreach ($categories[$food->food_id] as $category):?>
			<a class="category_tag"><?php echo ucfirst(prevent_xss($category->name))?></a>
		<?php endforeach?>
		</div>
		<?php endif?>
	</div>
</li>

<script>
	<?php if (isset($is_my_profile) && $is_my_profile):?>
	$(".btn_remove").button().click(function(){
		var food_id = $(this).attr("food_id");
		var $parent = $(this).parent();
		
		$('<div class="dialog-confirm-profile" title="Delete dish?"/>')
			.prependTo("#global")
			.html("<p>You sure you want to delete this dish?</p>")
			.hide()
			.dialog({
				autoOpen: true,
				modal: true,
				resizable: false,
				dialogClass: 'explore',
				close:	function(){
					$(".dialog-confirm-profile").dialog("destroy").remove();
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
								url: 		"/vendor/food/remove_food/"+food_id,
								data:		csrfData,
								success:	function(data){
									var respArr = $.parseJSON(data);
									if ("success" in respArr){
										successMessage("Dish removed");
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
	});
	
	$(".toggle_food_detail").click(function(){
		var food_id = $(this).attr("food_id");
		
		$.ajax({
			type: 		"post",
			url: 		"/customer/menu/retrieve_item/"+food_id,
			data:		csrfData,
			success:	function(data){
				var respArr = $.parseJSON(data);
				
				if ("success" in respArr){
					// add dynamic element
					$('<div class="food_modal_container"/>')
						.prependTo("#global")
						.hide()
						.html(respArr["view"]);
					
					// get parent width
					var w = $(".food_modal_container").parent().width();

					// open dialog
					$(".food_modal_container").dialog({
						autoOpen: true,
						modal: true,
						resizable: false,
						width: w*0.9,
						dialogClass: 'explore',
						close: 	function(e, ui){
							$(".food_modal_container").dialog("destroy").remove();
						}
					});
				}
			},
			error:		function(){
				// error
				errorMessage("Unable to retrieve this dish");
			}
		});
	});
	<?php endif?>
</script>