

<section id="menu_map">
	<div class="map_wrapper">
		<div class="map_container" id="map"></div>
	</div>
</section>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/2.2.1/mustache.min.js"></script>
<script>
	//global variables
	var map;
	var geocodingUrl = 'https://maps.googleapis.com/maps/api/geocode/json';
	var prev_infowindow = false;
	var food_data = <?php echo json_encode($foods_for_map); ?>;
	
	// InfoWindow template
	var template = <?php echo json_encode($template)?>;
	template = template.template;

	var composeAddress = function(kitchen) {
		if (!kitchen.address || kitchen.address.trim() == "") {
			return false;
		}
		var arr = [kitchen.address.trim()];
		if (kitchen.city && kitchen.city.trim() !== "") {
			arr.push(kitchen.city.trim());
		}
		if (kitchen.province && kitchen.province.trim() !== "") {
			arr.push(kitchen.province.trim());
		}
		if (kitchen.postal_code && kitchen.postal_code.trim() !== "") {
			arr.push(kitchen.postal_code.trim());
		}
		if (kitchen.country && kitchen.country.trim() !== "") {
			arr.push(kitchen.country.trim());
		}
		return arr.join(", ");
	};

	var printMarkers = function() {
		food_data.forEach(function (kitchen) {
			kitchen.full_address = composeAddress(kitchen);
			kitchen.latitude = parseFloat(kitchen.latitude);
			kitchen.longitude = parseFloat(kitchen.longitude);
			if (!isNaN(kitchen.latitude) && !isNaN(kitchen.longitude)) {
				printOneMarker(kitchen);
			}
			else if (kitchen.full_address) {
				$.ajax({
					url: geocodingUrl,
					dataType: 'json',
					data: {
						address: kitchen.address
					}
				}).then(function (res) {
					kitchen.latitude = res.results[0].geometry.location.lat;
					kitchen.longitude = res.results[0].geometry.location.lng;
					//create markers here
					printOneMarker(kitchen);
				});
			}
		});
	};
	
	var printOneMarker = function(kitchen) {
		var marker = new google.maps.Marker({
		  position: {
			  lat: kitchen.latitude,
			  lng: kitchen.longitude
		  },
		  map: map,
		  title: kitchen.name,
		  icon: "/imgs/ic_restaurant_black_24px.svg"
		});
		
		//add info window
		var infoWindow = new google.maps.InfoWindow({
			content: Mustache.render(template, kitchen)
		});
		marker.addListener('click', function() {
			if (prev_infowindow) {
				prev_infowindow.close();
			}
			prev_infowindow = infoWindow;
			infoWindow.open(map, marker);
		});
	};
	
	var initMap = function() {
		map = new google.maps.Map(document.getElementById('map'), {
			//zoom controls the range of the map. 0 is global. the bigger the number is, the closer it will be
			zoom: mapInfo.currentGeo.zoom
		});
		// center current location
		map.setCenter(new google.maps.LatLng(mapInfo.currentGeo.lat, mapInfo.currentGeo.lng));
		map.addListener('bounds_changed', throttle(function() {
			var bounds = map.getBounds();
		}, 1000));
		map.addListener('center_changed', throttle(function() {
			var center = map.getCenter();
			Cookies.set("latitude", center.lat());
			Cookies.set("longitude", center.lng());
		}, 100));
		map.addListener('zoom_changed', function() {
			var zoom = map.getZoom();
			Cookies.set("zoom", zoom);
		});

		printMarkers();
	}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $this->config->item('map_api_key')?>&callback=initMap" async defer></script>
