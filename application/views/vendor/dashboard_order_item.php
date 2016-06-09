<li order_id="<?php echo $food_order->order_id?>">
	<div class="vendor_section">
		<div class="title">
			<h2>Pickup: <?php echo $food_order->prep_time ?></h2>
			<h3><a href="/vendor/profile/id/">Jimmy Lu</a></h3>
		</div>
		
		<div class="order_item absolute_parent" id="order_item_<?php echo $food_order->order_id?>">
			<div class="order_descr">
				<a class="small_pic" 
					<?php if ($food_order->path!=""):?>
					style="background-image:url('<?php echo $food_order->path?>')"
					<?php endif?>
				></a>
				<div class="food_name">
					<h3><a href="/menu/item/<?php echo $food_order->food_id?>"><?php echo $food_order->food_name?></a></h3>
					<?php if ($food_order->food_alt_name != ""):?>
					<h3><a href="/menu/item/<?php echo $food_order->food_id?>"><?php echo $food_order->food_alt_name?></a></h3>
					<?php endif?>
				</div>
			</div>
			<h3 class="received right-align">
				<?php if ($food_order->is_filled == Order_model::$IS_FILLED_DELIVERED):?>
				<span>Delivered &#x2713;</span>
				<?php elseif ($food_order->refund_id != ""):?>
				<span class="canceled">Canceled &#x274c;</span>
				<?php else:?>
				<button class="btn_finish_order" order_id="<?php echo $food_order->order_id?>">Finish &#x2713;</button>
				<button class="btn_cancel_order" order_id="<?php echo $food_order->order_id?>">Cancel &#x274c;</button>
				<?php endif?>
			</h3>
			<div class="price_descr right-align">
				<h3 class="child"><?php echo $food_order->quantity?></h3>
				<a class="child">X</a>
				<h3 class="child price">$<?php echo $food_order->price?></h3>
			</div>
		</div>
	</div>
	
	<div class="total">
		<div class="line">
			<h3>TAXES</h3>
			<h3 class="amount">$<?php echo $billing["taxes"]?></h3>
		</div>
		<div class="line">
			<h3>FRONT COMMISSION</h3>
			<h3 class="amount">$<?php echo $billing["front_commission"]?></h3>
		</div>
		<div class="line">
			<h3>BACK COMMISSION</h3>
			<h3 class="amount">-$<?php echo $billing["back_commission"]?></h3>
		</div>
		<div class="line">
			<h3>NET GAIN</h3>
			<h3 class="amount">$<?php echo $billing["total"]?></h3>
		</div>
	</div>
</li>

<script>
	$(".btn_cancel_order[order_id=<?php echo $food_order->order_id?>]")
		.button()
		.click(function(e){
			var order_id = $(this).attr("order_id");
			
			$.ajax({
				type: 		"post",
				url: 		"/customer/order/retrieve_cancel/"+order_id,
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
								// destroy the dialog
								$(".food_modal_container").dialog("destroy").remove();
								
								// reload the list item
								$.ajax({
									type:		"post",
									url:		"/vendor/dashboard/retrieve_order_item/"+order_id,
									data: 		csrfData,
									success:	function(response){
										var respArr = $.parseJSON(response);
									
										if ("success" in respArr){
											successMessage("Saved");

											// replace existing list item
											$("li[order_id="+order_id+"]").replaceWith(respArr["li_display"]);
										} else {
											errorMessage(respArr["error"]);
											return respArr["error"];
										}
									},
									error:		function(){
										errorMessage("Unable to retrieve data");
									}
								});
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
		
	$(".btn_finish_order[order_id=<?php echo $food_order->order_id?>]")
		.button()
		.click(function(e){
			var order_id = $(this).attr("order_id");
			
			$.ajax({
				type:		"post",
				url:		"/vendor/dashboard/finish/"+order_id,
				data: 		csrfData,
				success:	function(response){
					var respArr = $.parseJSON(response);
				
					if ("success" in respArr){
						successMessage("Order marked as finished");

						// replace existing list item
						$("li[order_id="+order_id+"]").replaceWith(respArr["li_display"]);
					} else {
						errorMessage(respArr["error"]);
						return respArr["error"];
					}
				},
				error:		function(){
					errorMessage("Unable to process");
				}
			});
		});
</script>