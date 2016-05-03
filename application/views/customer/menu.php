<section id="menu_listing">
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
							<a class="food_maker"><h4><?php echo $food->vendor_name?></h4></a>
						</div>
					</li>
					<?php endforeach?>
				</ul>
			</div>
		</div>
		<?php endforeach ?>
	<?php endif ?>
</section>