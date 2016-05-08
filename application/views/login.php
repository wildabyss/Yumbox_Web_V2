<section id="login_section">
	<h1>SIGN UP with YUMBOX</h1>
	<p>Sign up for new user or log in for existing user is just one click away.</p>

	<?php if (isset($error)):?>
		<p><?php echo $error?></p>
	<?php endif?>
	<div class="login_button_wrapper">
		<a class="fb_login" href="<?php echo $fb_login_url?>">Log in with Facebook</a>
		<a class="google_login" href="<?php echo $google_login_url?>">Log in with Google</a>
	</div>
</section>