<section id="menu_listing">
	<?php if (count($foods)==0):?>
	<p><?php echo $empty_string?></p>
	<?php else: ?>
	<?php echo $food_list_display?>
	<?php endif ?>
</section>