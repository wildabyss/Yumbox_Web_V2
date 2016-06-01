<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/2.2.1/mustache.min.js"></script>

<style>
.kitchen-balloon ul {
    list-style-type: none;
    padding-left: 5px;
}
.kitchen-balloon ul li a {
    margin: 3px;
}
.kitchen-balloon ul li a.food-pic {
    display: inline-block;
    width: 50px;
    height: 50px;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    background-image: url("/imgs/image-not-available.gif");
    border: solid 1px #b5b3ac;
}
.kitchen-balloon ul li a.food-info {
    display: inline-block;
}
.kitchen-balloon ul li a.food-info span {
    display: block;
}
.kitchen-balloon ul li a.food-info span.food-price:before {
    content: "Price: ";
}
.kitchen-balloon ul li a.food-info span.food-rating:before {
    content: "Rating: ";
}
.kitchen-balloon ul li a.food-info span.food-prep-time:before {
    content: "Prep. time: ";
}
</style>

<section id="menu_map">
	<div class="map_wrapper">
		<div class="map_container" id="map"></div>
	</div>
</section>

<script>
	//global variables
	var map;
	var geocodingUrl = 'https://maps.googleapis.com/maps/api/geocode/json';
	var prev_infowindow = false;
	var sampleData = <?php echo json_encode($vendors); ?>;

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
        sampleData.forEach(function (kitchen) {
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
        //InfoWindow template
        var template = '\
<div class="kitchen-balloon">\
    <a class="kitchen-name" href="/vendor/profile/id/{{id}}">{{name}}</a>\
    <ul>\
        {{#foods}}\
        <li>\
            <a href="/menu/item/{{food_id}}" style="background-image: url(\'{{pic_path}}\')" class="food-pic"></a>\
            <a href="/menu/item/{{food_id}}" class="food-info">\
                <span class="food-name">{{food_name}}</span>\
                <span class="food-price">${{food_price}}</span>\
                <span class="food-rating">{{rating}}</span>\
                <span class="food-prep-time">{{prep_time}}</span>\
            </a>\
        </li>\
        {{/foods}}\
    </ul>\
</div>\
            ';
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
		//this is using a default location at Toronto downtown to show a map
		map = new google.maps.Map(document.getElementById('map'), {
			//zoom controls the range of the map. 0 is global. the bigger the number is, the closer it will be
			zoom: mapInfo.currentGeo.zoom
		});
		// center current location
		map.setCenter(new google.maps.LatLng(mapInfo.currentGeo.lat, mapInfo.currentGeo.lng));
        map.addListener('bounds_changed', throttle(function() {
            var bounds = map.getBounds();
            console.log(bounds.toJSON());
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
