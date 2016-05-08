<section id="profile_intro">
	<div class="pic_wrapper">
		<a id="profile_pic"></a>
		<p>
			<a id="followers"><?php echo $num_followers?> followers</a>
			<?php if ($my_id !== false  && !$is_my_profile):?>
				<a id="follow_btn">+ Follow</a>
			<?php endif?>
		</p>
	</div>
	<div class="intro_wrapper">
		<h1><?php echo $user_name?></h1>
		<?php if (!$is_my_profile && !$user->is_open):?>
		<h3 class="closed">KITCHEN IS CLOSED</h3>
		<?php endif?>
		<h3>ABOUT ME</h3>
		<p><?php echo $user->descr?></p>
	</div>
</section>

<script>
	
</script>