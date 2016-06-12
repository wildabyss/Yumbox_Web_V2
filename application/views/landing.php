<main id="landing_main">
	<?php echo $nav_content?>

	<div id="mobile_detector"></div>

	<section id="featured_dishes">
		<div class="button_parent" id="rush_dish">
			<span style="background-image: url('<?php echo $rush_food_pic?>')"></span>
			<a href="/menu/item/<?php echo $rush_food_id ?>" class="button">
				Featured Rush Pick
				<h1><?php echo prevent_xss($rush_food_name)?></h1>
				<h4>By <?php echo prevent_xss(ucwords($rush_vendor))?></h4>
				<p><?php echo prevent_xss($rush_food_descr)?></p>
			</a>
		</div>
		<div class="button_parent" id="explore_dish">
			<span style="background-image: url('<?php echo $explore_food_pic?>')"></span>
			<a href="/menu/item/<?php echo $explore_food_id ?>" class="button">
				Featured Explore Pick
				<h1><?php echo prevent_xss($explore_food_name)?></h1>
				<h4>By <?php echo ucwords(prevent_xss($explore_vendor))?></h4>
				<p><?php echo prevent_xss($explore_food_descr)?></p>
			</a>
		</div>
	</section>

	<section id="menu_mega_selection">
		<div class="button_parent">
			<a class="rush" href="/menu/rush">
				<h3><?php echo strtoupper($quick_menu_text)?></h3>
				<p>Get your gourmet right away</p>
			</a>
		</div>
		<div class="button_parent">
			<a class="explore" href="/menu/explore">
				<h3><?php echo strtoupper($full_menu_text)?></h3>
				<p>Experience the world of home kitchen</p>
			</a>
		</div>
	</section>
</main>

<section id="features" class="row">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
		<div class="box">
			<img src="/imgs/features_location.svg" width="50px" height="50px" />
			<h2>Wherever you are</h2>
			<div>Access Yumbox and uncover meals made by local passionate chefs near you</div>
		</div>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
		<div class="box">
			<img src="/imgs/features_fresh.svg" width="50px" height="50px" />
			<h2>Wholesome goodness</h2>
			<div>Fresh local ingredients, no preservatives and low in fat, sugar and salt</div>
		</div>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
		<div class="box">
			<img src="/imgs/features_fireworks.svg" width="50px" height="50px" />
			<h2>Explosion of selection</h2>
			<div>Explore your favorite dishes in its purest authenticate flavor, each is made to inspire</div>
		</div>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
		<div class="box">
			<img src="/imgs/features_time.svg" width="50px" height="50px" />
			<h2>Pick up</h2>
			<div>Pick up fresh meals near you, all on your schedule</div>
		</div>
	</div>
</section>
<!--
<section id="food-categories" class="row">
	<div class="col-xs-12 col-sm-12 title">
		<h1>Discover authentic ethnic cuisine</h1>
		<h2>Feel good eating wholesome meals</h2>
	</div>
	<div class="col-xs-12 col-sm-12 col-lg-4 ethnic-cuisine">
		<div style="background-image: url('/imgs/2-ethnic-cuisine-korean.jpg');">
			Korean
		</div>
	</div>
	<div class="col-xs-12 col-sm-12 col-lg-4 ethnic-cuisine">
		<div style="background-image: url('/imgs/2-ethnic-cuisine-kaiseki.jpg');">
			Kaiseki
		</div>
	</div>
	<div class="col-xs-12 col-sm-12 col-lg-4 ethnic-cuisine">
		<div style="background-image: url('/imgs/2-ethnic-cuisine-street-food.jpg');">
			Street Food
		</div>
	</div>
</section>
-->
<section id="enjoy" class="row">
	<div class="col-xs-12 col-sm-12 title">
		<h1>How our fans enjoy Yumbox</h1>
	</div>
	<div class="col-xs-12 col-sm-6 col-lg-4 ethnic-cuisine">
		<div style="background-image: url('/imgs/3-laptop.jpg');">
			Recharge with love-packed lunch
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-lg-4 ethnic-cuisine">
		<div style="background-image: url('/imgs/3-table.jpg');">
			Gastronomic fest, whenever wherever
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 col-lg-4 ethnic-cuisine">
		<div style="background-image: url('/imgs/3-coffee.jpg');">
			Feel good eating wholesome dinner &amp; unwind
		</div>
	</div>
</section>

<section id="section_4" style="background-image: url('/imgs/section_4.jpg')">
	<h1>Enjoy cooking? Create dishes you love and make money sharing extra cooked meals</h1>
	<?php if (isset($sign_in_link)):?>
		<a href="<?php echo $sign_in_link?>">Sign up</a>
	<?php endif?>
</section>

<script>
	// execute after DOM is fully loaded
	$(document).ready(function(){
		// landing page gallery
		var timerId = setInterval(function() {
			if ($('#rush_dish').is(':visible') && !$('#explore_dish').is(':visible')){
				$('#rush_dish').hide();
				$('#explore_dish').css('display', 'flex');
				
			} else if (!$('#rush_dish').is(':visible') && $('#explore_dish').is(':visible')){
				$('#rush_dish').css('display', 'flex');
				$('#explore_dish').hide();
			}
		}, 10000);
	});
	
	// window resize handler to support gallery auto switching
	$(window).resize(function(){
		if (!$('#mobile_detector').is(':visible')){
			$('#rush_dish').removeAttr('style');
			$('#explore_dish').removeAttr('style');
		}
	});
</script>
