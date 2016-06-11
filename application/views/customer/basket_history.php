<section id="basket_view">
	<h1 class="center title">MY PURCHASE</h1>
	
	<div class="button_container">
		<button id="btn_current">CURRENT</button>
		<button id="btn_past" class="ui-state-active">PAST</button>
	</div>
	
	<h2>PAST ORDERS</h2>
	
	<?php if (count($order_baskets)==0):?>
	<p class="center">No past orders.</p>
	<?php else:?>
		<?php foreach ($order_baskets as $basket):?>
		<a class="past_order_section" href="/customer/order/basket/<?php echo $basket->id?>">
			<h3 class="child"><?php echo $basket->order_date?></h3>
			<h3 class="child center">$<?php echo $basket->total_cost?></h3>
			<h3 class="received child right-align"><?php if ($basket->is_filled==1):?>Delivered &#x2713;<?php endif?></h3>
		</a>
		<?php endforeach?>
	<?php endif?>
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
		
	// prevent default hover and focus behaviours on the buttons
	$("#btn_past").hover(function(){
		$(this).toggleClass( "ui-state-active", true );
	}).focusout(function(e){
		$(this).addClass( "ui-state-active", true );
		e.preventDefault();
		e.stopPropagation();
	});
</script>