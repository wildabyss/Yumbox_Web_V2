<div class="food_category_wrapper">
	<div class="food_list_parent">
		<?php if (isset($category)):?>
		<h2><?php echo ucfirst(prevent_xss($category->name))?></h2>
		<?php endif?>
		<ul class="food_list">