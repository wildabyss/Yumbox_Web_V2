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
		<h1 class="title"><?php echo $user->name?></h1>
		<?php if (!$is_my_profile && !$user->is_open):?>
		<h3 class="is_closed">KITCHEN CLOSED</h3>
		<?php elseif (!$is_my_profile):?>
		<h3 class="is_open">KITCHEN OPEN</h3>
		<?php endif?>
		<?php if ($user->descr != ""):?>
		<h3>ABOUT ME</h3>
		<p><?php echo $user->descr?></p>
		<?php endif?>
	</div>
</section>

<script>
	
</script>