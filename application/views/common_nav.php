<header>
	<nav>
		<div class="nav_wrapper">
			<a id="mobile_user_menu_trigger"></a>
			<ul id="pc_nav_buttons_left">
				<li><a href="/" class="logo"></a></li>
				<li><a href="/menu/rush">YUM RUSH</a></li>
				<li><a href="/menu/explore">YUM EXPLORE</a></li>
				<li><a href="/#about">ABOUT</a></li>
			</ul>

			<ul id="mobile_user_menu">
				<li class="menu_item_home"><a href="/" class="logo">Yumbox</a></li>
				<li><a href="/menu/rush">Yum Rush</a></li>
				<li><a href="/menu/explore">Yum Explore</a></li>
				<?php if (isset($sign_in_button)):?>
					<li class="menu_item_log"><a class="banner_button" href="<?php echo $sign_in_link?>">Log In</a></li>
				<?php else:?>
					<li class="menu_item_kitchen"><a href="/vendor/profile">My Kitchen</a></li>
					<li class="menu_item_dashboard"><a>Chef's Dashboard</a></li>
					<li class="menu_item_log"><a class="banner_button" href="<?php echo $sign_out_link?>">Log Out</a></li>
				<?php endif?>
				<li class="menu_item_about"><a href="/#about">About</a></li>
			</ul>
			
			<ul id="pc_nav_buttons_right">
				<li class="user_menu_trigger_wrapper">
					<?php if (isset($sign_in_button)):?>
						<a class="banner_button" href="<?php echo $sign_in_link?>"><?php echo $sign_in_button?></a>
					<?php elseif (isset($user_name)):?>
						<div id="user_menu_trigger">
							<a class="banner_button"><?php echo strtoupper($user_name)?></a>
							<a id="user_menu_visual">&#9660;</a>
						</div>
					<?php endif?>
					<ul id="user_menu">
						<li class="menu_item_kitchen"><a href="/vendor/profile">My Kitchen</a></li>
						<li class="menu_item_dashboard"><a>Chef's Dashboard</a></li>
						<?php if (isset($sign_out_link)):?>
						<li class="menu_item_logout"><a href="<?php echo $sign_out_link?>">Logout</a></li>
						<?php endif?>
					</ul>
				</li>
				<li class="cart">
					<?php if (isset($user_name)):?>
						<a id="order_cart"></a>
					<?php endif?>
				</li>
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
				$('#user_menu').slideUp(300);
			} else {
				$('#user_menu').slideDown(300);
			}
			
			$(this).toggleClass("selected");
			e.stopPropagation();
		});
		
		// small screen user menu trigger
		$('#mobile_user_menu_trigger').click(function(e){
			if ($(this).hasClass("selected")){
				$('#mobile_user_menu').slideUp(300);
			} else {
				$('#mobile_user_menu').slideDown(300);
			}
			
			$(this).toggleClass("selected");
			e.stopPropagation();
		});
	</script>
</header>