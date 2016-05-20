<section id="profile_intro">
	<div class="pic_wrapper">
		<a id="profile_pic"></a>
		<p>
			<a id="followers"><?php echo $num_followers?> followers</a>
			<?php if ($my_id !== false  && !$is_my_profile):?>
				<a id="follow_btn">+ Follow</a>
			<?php endif?>
		</p>
	</div>
	<div class="intro_wrapper">
		<h1 class="title"><a id="edit_user_name" data-type="text" data-onblur="ignore"><?php echo $user->name?></a></h1>
		<?php if (!$user->is_open):?>
		<h3 class="is_closed">KITCHEN CLOSED</h3>
		<?php else:?>
		<h3 class="is_open">KITCHEN OPEN</h3>
		<?php endif?>
		<?php if (!$is_my_profile && $user->descr != "" || $is_my_profile):?>
		<h3>ABOUT ME</h3>
		<p><a id="edit_user_descr" data-type="textarea" data-onblur="ignore"><?php echo $user->descr?></a></p>
		<?php endif?>
	</div>
</section>

<section id="menu_listing">
	<div class="food_category_wrapper">
		<div class="food_list_parent">
			<?php if (count($foods)==0):?>
			<div id="no_food_info">
				<?php if ($is_my_profile):?>
				<p class="center">You are currently not selling any dishes.<br/>List your secret dishes and become a home chef today!</p>
				<?php else:?>
				<p class="center">This kitchen is empty.</p>
				<?php endif?>
			</div>
			<?php endif?>
			
			<ul class="food_list">
				<?php foreach ($foods as $food):?>
				<li class="<?php if ($is_my_profile):?>editable_li<?php endif?>">
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
						
						<div class="order_rating_time_container">
							<span><?php echo $food->total_orders?> orders</span>
							
							<?php if ($food->rating > 0):?>
								<span class="rating">&hearts; <?php echo $food->rating?>%</span>
							<?php endif?>
							
							<span class="prep_time"><?php echo $food->prep_time?></span>
						</div>
						
						<div class="categories_container">
						<?php foreach ($categories[$food->food_id] as $category):?>
							<a class="category_tag"><?php echo ucfirst($category->name)?></a>
						<?php endforeach?>
						</div>
					</div>
				</li>
				<?php endforeach?>
				
				<?php if ($is_my_profile):?>
				<li id="btn_add_new" class="editable_li">
					<h3 class="center">Add new dish +</h3>
				</li>
				<?php endif?>
			</ul>
		</div>
	</div>
</section>

<script>
	$("#btn_add_new").click(function(e){
		
	});

	$(document).ready(function(){
		<?php if ($is_my_profile):?>
		$("#edit_user_name").editable();
		
		$("#edit_user_descr").editable({
			rows: 3
		});
		
		<?php endif?>
	});
</script>