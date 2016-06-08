<section id="basket_view">
	<h1 class="center title">KITCHEN DASHBOARD</h1>
	
	<div class="button_container">
		<button id="btn_current" <?php if ($is_current):?>class="ui-state-active"<?php endif?>>TODO</button>
		<button id="btn_past" <?php if (!$is_current):?>class="ui-state-active"<?php endif?>>FINISHED</button>
	</div>
	
	<div id="no_items" <?php if (count($foods_orders)>0):?>style="display:none"<?php endif?>>
		<p>No item in the cart.</p>
	</div>
	
	<div>
		<?php foreach ($foods_orders as $food_order):?>
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
				<h3 class="amount">$<?php echo $billing[$food_order->order_id]["taxes"]?></h3>
			</div>
			<div class="line">
				<h3>FRONT COMMISSION</h3>
				<h3 class="amount">$<?php echo $billing[$food_order->order_id]["front_commission"]?></h3>
			</div>
			<div class="line">
				<h3>BACK COMMISSION</h3>
				<h3 class="amount">-$<?php echo $billing[$food_order->order_id]["back_commission"]?></h3>
			</div>
			<div class="line">
				<h3>NET GAIN</h3>
				<h3 class="amount">$<?php echo $billing[$food_order->order_id]["total"]?></h3>
			</div>
		</div>
		<?php endforeach?>
	</div>
</section>

<script>
	// pagination buttons behaviours
	$("#btn_current")
		.button()
		.click(function(e){
			window.location = "/vendor/dashboard/todo";
		});
		
	$("#btn_past")
		.button()
		.click(function(e){
			window.location = "/vendor/dashboard/finished";
		});
		
	// prevent default hover and focus behaviours on the buttons
	$("<?php if (!$is_current):?>#btn_past<?php else:?>#btn_current<?php endif?>").hover(function(){
		$(this).toggleClass( "ui-state-active", true );
	}).focusout(function(e){
		$(this).addClass( "ui-state-active", true );
		e.preventDefault();
		e.stopPropagation();
	});
	
	
	$(".btn_cancel_order")
		.button()
		.click(function(e){
			window.location.href = "/customer/order/cancel/"+$(this).attr("order_id");
		});
		
	$(".btn_finish_order")
		.button()
		.click(function(e){
			//window.location.href = "/customer/order/cancel/"+$(this).attr("order_id");
		});
</script>