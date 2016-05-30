			<?php if (isset($is_my_profile) && $is_my_profile):?>
			<li class="food_item" id="new_food_li">
				<a class="food_pic"></a>
				<div class="new_food">
					<input type="text" placeholder="Dish name" id="new_food_name" />
					
					<div class="food_price">
						<h3>$</h3>
						<input type="text" id="new_food_price" />
					</div>
					
					<input type="text" placeholder="Alternate/ethnic name"id="new_alt_name" />
				</div>
				<div class="new_food_buttons_wrapper">
					<div class="ui-dialog new_food_buttons">
						<button type="button" class="ui-button-dialog" id="btn_new_food_ok"></button>
						<button type="button" class="ui-button-dialog" id="btn_new_food_cancel"></button>
					</div>
				</div>
			</li>
			<li class="food_item">
				<a id="btn_add_new">
				<h3 class="center">Add new dish +</h3>
				</a>
			</li>
			<?php endif?>
		</ul>
	</div>
</div>

<script type="text/javascript">
	<?php if (isset($is_my_profile) && $is_my_profile):?>
	$("#btn_add_new").click(function(){
		$("#new_food_li").show();
	});
	
	$("#btn_new_food_ok").button({
		icons: {
			primary: "ui-icon-check"
		}
	});
	
	$("#btn_new_food_cancel").button({
		icons: {
			primary: "ui-icon-closethick"
		}
	}).click(function(){
		$("#new_food_li").hide();
	});
	<?php endif?>
</script>