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
			<h3>Can Deliver</h3>
			<h3 class="price">$<?php echo $food->price?></h3>
			<button id="add_to_order">ADD ORDER &#x2795;</button>
		</div>
		
		<p>
			<span class="orders"><?php echo $food->total_orders?> orders</span>
			<span class="rating">&hearts; <?php echo $food->rating?>%</span>
		</p>
		
		<p>Preparation time: <?php echo $food->prep_time?></p>
		
		<?php if ($food->is_open):?>
		<h3 class="is_open">KITCHEN OPEN</h3>
		<?php else:?>
		<h3 class="is_closed">KITCHEN CLOSED</h3>
		<?php endif?>
	</div>
	
	<h2 class="title center">ABOUT THE DISH</h2>
	<div class="about_dish">
		<div class="about_dish_section">
			<h2>Description</h2>
			<p>
				<?php if ($food->descr == ""):?>
				N/A
				<?php else:?>
				<?php echo $food->descr?>
				<?php endif?>
			</p>
			
			<div class="categories_container">
			<?php foreach ($categories as $category):?>
				<a class="category_tag"><?php echo ucfirst($category->name)?></a>
			<?php endforeach?>
			</div>
		</div>
		
		<div class="about_dish_section">
			<h2>Ingredients</h2>
			<p>
				<?php if ($food->ingredients == ""):?>
				N/A
				<?php else:?>
				<?php echo $food->ingredients?>
				<?php endif?>
			</p>
		</div>
		
		<div class="about_dish_section">
			<h2>Health Benefits</h2>
			<p>
				<?php if ($food->health_benefits == ""):?>
				N/A
				<?php else:?>
				<?php echo $food->health_benefits?>
				<?php endif?>
			</p>
		</div>
		
		<div class="about_dish_section">
			<h2>Eating Instructions</h2>
			<p>
				<?php if ($food->eating_instructions == ""):?>
				N/A
				<?php else:?>
				<?php echo $food->eating_instructions?>
				<?php endif?>
			</p>
		</div>
	</div>
	
	<h2 class="title center">REVIEWS</h2>
	<?php if (count($reviews)==0):?>
		<p class="reviews">No review for this item.</p>
	<?php else:?>
		<ul class="reviews">
			<?php foreach ($reviews as $review):?>
				<li>
					<?php if ($user_pictures[$review->user_id]!==false):?>
					<a class="profile_pic" style="background-image:url('<?php echo $user_pictures[$review->user_id]?>')"></a>
					<?php else:?>
					<a class="profile_pic"></a>
					<?php endif?>
					
					<div>
						<span><?php echo $review->user_name?></span>
						<p>&hearts; <?php echo $review->rating?>%</p>
						<?php if ($review->review != ""):?>
						<p>"<?php echo $review->review?>"</p>
						<?php endif?>
					</div>
				</li>
			<?php endforeach?>
		</ul>
	<?php endif?>
</section>

<script>
	$("#add_to_order")
		.button()
		.click(function(e){
			<?php if ($current_user === false):?>
			window.location.href = "/login?redirect=<?php echo urlencode($_SERVER['REQUEST_URI'])?>";
			<?php else:?>
			$.ajax({
				type: 		"post",
				url: 		"/customer/order/add/<?php echo $food->food_id?>",
				data:		csrfData,
				success:	function(data){
					var respArr = $.parseJSON(data);
					if ("success" in respArr){
						$("#order_count").html(respArr["order_count"]);
					}
				}
			});
			<?php endif?>
		});
</script>