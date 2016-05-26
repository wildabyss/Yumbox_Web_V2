<section id="menu_map">
	<div class="map_wrapper">
		<div class="map_container" id="map"></div>
	</div>
</section>

<script>
	//global variables
	var map;
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
		  $.ajax({
			url: geocodingUrl,
			dataType:'json',
			data: {
			  address: kitchen.address,  
			  key: 'AIzaSyAbFvvZUUwbJ-yieOa5g49ERWWLwmTHEh8'
			}
		  }).then(function(res) {   
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
	
	var initMap = function(){
		//this is using a default location at Toronto downtown to show a map
		map = new google.maps.Map(document.getElementById('map'), {
			//zoom controls the range of the map. 0 is global. the bigger the number is, the closer it will be
			zoom: 12
		});
		// center current location
		map.setCenter(new google.maps.LatLng(mapInfo.currentGeo.lat, mapInfo.currentGeo.lng));
		
		printMarkers();
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAofIUjndHtKWzUxJ34j4MhZ7fEgQFWY6I&callback=initMap" async defer></script>