<section id="order_checkout">
	<h1 class="center title">MY YUMBOX</h1>
	
	<div class="button_container">
		<button id="btn_current" <?php if ($is_open_basket):?>class="ui-state-active"<?php endif?>>CURRENT</button>
		<button id="btn_past" <?php if (!$is_open_basket):?>class="ui-state-active"<?php endif?>>PAST</button>
	</div>
	
	<?php foreach ($vendors as $vendor):?>
	<div class="vendor_section">
		<div class="title">
			<h3><?php echo strtoupper($vendor->name)?></h3>
		</div>
		
		<?php foreach ($foods_orders[$vendor->id] as $food_order):?>
		<div class="order_item">
			<div class="order_descr">
				<a class="small_pic" 
					<?php if ($food_order->path !=""):?>
					style="background-image:url('<?php echo $food_order->path?>')"
					<?php endif?>
				></a>
				<h3><?php echo $food_order->name?></h3>
				<?php if ($food_order->alternate_name != ""):?>
				<h3><?php echo $food_order->alternate_name?></h3>
				<?php endif?>
				<div class="modify_order">
					<a class="btn_remove">Remove</a>
				</div>
			</div>
			<div class="price_descr">
				<input class="quantity_food" name="quantity" value="<?php echo $food_order->quantity?>">
				<a class="child">X</a>
				<h3 class="child">$<?php echo $food_order->price?></h3>
			</div>
		</div>
		<?php endforeach?>
	</div>
	<?php endforeach?>
	
	<div class="total">
		<h3>TOTAL</h3>
		<h3 class="total_amount">$<?php echo number_format($total_cost, 2)?></h3>
	</div>
	
	<div class="action_buttons_container">
		<button id="btn_checkout">Checkout</button>
	</div>
</section>

<script>
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
		.click(function(e){
			
			
		});
		
	$(".btn_remove").click(function(e)){
		
	}
	
	// quantity spinners
	$(".quantity_food")
		.spinner({
			min: 1,
			stop:function(e){
				$(this).change();
			}
		}).change(function(e){
			
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