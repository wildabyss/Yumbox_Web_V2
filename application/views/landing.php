<div id="landing_main">
	<?php echo $nav_content?>

	<section id="featured_dishes">
		<div id="featured_left" class="featured_arrow"><</div>
		<div class="button_parent rush" >
			<span style="background-image: url('<?php echo $rush_food_pic?>')"></span>
			<a href="/menu/item/<?php echo $rush_food_id ?>" class="button">
				Featured Rush Pick
				<h1><?php echo $rush_food_name?></h1>
				<h4>By <?php echo ucwords($rush_vendor)?></h4>
				<p><?php echo $rush_food_descr?></p>
			</a>
		</div>
		<div class="button_parent explore">
			<span style="background-image: url('<?php echo $explore_food_pic?>')"></span>
			<a href="/menu/item/<?php echo $explore_food_id ?>" class="button">
				Featured Explore Pick
				<h1><?php echo $explore_food_name?></h1>
				<h4>By <?php echo ucwords($explore_vendor)?></h4>
				<p><?php echo $explore_food_descr?></p>
			</a>
		</div>
		<div id="featured_right" class="featured_arrow">></div>
	</section>
	
	<section id="menu_mega_selection">
		<div class="button_parent">
			<a class="rush" href="/menu/quickmenu">
				<h2><?php echo strtoupper($quick_menu_text)?></h2>
				<p>Get your gourmet right away</p>
			</a>
		</div>
		<div class="button_parent">
			<a class="explore" href="/menu/fullmenu">
				<h2><?php echo Strtoupper($full_menu_text)?></h2>
				<p>Experience the world of home kitchen</p>
			</a>
		</div>
	</section>
</div>

<section id="about">
	<div class="about_text">
		<h1>ABOUT US</h1>
		<h2>Home kitchen reinvented</h2>
		<p>Yumbox is a service made by foodies for foodies. Tap into the limitless world of home kitchen and recipes
		from your neighbours.</p>
		<h2>Easy and fast ordering</h2>
		<p>In today's world, fast ordering is the key to getting chown down your throat.</p>
		<h2>Become a chef</h2>
		<p>In Yumbox, transitioning from a consumer to a chef is only a few keystrokes away. Ever dreamt of
		sharing your cooking skills to your city? Let Yumbox make your dream come true.</p>
	</div>
</section>

<section id="contact">
	<div class="about_text">
		<h1>CONTACT US</h1>
		<h2>Sales</p>
		<p>Customers: sales@sample.ca</p>
		<p>Finance: sales@sample.ca</p>
		<h2>Careers</h2>
		<p>Jobs: jobs@sample.ca</p>
	</div>
</section>