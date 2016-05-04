<!doctype html>
<html lang="en"> 
<head>
	<meta charset="UTF-8">
	<title></title>
	
	<style type="text/css">
		body {
			font-family: sans-serif;
		}
	
	    .main {
			border: 1px solid black;
			box-shadow: 10px 10px 5px #888;
			border-radius: 12px;
			padding: 20px;
			background-color: #ddd;
			margin: 25px;
			width: 450px;
			margin-left:auto;
			margin-right:auto;	
		}
		
		.logo {
			width:275px;
			margin-left: auto;
			margin-right: auto;
			display: block;
			padding: 15px;
		}
		
		.container {
			-webkit-perspective: 300; perspective: 300;
		}
		.boiling { background-color: #CC0033; }
		.hot { background-color: #CC3300; }
		.warm { background-color: #FF9900; }
		.lukewarm { background-color: #FFFFCC; }
		.cold { background-color: #CCFFFF; }
		.freezing { background-color: #66CCFF; }
	</style>
	<script src="../scripts/jquery-1.6.4.min.js"></script>  
	<script src="../scripts/geo-ab.js"></script>

</head>
<body>
	<div class="main">
	    <h2>Device Orientation</h2>
	    <table>
	    	<tr>
	    		<td>Event Supported</td>
	    		<td id="doEvent"></td>
	    	</tr>
	    	<tr>
	    		<td>Tilt Left/Right [tiltLR]</td>
	    		<td id="doTiltLR"></td>
	    	</tr>
	    	<tr>
	    		<td>Tilt Front/Back [tiltFB]</td>
	    		<td id="doTiltFB"></td>
	    	</tr>
	    	<tr>
	    		<td>Direction [direction]</td>
	    		<td id="doDirection"></td>
	    	</tr>
	    	<tr>
	    		<td>Compass [direction]</td>
	    		<td id="doCompass"></td>
	    	</tr>
	    	<tr>
	    		<td>Motion Up/Down</td>
	    		<td id="doMotionUD"></td>
	    	</tr>
	    	<tr>
	    		<td>Orientation</td>
	    		<td id="doOrientation"></td>
	    	</tr>
	    	<tr>
	    		<td>Bearing</td>
	    		<td id="doBearing"></td>
	    	</tr>
	    	<tr>
	    		<td>Warmth</td>
	    		<td id="doWarmth"></td>
	    	</tr>
	    </table>
	</div>
	
	<div class="container" style="-webkit-perspective: 300; perspective: 300;">
		<img src="html5_logo.png" id="imgLogo" class="logo">
	</div>
	
	<script type="text/javascript">
		jQuery(window).ready(function(){  
			init();  
			initiate_watchlocation();  
		});  
	
		var watchProcess = null;  
		var brng = 0;  
	
		function initiate_watchlocation() {
			if (watchProcess == null) {
				watchProcess = navigator.geolocation.watchPosition(handle_geolocation_query, handle_errors);
			}  
		}  
	
		function stop_watchlocation() {
			if (watchProcess != null){
				navigator.geolocation.clearWatch(watchProcess);
				watchProcess = null;
			}
		}  
	
		function handle_errors(error){
			switch(error.code){
				case error.PERMISSION_DENIED: alert("user did not share geolocation data");
				break;
				
				case error.POSITION_UNAVAILABLE: alert("could not detect current position");
				break;
				
				case error.TIMEOUT: alert("retrieving position timedout");
				break;
				
				default: alert("unknown error");
				break;
			}
		}  

		var count = 0;

 	function handle_geolocation_query(position) {
		lat=position.coords.latitude;
		lon=position.coords.longitude;  
    	lat1=lat;
		lon1=lon;
		lat2=52.35419043; lon2=4.882811575;
//		lat2=52.3520000; lon2=4.8850000;
//		lat2=52.3540000; lon2=4.8900000;
//		lat2=52.3580000; lon2=4.8910000;
//		lat2=52.3640000; lon2=4.8870000;
//		lat2=52.3740000; lon2=4.8500000;
		lat2=52.3640000; lon2=4.8170000; 

		var d = getDistance(lat1,lon1,lat2,lon2);
		brng = getBearing(lat1,lon1,lat2,lon2);	
		if(brng<0)brng+=360;

		var difStraight=d;
		$("body").removeClass('boiling , hot, warm, lukewarm, cold, freezing');
		if(difStraight<0.1){
			warmthStr="boiling";
			$("body").addClass('boiling');
		}else if(difStraight<0.3){
			warmthStr="hot";
			$("body").addClass('hot');
		}else if(difStraight<0.6){
			warmthStr="warm";
			$("body").addClass('warm');
		}else if(difStraight<1){
			warmthStr="lukewarm";
			$("body").addClass('lukewarm');
		}else if(difStraight<3){
			warmthStr="cold";
			$("body").addClass('cold');
		}else{
			warmthStr="freezing";
			$("body").addClass('freezing');
		}
		warmthStr+=" | Distance: " + difStraight+", Bearing: "+brng+" | Lat: "+lat+", Lon: "+lon;
		document.getElementById("doWarmth").innerHTML = warmthStr;
	}

	
		function init() {
//	browser detection - iOS/Safari
			if (window.DeviceOrientationEvent) {
				document.getElementById("doEvent").innerHTML = "DeviceOrientation";
				window.addEventListener('deviceorientation', function(eventData) {
					var tiltLR = eventData.gamma;					
					var tiltFB = eventData.beta;
					var dir = eventData.alpha
					var compass = eventData.webkitCompassHeading
					var motUD = null;					
					var orientation = window.orientation;
					deviceOrientationHandler(tiltLR, tiltFB, dir, compass, motUD, orientation);
					}, false);
//	browser detection - Mozilla/Firefox
			} else if (window.OrientationEvent) {
				document.getElementById("doEvent").innerHTML = "MozOrientation";
				window.addEventListener('MozOrientation', function(eventData) {
					var tiltLR = eventData.x * 90;					
					var tiltFB = eventData.y * -90;					
					var dir = null;
					var compass = null;
					var motUD = eventData.z;
					var orientation = null;
					deviceOrientationHandler(tiltLR, tiltFB, dir, compass, motUD, orientation);
					}, false);
//	browser detection - not supported
			} else {
				document.getElementById("doEvent").innerHTML = "Not supported on your device or browser.  Sorry."
			}
		}
	
		function deviceOrientationHandler(tiltLR, tiltFB, dir, compass, motionUD, orientation) {
			arrowRotate = (brng-compass)-orientation;

			document.getElementById("doTiltLR").innerHTML = Math.round(tiltLR);
			document.getElementById("doTiltFB").innerHTML = Math.round(tiltFB);
			document.getElementById("doDirection").innerHTML = Math.round(dir);
			document.getElementById("doCompass").innerHTML = Math.round(compass);
			document.getElementById("doMotionUD").innerHTML = motionUD;
			document.getElementById("doOrientation").innerHTML = orientation;
			document.getElementById("doBearing").innerHTML = brng;
			
			// Apply the transform to the image
//			document.getElementById("imgLogo").style.webkitTransform = "rotate("+ tiltLR +"deg) rotate3d(1,0,0, "+ (tiltFB*-1)+"deg)";
//			document.getElementById("imgLogo").style.MozTransform = "rotate("+ tiltLR +"deg)";
//			document.getElementById("imgLogo").style.transform = "rotate("+ tiltLR +"deg) rotate3d(1,0,0, "+ (tiltFB*-1)+"deg)";
			document.getElementById("imgLogo").style.webkitTransform = "rotate("+arrowRotate+"deg) rotate3d(1,0,0, "+ (tiltFB*-1)+"deg)";
			document.getElementById("imgLogo").style.MozTransform = "rotate("+arrowRotate+"deg)";
			document.getElementById("imgLogo").style.transform = "rotate("+arrowRotate+"deg) rotate3d(1,0,0, "+ (tiltFB*-1)+"deg)";
		}
	</script>
</body>
</html>