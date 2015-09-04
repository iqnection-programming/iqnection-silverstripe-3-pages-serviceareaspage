
var mapIcon;
var map;
var bounds;
var address_objects = [];
$(document).ready(function(){
	
	$('input, option, textarea').focus(function(){
		$("label[for="+$(this).attr('id')+"]").hide();
	});
	
	$('input, option, textarea').blur(function(){
		if(!$(this).val())$("label[for="+$(this).attr('id')+"]").show();
	});
	
	$('#Form_ServiceAreasForm').validate({
		useNospam: true	
	});
});

$(window).load(function(){
	var myOptions = {
	  zoom: 0,
	  zoomControl:false,
	  streetViewControl:false,
	  mapTypeControl:false,
	  mapTypeId: google.maps.MapTypeId.$MapType
	};
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	bounds = new google.maps.LatLngBounds();
	for(var i = 0; i < address_objects.length; i++){
		addMarker(address_objects[i]);	
	}
	map.fitBounds(bounds);
	//If the map is currently zoomed in close than we\'d like, pull it back.
	//If the map is right at it\'s bounds, pull it back by 1;
	google.maps.event.addListenerOnce(map, "bounds_changed", function() { 
		var new_zoom = this.getZoom() > 12 ? 12 : this.getZoom()-1;
		map.setZoom(new_zoom); 
	});
});

function addMarker(object) {
	var latlng = new google.maps.LatLng(object.LatLng[0], object.LatLng[1]);
	var marker = new google.maps.Marker({
		map: map,
		position: latlng,
		title:object.Title,
		icon:mapIcon
	});
	var heading = object.Title ? "<h5>"+object.Title+"</h5>" : "";
	var contentString = '<div id="map-popup">'+heading+'<p>'+object.Address+'</p><p><a href="https://maps.google.com/maps?q='+object.Address+'" target="_blank">Get Driving Directions</a></p></div>';
	var infowindow = new google.maps.InfoWindow({
		content: contentString
	});
	google.maps.event.addListener(marker, 'click', function() {
		infowindow.open(map,marker);
	});
	bounds.extend(latlng);
}

//Draw service area http://itouchmap.com/latlong.html
/*var service_area_coords = [
	new google.maps.LatLng(40.681332,-75.819397),
	new google.maps.LatLng(40.926657,-75.396423),
	new google.maps.LatLng(40.580307,-75.066833),
	new google.maps.LatLng(40.287627,-74.638367),
	new google.maps.LatLng(39.791092,-74.833374),
	new google.maps.LatLng(39.816694,-75.84137),
	new google.maps.LatLng(40.334681,-75.577698)
];
service_area = new google.maps.Polygon({
	paths: service_area_coords,
	strokeColor: "#ed9248",
	strokeOpacity: 0.75,
	strokeWeight: 2,
	fillColor: "#ed9248",
	fillOpacity: 0.25
});
service_area.setMap(map);*/