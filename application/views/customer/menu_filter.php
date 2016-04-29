<section id="menu_mega_selection" class="small <?php if ($is_rush):?>rush<?php else:?>explore<?php endif?>">
	<div class="button_parent">
		<a <?php if (!$is_rush):?>class="rush"<?php endif?> href="/menu/quickmenu">
			<h3><?php echo strtoupper($quick_menu_text)?></h3>
		</a>
	</div>
	<div class="button_parent">
		<a <?php if ($is_rush):?>class="explore"<?php endif?> href="/menu/fullmenu">
			<h3><?php echo Strtoupper($full_menu_text)?></h3>
		</a>
	</div>
</section>

<section id="menu_filter" class="<?php if ($is_rush):?>rush<?php else:?>explore<?php endif?>">
	<form action="<?php if ($is_rush):?>/menu/quickmenu<?php else:?>/menu/fullmenu<?php endif?>" method="get">
		<input id="search" name="search" placeholder="e.g. burrito" />
		
		<div class="menu_filter_container">
			<div class="menu_filter_zone">
				<div>
					<p>Price range: </p>
					<div class="filter_slider" id="price_slider"></div>
				</div>
				<div>
					<p>Rating: </p>
					<div class="filter_slider" id="rating_slider"></div>
				</div>
				<div>
					<p>Delivery time: </p>
					<div class="filter_slider" id="turnaround_slider"></div>
				</div>
			</div>
			
			<div class="menu_filter_zone">
				<p>Categories:</p>
				<div id="menu_filter_categories">
					<?php foreach ($main_categories as $category):?>
						<input type="checkbox" id="rad_cat_<?php echo $category->id?>" name="category" />
						<label for="rad_cat_<?php echo $category->id?>"><?php echo ucfirst($category->name)?></label>
					<?php endforeach?>
				</div>
			</div>
		</div>
	</form>
	
	<script>
		$( "#price_slider" ).slider({
			range: true,
			min: 0,
			max: 50,
			step: 10,
			values: [0, 50]
		});
		$( "#rating_slider" ).slider({
			range: true,
			min: 0,
			max: 5,
			step: 1,
			values: [0, 5]
		});
		$( "#turnaround_slider" ).slider({
			range: true,
			min: 0,
			max: 5,
			step: 1,
			values: [0, 5]
		});
		
		$( "#menu_filter_categories" ).buttonset();
	</script>
</section>