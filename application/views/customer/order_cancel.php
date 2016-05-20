<section id="basket_view">
	<h1 class="center title">CANCEL ORDER</h1>

	<div>
		<h2>ITEM SUMMARY</h2>
		
		<div class="vendor_section">
			<div class="title">
				<h3><a href="/vendor/profile/id/<?php echo $vendor->id?>"><?php echo strtoupper($vendor->name)?></a></h3>
				
				<div class="pickup">
					<p class="location">Pick-up:</p>
					<p><a target="_blank" href="<?php echo send_to_google_maps($vendor->address, $vendor->city, $vendor->province, $vendor->postal_code)?>">
						<?php echo $vendor->address?><br/>
						<?php echo $vendor->city?>, <?php echo $vendor->province?><br/>
						<?php echo $vendor->postal_code?>
					</a></p>
					
					<p class="time">Pick-up time:</p>
				</div>
			</div>
			
			<div class="order_item" id="order_item_<?php echo $food_order->order_id?>">
				<div class="order_descr">
					<a class="small_pic" 
						<?php if ($food_order->path!=""):?>
						style="background-image:url('<?php echo $food_order->path?>')"
						<?php endif?>
					></a>
					<div class="food_name">
						<h3><a href="/menu/item/<?php echo $food_order->food_id?>"><?php echo $food_order->name?></a></h3>
						<?php if ($food_order->alternate_name != ""):?>
						<h3><a href="/menu/item/<?php echo $food_order->food_id?>"><?php echo $food_order->alternate_name?></a></h3>
						</a>
						<?php endif?>
					</div>
				</div>
				<div class="price_descr right-align">
					<h3 class="child"><?php echo $food_order->quantity?></h3>
					<a class="child">X</a>
					<h3 class="child price">$<?php echo $food_order->price?></h3>
				</div>
			</div>
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
	
		<div class="action_buttons_container">
			<button id="btn_cancel_begin">Proceed</button>
		</div>
	</div>
	
	<div id="cancel_section">
		<h2>CANCELLATION</h2>
		
		<div class="cancel_info">
			<p><label for="explanation">Reason for cancellation:</label></p>
			<textarea id="explanation"></textarea>
		</div>
		
		<div class="action_buttons_container">
			<button id="btn_process">Process</button>
		</div>
	</div>
</section>

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script>
	// set Stripe public key
	Stripe.setPublishableKey('<?php echo $this->config->item("stripe_public_key")?>');

	$("#btn_cancel_begin")
		.button()
		.click(function(e){
			$(this).parent().hide();
			$("#cancel_section").slideDown(100);
		});
		
	$("#btn_process")
		.button()
		.click(function(e){
			$(this).button("disable");
			statusMessageOn("Processing, please wait...");
			
			// clone dictionary
			var inputs = $.extend({}, csrfData);
			inputs["explanation"] = $("#explanation").val();
			
			$.ajax({
				type: 		"post",
				url: 		"/customer/order/refund/<?php echo $food_order->order_id?>",
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
		});
</script>