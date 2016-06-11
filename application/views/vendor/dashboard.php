<section id="basket_view">
	<h1 class="center title">KITCHEN DASHBOARD</h1>
	
	<div class="button_container">
		<button id="btn_current" <?php if ($is_current):?>class="ui-state-active"<?php endif?>>TODO</button>
		<button id="btn_past" <?php if (!$is_current):?>class="ui-state-active"<?php endif?>>FINISHED</button>
	</div>
	
	<div id="no_items" <?php if ($total_orders>0):?>style="display:none"<?php endif?>>
		<p>No item in the cart.</p>
	</div>
	
	<ul class="partition">
		<?php echo $orders_display ?>
	</ul>
	
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
	</script>
</section>