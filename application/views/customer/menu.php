<section id="menu_mega_selection" class="small <?php if ($is_rush):?>rush<?php else:?>explore<?php endif?>">
	<div class="button_parent">
		<a <?php if (!$is_rush):?>class="rush"<?php endif?> href="/menu/quickmenu">
			<h2><?php echo strtoupper($quick_menu_text)?></h2>
		</a>
	</div>
	<div class="button_parent">
		<a <?php if ($is_rush):?>class="explore"<?php endif?> href="/menu/fullmenu">
			<h2><?php echo Strtoupper($full_menu_text)?></h2>
		</a>
	</div>
</section>

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