<section id="food_detail">
	<h1><?php echo $food->food_name?>
		<?php if ($food->alternate_name != ""):?> | <?php echo $food->alternate_name?><?php endif?>
	</h1>
	<h2><?php echo $food->user_name?></h2>
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
</section>