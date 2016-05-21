<li>
	<a class="food_pic" href="/menu/item/<?php echo $food->food_id?>"
		<?php if ($food->pic_path != ''):?>
		style="background-image: url('<?php echo $food->pic_path?>')"
		<?php endif?>></a>
	<div>
		<a href="/menu/item/<?php echo $food->food_id?>">
			<h3><?php echo $food->food_name?></h3>
		</a>
		<a class="food_price"><h3>$<?php echo $food->food_price?></h3></a>
		
		<?php if ($food->food_alt_name != ""):?>
		<a class="alt_name" href="/menu/item/<?php echo $food->food_id?>">
			<h3><?php echo $food->food_alt_name?></h3>
		</a>
		<?php endif ?>
		
		<?php if (!isset($is_my_profile)):?>
		<a class="food_maker" href="/vendor/profile/id/<?php echo $food->vendor_id?>">
			<h4><?php echo $food->vendor_name?></h4>
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
			<a class="category_tag"><?php echo ucfirst($category->name)?></a>
		<?php endforeach?>
		</div>
		<?php endif?>
	</div>
</li>