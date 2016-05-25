<section id="basket_view">
	<h1 class="center title">MY PURCHASE</h1>
	
	<div class="button_container">
		<button id="btn_current" <?php if ($is_open_basket):?>class="ui-state-active"<?php endif?>>CURRENT</button>
		<button id="btn_past" <?php if (!$is_open_basket):?>class="ui-state-active"<?php endif?>>PAST</button>
	</div>

	<div>
		<?php if (!$is_open_basket):?>
		<h2 class="child"><?php echo strtoupper($order_basket->order_date)?></h2>
		<?php else:?>
		<h2>ORDER SUMMARY</h2>
		<?php endif?>
		
		<?php foreach ($vendors as $vendor):?>
		<div class="vendor_section">
			<div class="title">
				<h3><a href="/vendor/profile/id/<?php echo $vendor->id?>"><?php echo prevent_xss(strtoupper($vendor->name))?></a></h3>
				
				<div class="pickup">
					<p class="location">Pick-up:</p>
					<p>
						<a target="_blank" href="<?php echo prevent_xss(send_to_google_maps($vendor->address, $vendor->city, $vendor->province, $vendor->postal_code))?>">
							<?php echo prevent_xss($vendor->address)?><br/>
							<?php echo prevent_xss($vendor->city)?>, <?php echo prevent_xss($vendor->province)?><br/>
							<?php echo prevent_xss($vendor->postal_code)?>
						</a>
					</p>
				</div>
			</div>
			
			<?php foreach ($foods_orders[$vendor->id] as $food_order):?>
			<div class="order_item" id="order_item_<?php echo $food_order->order_id?>">
				<?php if ($is_open_basket):?>
				<button class="btn_remove" order_id="<?php echo $food_order->order_id?>">X</button>
				<?php endif?>
				<div class="order_descr">
					<a class="small_pic" 
						<?php if ($food_order->path!=""):?>
						style="background-image:url('<?php echo $food_order->path?>')"
						<?php endif?>
					></a>
					<div class="food_name">
						<h3><a href="/menu/item/<?php echo $food_order->food_id?>"><?php echo prevent_xss($food_order->name)?></a></h3>
						<?php if ($food_order->alternate_name != ""):?>
						<h3><a href="/menu/item/<?php echo $food_order->food_id?>"><?php echo prevent_xss($food_order->alternate_name)?></a></h3>
						</a>
						<?php endif?>
					</div>
					<div class="pickup_time">
						<p class="time">Pick-up time: <?php echo $food_order->prep_time ?></p>
					</div>
				</div>
				<h3 class="received right-align">
				<?php if ($food_order->is_filled == Order_model::$IS_FILLED_DELIVERED):?>
				<span>Delivered &#x2713;</span>
				<?php elseif ($food_order->refund_id != ""):?>
				<span class="canceled">Canceled &#x274c;</span>
				<?php elseif (!$is_open_basket):?>
				<button class="btn_cancel_order" order_id="<?php echo $food_order->order_id?>">Cancel &#x274c;</button>
				<?php endif?>
				</h3>
				<div class="price_descr right-align">
					<?php if ($is_open_basket):?>
					<input class="quantity_food" order_id="<?php echo $food_order->order_id?>" value="<?php echo $food_order->quantity?>" />
					<?php else:?>
					<h3 class="child"><?php echo $food_order->quantity?></h3>
					<?php endif?>
					<a class="child">X</a>
					<h3 class="child price">$<?php echo $food_order->price?></h3>
				</div>
			</div>
			<?php endforeach?>
		</div>
		<?php endforeach?>
		
		<div id="no_items" <?php if (count($foods_orders)>0):?>style="display:none"<?php endif?>>
			<p>No item in the cart.</p>
		</div>
		
		<div class="total">
			<div class="line">
				<h3>COMMISSION</h3>
				<h3 class="amount" id="commission">$<?php echo number_format($commission, 2)?></h3>
			</div>
			<div class="line">
				<h3>TAXES</h3>
				<h3 class="amount" id="taxes">$<?php echo number_format($taxes, 2)?></h3>
			</div>
			<div class="line">
				<h3>TOTAL</h3>
				<h3 class="amount" id="total_amount">$<?php echo number_format($total_cost, 2)?></h3>
			</div>
		</div>
		
		<?php if ($is_open_basket):?>
		<div class="action_buttons_container">
			<button id="btn_checkout">Checkout</button>
		</div>
		<?php endif?>
	</div>
	
	<div id="order_checkout">
		<h2>BILLING INFORMATION</h2>
		
		<form method="post" id="payment_form">
			<div class="payment_info">
				<p>
					<label for="cardholder">Cardholder name:</label>
					<input type="text" id="cardholder" />
				</p>
				<p>
					<label for="credit_number">Credit card number:</label>
					<input type="text" autocomplete="off" id="credit_number" data-stripe="number" />
				</p>
				<p>
					<label for="exp_date">Expiration:</label>
					<input type="text" maxlength="2" autocomplete="off" id="exp_month" placeholder="MM" data-stripe="exp_month" />
					/ <input type="text" maxlength="2" autocomplete="off" id="exp_year" placeholder="YY" data-stripe="exp_year" />
				</p>
				<p>
					<label for="cvc">CVC:</label>
					<input type="text" maxlength="3" autocomplete="off" id="cvc" data-stripe="cvc" />
				</p>
				<p>
					<label for="address">Address on card:</label>
					<input type="text" id="address" data-stripe="address_line1" />
				</p>
				<p>
					<label for="postal">Postal code:</label>
					<input type="text" id="postal" data-stripe="address_zip" />
				</p>
			</div>
		</form>
		
		<div class="action_buttons_container">
			<button id="btn_process">Process</button>
		</div>
	</div>
</section>

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script>
	// set Stripe public key
	Stripe.setPublishableKey('<?php echo $this->config->item("stripe_public_key")?>');

	$("#btn_current")
		.button()
		.click(function(e){
			window.location = "/customer/order/current";
		});
		
	$("#btn_past")
		.button()
		.click(function(e){
			window.location = "/customer/order/basket";
		});
		
	$("#btn_checkout")
		.button()
		<?php if (count($foods_orders)==0):?>.button("disable")<?php endif?>
		.click(function(e){
			$(this).parent().hide();
			$("#order_checkout").slideDown(100);
		});
		
	$(".btn_remove")
		.button()
		.click(function(e){
			var order_id = $(this).attr("order_id");
			
			$.ajax({
				type: 		"post",
				url: 		"/customer/order/remove/"+order_id,
				data:		csrfData,
				success:	function(data){
					var respArr = $.parseJSON(data);
					if ("success" in respArr){
						
						// update order count
						$("#order_count").html(respArr["order_count"]);
						
						// update total cost
						$("#total_amount").html("$"+parseFloat(respArr["total_cost"]).toFixed(2));
						
						// parent of order item
						var vendor_section = $("#order_item_"+order_id).parent();
						
						// remove order item from view
						$("#order_item_"+order_id).remove();
						if (vendor_section.children(".order_item").length == 0){
							// remove parent
							vendor_section.remove();
						}
						
						// show no item
						if (respArr["order_count"]==0){
							$("#no_items").show();
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
		});
	
	$("#btn_process")
		.button()
		.click(function(e){
			$(this).button("disable");
			statusMessageOn("Processing, please wait...");
			
			// retrieve Stripe token
			Stripe.card.createToken($("#payment_form"), function(status, response){
				if (response.error){
					// error detected
					errorMessage(response.error.message);
					$("#btn_process").button("enable");
				} else {
					// pass token to server
					
					// clone dictionary
					var inputs = $.extend({}, csrfData);
					inputs["token"] = response.id;
					
					$.ajax({
						type: 		"post",
						url: 		"/customer/order/payment/<?php echo $order_basket->id?>",
						data:		inputs,
						success:	function(data){
							var respArr = $.parseJSON(data);
							if ("success" in respArr){
								
								// redirect to past orders
								
								var basket_id = respArr["basket_id"];
								window.location.href = "/customer/order/basket/"+basket_id;
								
							} else {
								// error
								errorMessage(respArr["error"]);
								$("#btn_process").button("enable");
							}
						},
						error: 		function(){
							errorMessage("Unable to process");
							$("#btn_process").button("enable");
						}
					});
				}
			});
		});
	
	$(".btn_cancel_order")
		.button()
		.click(function(e){
			window.location.href = "/customer/order/cancel/"+$(this).attr("order_id");
		});
	
	// quantity spinners
	$(".quantity_food")
		.spinner({
			min: 1,
			stop:function(e){
				$(this).change();
			}
		}).change(function(e){
			var order_id = $(this).attr("order_id");
			var quantity = $(this).val();
			
			$.ajax({
				type: 		"post",
				url: 		"/customer/order/change/"+order_id+"/"+quantity,
				data:		csrfData,
				success:	function(data){
					var respArr = $.parseJSON(data);
					if ("success" in respArr){
						
						// update order count
						$("#order_count").html(respArr["order_count"]);
						
						// update total cost
						$("#total_amount").html("$"+parseFloat(respArr["total_cost"]).toFixed(2));
						
						// show no item
						if (respArr["order_count"]==0){
							$("#no_items").show();
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
		});
	
	// prevent default hover and focus behaviours on the buttons
	$("<?php if (!$is_open_basket):?>#btn_past<?php else:?>#btn_current<?php endif?>").hover(function(){
		$(this).toggleClass( "ui-state-active", true );
	}).focusout(function(e){
		$(this).addClass( "ui-state-active", true );
		e.preventDefault();
		e.stopPropagation();
	});
</script>