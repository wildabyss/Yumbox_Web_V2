<div class="login_wrapper">
	<h2>New to Yumbox? It only takes one click!</h2>
	<h2>Register or login with:</h2>
	<?php if (isset($error)):?>
		<p><?php echo $error?></p>
	<?php endif?>
	<div class="login_button_wrapper">
		<a class="fb_login" href="<?php echo $fb_login_url?>"></a>
		<a class="google_login" href="<?php echo $google_login_url?>"></a>
	</div>
</div>