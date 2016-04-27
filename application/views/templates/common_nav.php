<header>
	<nav>
		<a href="/" class="logo">
			YUMBOX
		</a>
		<ul>
			<li><a href="/#about">ABOUT</a></li>
			<li><a href="/vendor"><?php echo $vendor_button?></a></li>
			<li>
				<?php if (isset($sign_in_button)):?>
					<a class="banner_button" href="<?php echo $sign_in_link?>"><?php echo $sign_in_button?></a>
				<?php else:?>
					<a class="banner_button" href="<?php echo $user_name?>"><?php echo $sign_out_button?></a>
				<?php endif?>
			</li>
		</ul>
	</nav>
</header>