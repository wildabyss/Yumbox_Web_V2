<header>
	<nav>
		<div class="nav_wrapper">
			<a href="/" class="logo">
				YUMBOX
			</a>
			<ul id="pc_nav_buttons">
				<li><a href="/#about">ABOUT</a></li>
				<li><a href="/vendor/profile"><?php echo $vendor_button?></a></li>
				<li>
					<?php if (isset($sign_in_button)):?>
						<a class="banner_button" href="<?php echo $sign_in_link?>"><?php echo $sign_in_button?></a>
					<?php else:?>
						<a class="banner_button" href="<?php echo $sign_out_link?>"><?php echo strtoupper($user_name)?></a>
					<?php endif?>
				</li>
			</ul>
		</div>
	</nav>
</header>