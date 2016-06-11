<li>
	<?php if ($user_picture!==false):?>
	<a class="profile_pic" style="background-image:url('<?php echo $user_picture?>')"></a>
	<?php else:?>
	<a class="profile_pic"></a>
	<?php endif?>
	
	<div class="review_info">
		<span><?php echo prevent_xss($review->user_name)?></span>
		<p>&hearts; <?php echo $review->rating?>%</p>
		<?php if ($review->review != ""):?>
		<p>"<?php echo prevent_xss($review->review)?>"</p>
		<?php endif?>
	</div>
</li>