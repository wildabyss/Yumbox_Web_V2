<section>
	<?php foreach ($food_categories as $category):?>
	<div class="food_category_wrapper">
		<h2><?php echo ucwords($category->name)?></h2>
		<ul class="food_list">
			<?php foreach ($foods[$category->id] as $food):?>
			<li><a href="/menu/food/<?php echo $food->id?>">
				<img src="<?php echo $food->path?>" class="food_pic" />
				<p><?php echo $food->name?></p>
			</a></li>
			<?php endforeach?>
		</ul>
	</div>
	<?php endforeach ?>
</section>