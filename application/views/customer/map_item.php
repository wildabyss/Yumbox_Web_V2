<div class="kitchen-balloon">
	<a class="kitchen-name" href="/vendor/profile/id/{{id}}">{{name}}</a>
	<ul>
		{{#foods}}
		<li>
			<a href="/menu/item/{{food_id}}" style="background-image: url('{{food_pic}}?width=60&height=60')" class="food-pic"></a>
			<a href="/menu/item/{{food_id}}" class="food-info">
				<span class="food-name">{{food_name}}</span>
				<span class="food-price">${{food_price}}</span>
				<span class="food-rating">&hearts; {{rating}}%</span>
				<span class="food-prep-time">{{prep_time}}</span>
			</a>
		</li>
		{{/foods}}
	</ul>
</div>