<section id="food_detail">
	<h1 class="title center"><?php echo strtoupper($food->food_name)?>
		<?php if ($food->alternate_name != ""):?> | <?php echo $food->alternate_name?><?php endif?>
	</h1>
	<h3 class="center"><a href="/vendor/profile/id/<?php echo $food->user_id?>"><?php echo $food->user_name?></a></h3>
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
	
	<div class="tight_cluster">
		<div class="order_info">
			<h2>Can Deliver</h2>
			<h2 class="price">$<?php echo $food->price?></h2>
			<a id="add_to_order">ADD TO ORDER &#x2795;</a>
		</div>
		
		<p>
			<span><?php echo $food->total_orders?> orders</span>
			<span>&hearts; <?php echo $food->rating?>%</span>
		</p>
	</div>
	
	<h2 class="title center">ABOUT THE DISH</h2>
	<div class="about_dish">
		<div class="about_dish_section">
			<h2>Description</h2>
			<p><?php echo $food->descr?></p>
		</div>
		
		<div class="about_dish_section">
			<h2>Ingredients</h2>
			<p><?php echo $food->ingredients?></p>
		</div>
		
		<div class="about_dish_section">
			<h2>Health Benefits</h2>
			<p><?php echo $food->health_benefits?></p>
		</div>
	</div>

	<h2 class="title center">PEOPLE HAVE ALSO ORDERED</h2>

</section>

<script>
	$("#add_to_order").button()
</script>

<section id="review_section">
	
</section>