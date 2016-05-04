<section id="profile_intro">
	<div class="pic_wrapper">
		<a id="profile_pic"></a>
		<p>
			<a id="followers"><?php echo $num_followers?> followers</a>
			<?php if ($my_id !== false):?>
				<a id="follow_btn">+ Follow</a>
			<?php endif?>
		</p>
	</div>
	<div class="intro_wrapper">
		<h1><?php echo $user_name?></h1>
		<h3>HOURS</h3>
		<p><?php echo date('g:i A', $start_time)?> - <?php echo date('g:i A', $end_time)?></p>
		<h3>ABOUT ME</h3>
		<p><?php echo $user_descr?></p>
	</div>
</section>

<section id="profile_menu_listing">
	<?php if (count($foods)==0):?>
		<p><?php echo $empty_string?></p>
		
	<?php else: ?>
		<?php foreach ($categories as $category):?>
		<div class="food_category_wrapper">
			<div class="food_list_parent">
				<h2><?php echo ucfirst($category->name)?></h2>
				<ul class="food_list">
					<?php foreach ($foods[$category->id] as $food):?>
					<li>
						<a class="food_pic" href="/menu/item/<?php echo $food->food_id?>"
							<?php if ($food->pic_path != ''):?>
							style="background-image: url('<?php echo $food->pic_path?>')"
							<?php endif?>></a>
						<div>
							<a href="/menu/item/<?php echo $food->food_id?>">
								<h3><?php echo $food->food_name?></h3>
								<?php if ($food->food_alt_name != ""):?>
									<h3><?php echo $food->food_alt_name?></h3>
								<?php endif ?>
							</a>
							<a class="food_price"><h3>$<?php echo $food->food_price?></h3></a>
						</div>
					</li>
					<?php endforeach?>
				</ul>
			</div>
		</div>
		<?php endforeach ?>
	<?php endif ?>
</section>

<script>
	
</script>