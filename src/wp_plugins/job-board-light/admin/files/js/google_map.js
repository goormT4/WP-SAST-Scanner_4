var geocoder;
								jQuery(document).ready(function($) {									
									var map;
									var marker;

									 geocoder = new google.maps.Geocoder();
									

									function geocodePosition(pos) {
									  geocoder.geocode({
									    latLng: pos
									  }, function(responses) {
									    if (responses && responses.length > 0) {
									      updateMarkerAddress(responses[0].formatted_address);
									    } else {
									      updateMarkerAddress('Cannot determine address at this location.');
									    }
									  });
									}

									function updateMarkerPosition(latLng) {
									  jQuery('#latitude').val(latLng.lat());
									  jQuery('#longitude').val(latLng.lng());	
									
										codeLatLng(latLng.lat(), latLng.lng());
									}

									function updateMarkerAddress(str) {
									  jQuery('#address').val(str);
									}

									function initialize() {
									  var have_lat =dirpro.lat;
									  if(have_lat!=''){
										 var latlng = new google.maps.LatLng(dirpro.lat,dirpro.lng);
									 
									  } else{
										 
										  var latlng = new google.maps.LatLng(40.748817, -73.985428);
									  }	
									  
									  var mapOptions = {
									    zoom: 2,
									    center: latlng
									  }

									  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
										
									  geocoder = new google.maps.Geocoder();

									  marker = new google.maps.Marker({
									  	position: latlng,
									    map: map,
									    draggable: true
									  });

									  // Add dragging event listeners.
									  google.maps.event.addListener(marker, 'dragstart', function() {
									    updateMarkerAddress('Please Wait Dragging...');
									  });
									  
									  google.maps.event.addListener(marker, 'drag', function() {
									    updateMarkerPosition(marker.getPosition());
									  });
									  
									  google.maps.event.addListener(marker, 'dragend', function() {
									    geocodePosition(marker.getPosition());
									  });

									}

									google.maps.event.addDomListener(window, 'load', initialize);
									
									

									jQuery(document).ready(function() { 
									         
									  initialize();
									          
									  jQuery(function() {
										  
										  
										  
										   var input = document.getElementById('address');
											var autocomplete = new google.maps.places.Autocomplete(input);
												google.maps.event.addListener(autocomplete, 'place_changed', function () {
												var place = autocomplete.getPlace();
											
												document.getElementById('latitude').value = place.geometry.location.lat();
												document.getElementById('longitude').value = place.geometry.location.lng(); 
												
												var location = new google.maps.LatLng(place.geometry.location.lat(), place.geometry.location.lng());
												codeLatLng(place.geometry.location.lat(), place.geometry.location.lng());
												
												marker.setPosition(location);
												map.setZoom(16);
												map.setCenter(location);
												
											});
											
											
																							  

									    
									  });
									  
									  //Add listener to marker for reverse geocoding
									  google.maps.event.addListener(marker, 'drag', function() {
									    geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
									      if (status == google.maps.GeocoderStatus.OK) {
									        if (results[0]) {
												
									          jQuery('#address').val(results[0].formatted_address);
									          jQuery('#latitude').val(marker.getPosition().lat());
									          jQuery('#longitude').val(marker.getPosition().lng());
									        }
									      }
									    });
									  });
									  
									});

								});
								// For city country , zip
								function codeLatLng(lat, lng) {
									var city;
									var postcode;
									var state;
									var country;	
									var latlng = new google.maps.LatLng(lat, lng);
									geocoder.geocode({'latLng': latlng}, function(results, status) {
									  if (status == google.maps.GeocoderStatus.OK) {
									
										if (results[1]) {
									
										//find country name
											 for (var i=0; i<results[0].address_components.length; i++) {
											for (var b=0;b<results[0].address_components[i].types.length;b++) {
											
												//there are different types that might hold a city admin_area_lvl_1 usually does in come cases looking for sublocality type will be more appropriate
												if (results[0].address_components[i].types[b] == "locality") {
													//this is the object you are looking for
													city= results[0].address_components[i];		
													//break;
												}
												if (results[0].address_components[i].types[b] == "country") {
													country= results[0].address_components[i];
												}
												if (results[0].address_components[i].types[b] == "postal_code") {													
													postcode= results[0].address_components[i];													
												}	
												
											}
										}
										//city data
										jQuery('#city').val('');
										jQuery('#postcode').val('');
										
										jQuery('#address').val(results[0].formatted_address); 
										jQuery('#city').val(city.long_name);
										jQuery('#postcode').val(postcode.long_name);
										jQuery('#country').val(country.long_name);
									
										


										} else {
										  
										}
									  } else {
										
									  }
									});
								  }