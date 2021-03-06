<header>
	<nav>
		<div class="nav_wrapper">
			<a id="mobile_user_menu_trigger"></a>
			<ul id="pc_nav_buttons_left">
				<li><a href="/" class="logo"></a></li>
				<li><a href="/menu/rush">YUM RUSH</a></li>
				<li><a href="/menu/explore">YUM EXPLORE</a></li>
				<li><a href="/#features">ABOUT</a></li>
			</ul>

			<ul id="mobile_user_menu">
				<li class="menu_item_home"><a href="/" class="logo">Yumbox</a></li>
				<li><a href="/menu/rush">Yum Rush</a></li>
				<li><a href="/menu/explore">Yum Explore</a></li>
				<?php if (isset($sign_in_button)):?>
				<li class="menu_item_log"><a class="banner_button" href="<?php echo $sign_in_link?>">Log In</a></li>
				<?php else:?>
				<li class="menu_item_kitchen"><a href="/vendor/profile"><?php if ($is_vendor):?>My Kitchen<?php else:?>My Profile<?php endif?></a></li>
				<li class="menu_item_dashboard" <?php if (!$is_vendor):?>style="display:none"<?php endif?>><a href="/vendor/dashboard">Chef's Dashboard</a></li>
				<li class="menu_item_log"><a class="banner_button" href="<?php echo $sign_out_link?>">Log Out</a></li>
				<?php endif?>
				<li class="menu_item_about"><a href="/#features">About</a></li>
				<li class="filler"></li>
			</ul>
			
			<ul id="pc_nav_buttons_right">
				<?php if (isset($sign_in_button)):?>
				<li>
					<a class="banner_button" href="<?php echo $sign_in_link?>"><?php echo $sign_in_button?></a>
				</li>
				<?php endif?>
				
				<li class="user_menu_trigger_wrapper">
					<?php if (isset($user_name)):?>
					<div id="user_menu_trigger">
						<a class="banner_button"><?php echo prevent_xss(strtoupper($user_name))?></a>
						<a id="user_menu_visual">&#9660;</a>
					</div>
					<?php endif?>
					
					<?php if (!isset($sign_in_button)):?>
					<ul id="user_menu">
						<li class="menu_item_kitchen"><a href="/vendor/profile"><?php if ($is_vendor):?>My Kitchen<?php else:?>My Profile<?php endif?></a></li>
						<li class="menu_item_dashboard" <?php if (!$is_vendor):?>style="display:none"<?php endif?>><a href="/vendor/dashboard">Chef's Dashboard</a></li>
						<li class="menu_item_logout"><a href="<?php echo $sign_out_link?>">Logout</a></li>
					</ul>
					<?php endif?>
				</li>
				
				<?php if (isset($user_name)):?>
				<li>
					<a class="cart" href="/customer/order">
						<span class="order_cart"></span>
						<span id="order_count" class="round_border"><?php echo $order_count?></span>
					</a>
				</li>
				<?php endif?>
				
				<?php if (ENVIRONMENT !== 'production'):?>
				<li style="color: red">[BETA]</li>
				<?php endif?>
			</ul>
		</div>
	</nav>
	
	<script>
		// make into menus
		$("#user_menu").menu();
		$("#mobile_user_menu").menu();
		
		// large screen user menu trigger
		$('#user_menu_trigger').click(function(e){
			if ($(this).hasClass("selected")){
				hideUserMenu();
			} else {
				showUserMenu();
			}
			
			e.stopPropagation();
		});
		$('li.filler').click(function(e){
			e.stopPropagation();
		});
		
		// small screen user menu trigger
		$('#mobile_user_menu_trigger').click(function(e){
			showMobileMenu();
			e.stopPropagation();
		});
	</script>
</header>
