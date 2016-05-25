<section id="menu_mega_selection" class="small <?php if ($is_rush):?>rush<?php else:?>explore<?php endif?>">
	<div class="button_parent">
		<a <?php if (!$is_rush):?>class="rush"<?php endif?> href="/menu/rush">
			<h3><?php echo strtoupper($quick_menu_text)?></h3>
		</a>
	</div>
	<div class="button_parent">
		<a <?php if ($is_rush):?>class="explore"<?php endif?> href="/menu/explore">
			<h3><?php echo Strtoupper($full_menu_text)?></h3>
		</a>
	</div>
</section>

<section id="menu_filter" class="<?php if ($is_rush):?>rush<?php else:?>explore<?php endif?>">
	<?php echo form_open($form_action, array('method'=>'get', 'id'=>'filter_form'))?>
		<div class="search_container">
			<input id="search" class="ui-state-default" name="search" placeholder="e.g. burrito" 
				type="text" value="<?php echo $search_query ?>" />
			<button id="btn_location" type="button"></button>
			<div id="location_dialog" class="<?php if ($is_rush):?>rush<?php else:?>explore<?php endif?>" title="My location">
				<input type="text" value="<?php echo $location?>" class="ui-state-default" id="input_location" />
			</div>
			<button id="btn_filter" class="hidden" type="button"></button>
		</div>
		
		
		
		<div class="menu_filter_container">
			<div class="menu_filter_zone">
				<div>
					<p>Price range: <span id="price_slider_output"></span></p>
					<div class="filter_slider" id="price_slider"></div>
					<input type="hidden" name="price_min" id="price_filter_min"
						value="<?php echo $price_filter['min']?>"/>
					<input type="hidden" name="price_max" id="price_filter_max"
						value="<?php echo $price_filter['max']?>"/>
				</div>
				<div>
					<p>Minimum rating: <span id="rating_slider_output"></span></p>
					<div class="filter_slider" id="rating_slider"></div>
					<input type="hidden" name="rating_min" id="rating_filter_min"
						value="<?php echo $rating_filter?>"/>
				</div>
			</div>
			
			<div class="menu_filter_zone">
				<p>Categories:</p>
				<div id="menu_filter_categories">
					<?php foreach ($main_categories as $category):?>
						<input type="checkbox" id="rad_cat_<?php echo $category->id?>" 
							value="<?php echo $category->id?>" name="category[]" 
							<?php if (in_array($category->id, $chosen_categories)):?>checked<?php endif?> />
						<label for="rad_cat_<?php echo $category->id?>"><?php echo ucfirst(prevent_xss($category->name))?></label>
					<?php endforeach?>
				</div>
			</div>
		</div>
		
		<div class="menu_button_container">
			<button id="btn_map" class="<?php if (!$is_list):?>ui-state-active<?php endif?>">UPDATE MAP</button>
			<button id="btn_list" class="<?php if ($is_list):?>ui-state-active<?php endif?>">UPDATE LIST</button>
		</div>
	<?php echo form_close()?>
	
	<script>
		// update price slider's equivalent output
		var updatePriceSliderOutput = function(ui){
			if (ui==null){
				var low = $('#price_slider').slider('values', 0);
				var high = $('#price_slider').slider('values', 1);
			} else {
				var low = ui.values[0];
				var high = ui.values[1];
			}
			var max = $('#price_slider').slider('option', 'max');
			
			var output = '$'+low+' - ';
			if (high >= max)
				output += 'MAX';
			else
				output += '$'+high;
			
			$('#price_slider_output').html(output);
		}
		
		// update rating slider's equivalent output
		var updateRatingSliderOutput = function(ui){
			if (ui==null){
				var low = $('#rating_slider').slider('value');
			} else {
				var low = ui.value;
			}
			var max = $('#rating_slider').slider('option', 'max');
			
			var output = '&hearts; '+low/max*100+'%';
			
			$('#rating_slider_output').html(output);
		}
	
		// range sliders
		$( "#price_slider" ).slider({
			range: true,
			min: 0,
			max: 50,
			step: 10,
			values: [$('#price_filter_min').val(), $('#price_filter_max').val()],
			slide: function(ev, ui){
				$('#price_filter_min').val(ui.values[0]);
				$('#price_filter_max').val(ui.values[1]);
				updatePriceSliderOutput(ui);
			}
		});
		$( "#rating_slider" ).slider({
			min: 0,
			max: 5,
			step: 1,
			value: $('#rating_filter_min').val(),
			slide: function(ev, ui){
				$('#rating_filter_min').val(ui.value);
				updateRatingSliderOutput(ui);
			}
		});
		
		// initially update the ranged outputs
		updatePriceSliderOutput();
		updateRatingSliderOutput();
		
		// category filter checkboxes
		$("#menu_filter_categories").buttonset();
		
		// submit buttons
		var filter_button_click = function(e, is_list){
			e.preventDefault();
			
			// modify url action
			var url = $('#filter_form').attr('action');
			if (is_list)
				url = url.replace("/map", "/list");
			else
				url = url.replace("/list", "/map");
			$('#filter_form').attr('action', url).submit();
		}
		$( "#btn_map" ).button().click(function(e){
			filter_button_click(e, false);
		});
		$( "#btn_list" ).button().click(function(e){
			filter_button_click(e, true);
		});
		
		// filter button
		$("#btn_filter").button().click(function(e){
			if ($(this).hasClass("hidden")){
				// show filter
				$(".menu_filter_container").slideDown(100, function(){
					$(this).css("display", "flex");
				});
			} else {
				// hide filter
				$(".menu_filter_container").slideUp(100);
			}
			$(this).toggleClass("hidden");
		});
		
		// location dialog
		$("#location_dialog").dialog({
			autoOpen: false,
			modal: true,
			resizable: false,
			dialogClass: '<?php if ($is_rush):?>rush<?php else:?>explore<?php endif?>',
			buttons:[
				{
					icons: {
						primary: "ui-icon-check"
					},
					'class': 'ui-button-dialog',
					click: function(){
						$("#input_location").attr("name", "location");
						$("#input_location").appendTo($("#filter_form"));
						$('#filter_form').submit();
					}
				},
				{
					icons: {
						primary: "ui-icon-closethick"
					},
					'class': 'ui-button-dialog',
					click: function(){
						$(this).dialog("close");
					}
				}
			]
		})
		
		// location button
		$("#btn_location").button().click(function(e){
			$("#location_dialog").dialog("open");
		});
		
		// prevent default hover and focus behaviours on the buttons
		$("<?php if (!$is_list):?>#btn_map<?php else:?>#btn_list<?php endif?>").hover(function(){
			$(this).toggleClass( "ui-state-active", true );
		}).focusout(function(e){
			$(this).addClass( "ui-state-active", true );
			e.preventDefault();
			e.stopPropagation();
		});
	</script>
</section>