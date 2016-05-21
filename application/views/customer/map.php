<section id="menu_map">
	<div class="map_wrapper">
		<div class="map_container"></div>
	</div>
</section>

<script>
	//global variables
	var map;
	var mapInfo = {};
	var geocodingUrl = 'https://maps.googleapis.com/maps/api/geocode/json';
	var prev_infowindow =false;  
	var sampleData = [{
		"name":"Jimmy's",
		"category":["Chinese","Canadian"],
		"rate":0.95,
		"address":"485 Queen Street W, Toronto"
		},{
		"name":"Amy's",
		"category":["Vietnam","Canadian"],
		"rate":0.94,
		"address":"300 Queen Street W, Toronto"
		},{
		"name":"Leila's",
		"category":["Italian","Canadian"],
		"rate":0.94,
		"address":"100 Queen Street W, Toronto"
		},{
		"name":"Maya's",
		"category":["Canadian"],
		"rate":0.95,
		"address":"200 Queen Street W, Toronto"
	}];
	
	var printMarkers = function(){
		sampleData.forEach(function(kitchen){
		  console.log(kitchen.address);
		  $.ajax({
			url: geocodingUrl,
			dataType:'json',
			data: {
			  address: kitchen.address,  
			  key: 'AIzaSyAbFvvZUUwbJ-yieOa5g49ERWWLwmTHEh8'
			}
		  }).then(function(res) {   
			console.log(res.results[0].geometry.location); 
			console.log(kitchen.name);
			var kitchenLoc =  res.results[0].geometry.location;
			//create markers here
			printOneMarker(kitchenLoc,kitchen);
		  });
		});
	};
	
	var printOneMarker = function(markerPos,kitchen){
		var marker = new google.maps.Marker({
		  position: markerPos,
		  map: map,
		  title: kitchen.name
		});
		
		//add infowindow
		var categoryString = "";
		for (var i = 0; i < kitchen.category.length; i++){
			categoryString += "<p class='tag'>" + kitchen.category[i] + "</p>";
		}
		var contentString = "<div class='kitchenInfo>'" + 
			"<h2>" + kitchen.name + "</h2>" + categoryString +
			"<p>" +  (kitchen.rate)*100 + "%</p> </div>";
		var infowindow = new google.maps.InfoWindow({
			content: contentString
		});
		marker.addListener('click', function() {
			if( prev_infowindow ) {
				prev_infowindow.close();
			}
			prev_infowindow = infowindow;
			infowindow.open(map, marker);
		});
	};
	
	var centerCurrentLocation = function(){
		navigator.geolocation.getCurrentPosition(function(position) {
			mapInfo.currentGeo = {
				lat: position.coords.latitude,
				lng: position.coords.longitude,
			}
			console.log(mapInfo.currentGeo);
			map.setCenter(new google.maps.LatLng(mapInfo.currentGeo.lat, mapInfo.currentGeo.lng));
		}, function() {
			console.log("Error!!!!!!!!!");
		});
	};
	
	var initMap = function(){
		centerCurrentLocation();
		//this is using a default location at Toronto downtown to show a map
		map = new google.maps.Map(document.getElementById('map'), {
			center: {lat: 43.6532, lng: -79.3832},
			//zoom controls the range of the map. 0 is global. the bigger the number is, the closer it will be
			zoom: 12
		});
		printMarkers();
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAofIUjndHtKWzUxJ34j4MhZ7fEgQFWY6I&callback=initMap" async defer></script>