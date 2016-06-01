			<?php if (isset($is_my_profile) && $is_my_profile):?>
			<li class="food_item" id="new_food_li">
				<?php echo form_open("", array("id"=>"new_food_form"))?>
					<a class="food_pic"></a>
					<input id="new_food_pic" type="file" accept="image/*">
					
					<div class="new_food">
						<input type="text" placeholder="Dish name" name="name" id="new_food_name" />
						
						<div class="food_price">
							<h3>$</h3>
							<input type="text" name="price" id="new_food_price" />
						</div>
						
						<input type="text" placeholder="Alternate/ethnic name" name="alt_name" id="new_alt_name" />
					</div>
					<div class="new_food_buttons_wrapper">
						<div class="ui-dialog new_food_buttons">
							<button type="button" class="ui-button-dialog" id="btn_new_food_ok"></button>
							<button type="button" class="ui-button-dialog" id="btn_new_food_cancel"></button>
						</div>
					</div>
				<?php echo form_close()?>
			</li>
			<li class="food_item" id="btn_add_new_parent">
				<button id="btn_add_new">
				<h3 class="center">Add new dish +</h3>
				</button>
			</li>
			<?php endif?>
		</ul>
		
		<?php if (isset($show_more) && $show_more):?>
		<p class="center <?php if (isset($is_rush) && $is_rush):?>rush<?php else:?>explore<?php endif?>"><button class="btn_show_more">Show more</button></p>
		<?php endif?>
	</div>
</div>

<script type="text/javascript">
	$(".btn_show_more").button();

	<?php if (isset($is_my_profile) && $is_my_profile):?>
	$("#btn_add_new").button().click(function(){
		$("#new_food_li").show();
		$("#btn_add_new_parent").hide();
	});
	
	$("#btn_new_food_ok").button({
		icons: {
			primary: "ui-icon-check"
		}
	}).click(function(e){
		$("#new_food_form").submit(function(e){
			$.ajax({
				type:		"post",
				url:		"/vendor/food/new_food",
				data: 		$("#new_food_form").serialize(),
				success:	function(response){
					var respArr = $.parseJSON(response);
				
					if ("success" in respArr){
						successMessage("Saved");

						// add to list
						$(respArr["li_display"]).insertBefore("#new_food_li");
						
						// reset form
						$("#new_food_li").hide();
						$("#btn_add_new_parent").show();
						reset_form();
						
						// remove no food info
						$("#no_food_info").hide();
					} else {
						errorMessage(respArr["error"]);
						return respArr["error"];
					}
				},
				error:		function(){
					errorMessage("Unable to process");
				}
			});
			
			// prevent actual form submission
			e.preventDefault();
		}(e));
	});
	
	$("#btn_new_food_cancel").button({
		icons: {
			primary: "ui-icon-closethick"
		}
	}).click(function(){
		$("#new_food_li").hide();
		$("#btn_add_new_parent").show();
		reset_form();
	});
	
	var reset_form = function(){
		$("#new_food_name").val("");
		$("#new_alt_name").val("");
		$("#new_food_price").val("");
	}
	<?php endif?>
</script>