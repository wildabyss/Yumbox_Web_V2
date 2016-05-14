<section id="order_checkout">
	<h1 class="center title">MY YUMBOX</h1>
	
	<div class="button_container">
		<a id="current_btn" class="ui-state-active" href="/customer/order/current">Current Order</a>
		<a id="past_btn" href="/customer/order/basket">Past Orders</a>
	</div>
	
	<?php foreach ($vendors as $vendor):?>
	<div class="vendor_section">
		<div class="title">
			<h3><?php echo strtoupper($vendor->name)?></h3>
		</div>
		
		<div class="order_item">
			<div class="order_descr">
				<a class="small_pic" style="background-image:url('/food_pics/Easy-Kung-Pao-Chicken-Recipe-48.jpg')"></a>
				<h3>Best steak in town</h3>
			</div>
			<div class="price_descr">
				<h3 class="child">$20.00</h3>
				<a class="child">X</a>
				<input class="quantity_food" name="value">
			</div>
		</div>
	</div>
	<?php endforeach?>
	
	<div class="total">
	
	</div>
</section>

<script>
	$("#current_btn").button();
	$("#past_btn").button();
	
	// quantity spinners
	$(".quantity_food").spinner();
</script>