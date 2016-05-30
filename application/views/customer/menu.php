<section id="menu_listing">
	<?php if (count($foods)==0):?>
	<p><?php echo $empty_string?></p>
	<?php else: ?>
	<?php echo $food_list_display?>
	
	<?php if (isset($show_more) && $show_more):?>
	<div class="food_category_wrapper">
		<div class="food_list_parent">
			<h2></h2>
			<p class="center <?php if (isset($is_rush) && $is_rush):?>rush<?php else:?>explore<?php endif?>"><button class="btn_show_more_categories">Show more categories</button></p>
		</div>
	</div>
	<?php endif?>
	
	<?php endif ?>
</section>

<script>
	$(".btn_show_more_categories").button();
</script>