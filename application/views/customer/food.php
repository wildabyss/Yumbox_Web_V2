<div class="food_display_wrapper">
	<h2><?php echo $food->food_name?></h2>
	<p>Vendor: <?php echo $food->user_name?></p>
	<ul class="food_list">
		<?php foreach ($food_pictures as $picture):?>
		<li>
			<img src="<?php echo $picture->path?>" class="food_pic" />
		</li>
		<?php endforeach?>
	</ul>
	<p>Price: $<?php echo $food->price?></p>
	<p>Ingredients: <?php echo $food->ingredients ?>
	<p>Health Benefits: <?php echo $food->health_benefits?>
</div>