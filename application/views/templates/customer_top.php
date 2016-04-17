<div id="banner_wrapper">
	<div class="banner_content">
		<a class="banner_slogan" href="/"><?php echo $slogan ?></a>
	</div>
	<a class="banner_button" href="/vendor"><?php echo $vendor_button?></a>
	<?php if (isset($sign_in_button)):?>
		<a class="banner_button" href="<?php echo $sign_in_link?>"><?php echo $sign_in_button?></a>
	<?php else:?>
		<a class="banner_button" href="<?php echo $sign_out_link?>"><?php echo $sign_out_button?></a>
	<?php endif?>
</div>