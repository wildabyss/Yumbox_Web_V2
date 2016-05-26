<!DOCTYPE html>
<html>
	<head>
		<!-- content type -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="Content-Style-Type" content="text/css" />
		
		<!-- jquery -->
		<link href="/css/jquery-ui.min.css" rel="stylesheet" />
		
		<!-- layout and styles -->
		<title>Yumbox</title>
		<link rel="shortcut icon" href="/imgs/logo.ico" type="image/x-icon"/>
		<link rel="stylesheet" href="/css/common.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="/css/jqueryui-editable.css" type="text/css" media="screen" />
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,400italic|Montserrat" rel="stylesheet" type="text/css"/>
	</head>
	
	<body>
		<!-- jquery -->
		<script src="/js/jquery.min.js"></script>
		<script src="/js/jquery-ui.min.js"></script>
		<script src="/js/jquery.ui.touch-punch.min.js"></script>
		
		<!-- textarea autosizing -->
		<script src="/js/autosize.min.js"></script>
		
		<!-- x-editable -->
		<script src="/js/jqueryui-editable.min.js"></script>
		
		<!-- javascript cookie -->
		<script src="/js/js.cookie.js"></script>
		
		<!-- global js loading -->
		<script src="/js/global.js"></script>
		<script>
			// csrf token
			var csrfData = {};
			csrfData["<?php echo $this->security->get_csrf_token_name()?>"] = "<?php echo $this->security->get_csrf_hash()?>";
			
			// get current location
			var mapInfo = {};
			// get coordinates from cookies
			mapInfo.currentGeo = {
				lat: Cookies.get('latitude'),
				lng: Cookies.get('longitude')
			}; 

			if (mapInfo.currentGeo.lat === undefined || mapInfo.currentGeo.lng === undefined || mapInfo.currentGeo.lat == 'undefined' || mapInfo.currentGeo.lng == 'undefined'){
				// use geolocation
				navigator.geolocation.getCurrentPosition(function(position) {
					mapInfo.currentGeo = {
						lat: position.coords.latitude,
						lng: position.coords.longitude,
					};
				}, function() {

					// no location observed, use default location
					mapInfo.currentGeo = {
						lat: '<?php echo $location["latitude"]?>',
						lng: '<?php echo $location["longitude"]?>'
					};
				});
				
				// save to cookie
				Cookies.set('latitude', mapInfo.currentGeo.lat);
				Cookies.set('longitude', mapInfo.currentGeo.lng);

				// reload
				//location.reload();
			}
		</script>
	
		<div id="haze"></div>
		<div id="top_status"></div>
		<div id="global">
