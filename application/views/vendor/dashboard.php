<section id="basket_view">
	<h1 class="center title">KITCHEN DASHBOARD</h1>
	
	<div class="button_container">
		<button id="btn_current" class="ui-state-active">TODO</button>
		<button id="btn_past">FINISHED</button>
	</div>
	
	<div id="no_items" <?php //if (count($foods_orders)>0):?>style="display:none"<?php //endif?>>
		<p>No item in the cart.</p>
	</div>
	
	<div>
		<div class="vendor_section">
			<div class="title">
				<h2>Pickup: <?php //echo $food_order->prep_time ?></h2>
				<h3><a href="/vendor/profile/id/">Jimmy Lu</a></h3>
			</div>
			
			<div class="order_item absolute_parent" id="order_item_<?php //echo $food_order->order_id?>">

				<div class="order_descr">
					<a class="small_pic"></a>
					<div class="food_name">
						<h3><a href="/menu/item/">Steak</a></h3>
						<?php //if ($food_order->alternate_name != ""):?>
						<h3><a href="/menu/item/">Steak B</a></h3>
						</a>
						<?php //endif?>
					</div>
				</div>
				<h3 class="received right-align">
					<!--
					<?php //if ($food_order->is_filled == Order_model::$IS_FILLED_DELIVERED):?>
					<span>Delivered &#x2713;</span>
					<?php //elseif ($food_order->refund_id != ""):?>
					<span class="canceled">Canceled &#x274c;</span>
					<?php //else:?>
					-->
					<button class="btn_cancel_order" order_id="<?php //6echo $food_order->order_id?>">Cancel &#x274c;</button>
					<button class="btn_finish_order" order_id="<?php //6echo $food_order->order_id?>">Finish &#x2713;</button>
					<?php //endif?>
				</h3>
				<div class="price_descr right-align">
					<h3 class="child"><?php echo 5; //$food_order->quantity?></h3>
					<a class="child">X</a>
					<h3 class="child price">$<?php echo 5.00; //$food_order->price?></h3>
				</div>
			</div>
		</div>
		
		<div class="total">
			<div class="line">
				<h3>COMMISSION</h3>
				<h3 class="amount" id="commission">-$0.00</h3>
			</div>
			<div class="line">
				<h3>TOTAL</h3>
				<h3 class="amount" id="total_amount">$15.01</h3>
			</div>
		</div>
	</div>

</section>