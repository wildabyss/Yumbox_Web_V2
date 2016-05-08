<section id="food_detail">
	<h1><?php echo strtoupper($food->food_name)?>
		<?php if ($food->alternate_name != ""):?> | <?php echo $food->alternate_name?><?php endif?>
	</h1>
	<h3><a><?php echo $food->user_name?></a></h3>
	<ul id="food_detail_gallery">
		<?php if (count($food_pictures)==0):?>
			<li>
				<a class="food_pic"></a>
			</li>
		<?php else:?>
			<?php foreach ($food_pictures as $picture):?>
			<li>
				<a class="food_pic" style="background-image:url('<?php echo $picture->path?>')"></a>
			</li>
			<?php endforeach?>
		<?php endif?>
	</ul>
	<h2>ABOUT THE DISH</h2>
	<p>Price: $<?php echo $food->price?></p>
	<p>Ingredients: <?php echo $food->ingredients ?>
	<p>Health Benefits: <?php echo $food->health_benefits?>
</section>