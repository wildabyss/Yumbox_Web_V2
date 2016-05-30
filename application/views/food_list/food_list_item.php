<li class="food_item absolute_parent">
	<?php if (isset($is_my_profile) && $is_my_profile):?>
	<button class="btn_remove" food_id="<?php echo $food->food_id?>">X</button>
	<?php endif?>
	
	<a class="food_pic" href="/menu/item/<?php echo $food->food_id?>"
		<?php if ($food->pic_path != ''):?>
		style="background-image: url('<?php echo $food->pic_path?>')"
		<?php endif?>>
	</a>
	<div>
		<a href="/menu/item/<?php echo $food->food_id?>">
			<h3><?php echo prevent_xss($food->food_name)?></h3>
		</a>
		<a class="food_price"><h3>$<?php echo $food->food_price?></h3></a>
		
		<?php if ($food->food_alt_name != ""):?>
		<a class="alt_name" href="/menu/item/<?php echo $food->food_id?>">
			<h3><?php echo prevent_xss($food->food_alt_name)?></h3>
		</a>
		<?php endif ?>
		
		<?php if (!isset($is_my_profile)):?>
		<a class="food_maker" href="/vendor/profile/id/<?php echo $food->vendor_id?>">
			<h4><?php echo prevent_xss($food->vendor_name)?></h4>
		</a>
		<?php endif?>
		
		<div class="order_rating_time_container">
			<span><?php echo $food->total_orders?> orders</span>
			
			<?php if ($food->rating > 0):?>
			<span class="rating">&hearts; <?php echo $food->rating?>%</span>
			<?php endif?>
			
			<span class="prep_time"><?php echo $food->prep_time?></span>
		</div>
		
		<?php if (isset($categories)):?>
		<div class="categories_container">
		<?php foreach ($categories[$food->food_id] as $category):?>
			<a class="category_tag"><?php echo ucfirst(prevent_xss($category->name))?></a>
		<?php endforeach?>
		</div>
		<?php endif?>
	</div>
</li>

<script>
	<?php if (isset($is_my_profile) && $is_my_profile):?>
	// this button(s) is created in food_list_item view
	$(".btn_remove").button().click(function(){
		var food_id = $(this).attr("food_id");
		var $parent = $(this).parent();
		
		$("#dialog-confirm")
			.data('food_id', food_id)
			.data('parent', $parent)
			.dialog("open");
	});
	<?php endif?>
</script>