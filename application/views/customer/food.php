<section id="food_detail">
	<h1><?php echo strtoupper($food->food_name)?>
		<?php if ($food->alternate_name != ""):?> | <?php echo $food->alternate_name?><?php endif?>
	</h1>
	<h3><a href="/vendor/profile/id/<?php echo $food->user_id?>"><?php echo $food->user_name?></a></h3>
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
	
	<div class="order_info">
	<h3>Can Deliver</h3>
	<h3>$<?php echo $food->price?></h3>
	<a id="add_to_order">ADD TO ORDER &#x2795;</a>
	</div>
	
	<h2>ABOUT THE DISH</h2>
	<p>Price: $<?php echo $food->price?></p>
	<p>Ingredients: <?php echo $food->ingredients ?>
	<p>Health Benefits: <?php echo $food->health_benefits?>
</section>

<script>
	$("#add_to_order").button()
</script>