if (typeof(Perch) == 'undefined') {
	Perch	= {};
	Perch.UI	= {};
	Perch.Apps	= {};
}

Perch.UI.Maps	= function()
{
	var init = function() {
		build_maps();
		
		// reload after reordering ajax load	
		$(window).on('Perch_Init_Editors', build_maps);
	};
	
	
	var build_maps	= function() {
		var map_fields = $('input.map_adr');
		
		if (map_fields.length) {
			map_fields.each(function(i,o){
				var self = $(o);
				var mapcont = self.parent().find('div.map');
				var img = mapcont.find('img').remove();
				
				var hide = false;

				var mapdiv = $('<div class="mapdiv"></div>').appendTo(mapcont);
				mapdiv.css({
					'width': mapcont.attr('data-width'),
					'height': mapcont.attr('data-height')
				});
									
				var mapid = mapcont.attr('data-mapid');
				
				if (get_field(mapid, 'clat')) {
					var latlng = new google.maps.LatLng(get_field(mapid, 'clat'), get_field(mapid, 'clng'));
					var opts = {
						zoom: parseInt(get_field(mapid, 'zoom'),10),
						center: latlng,
						mapTypeId: get_type(get_field(mapid, 'type'))
					};
				}else{
					var opts = {
						zoom: 10,
						center: new google.maps.LatLng(51.5, -0.11),
						mapTypeId: get_type('roadmap')
					};
					hide = true;
				}
			
				
				var map = new google.maps.Map(mapdiv.get(0), opts);
				
				var point = new google.maps.LatLng(get_field(mapid, 'lat'), get_field(mapid, 'lng'));
				var marker = new google.maps.Marker({
					position: point, 
					map: map
				});
				
				// zoom
				google.maps.event.addListener(map, 'zoom_changed', function() {
					set_field(mapid, 'zoom', map.getZoom());
				});
				
				// centre
				google.maps.event.addListener(map, 'center_changed', function() {
					var p = map.getCenter();
					set_field(mapid, 'clat', p.lat());
					set_field(mapid, 'clng', p.lng());
				});
				
				// type
				google.maps.event.addListener(map, 'maptypeid_changed', function() {
					set_field(mapid, 'type', map.getMapTypeId());
				});
				
				// click - markers
				google.maps.event.addListener(map, 'click', function(e) {
					var lat = e.latLng.lat();
					var lng = e.latLng.lng();
					marker.setPosition(new google.maps.LatLng(lat, lng));
					set_field(mapid, 'lat', lat);
					set_field(mapid, 'lng', lng);
				});

				
				var find_button = $('<a href="#" class="compact-button">'+mapcont.attr('data-btn-label')+'</a>');
				find_button.insertAfter(self);
				find_button.click(function(e){
					e.preventDefault();
					mapcont.removeClass('offscreen');
					
					var adr = $(this).parent().find('input.map_adr').val();

					geocoder = new google.maps.Geocoder();
					geocoder.geocode( { 'address': adr}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							map.setCenter(results[0].geometry.location);
							marker.setPosition(results[0].geometry.location);
							set_field(mapid, 'lat', results[0].geometry.location.lat());
							set_field(mapid, 'lng', results[0].geometry.location.lng());
						}
					});
					
				});
				
				if (hide) mapcont.addClass('offscreen');
			});
		}
	};
	
	var get_type = function(type) {
		switch(type) {
			case 'roadmap'	: return google.maps.MapTypeId.ROADMAP; break;
			case 'satellite': return google.maps.MapTypeId.SATELLITE; break;
			case 'hybrid'	: return google.maps.MapTypeId.HYBRID; break;
			case 'terrain'	: return google.maps.MapTypeId.TERRAIN; break;
			default			: return google.maps.MapTypeId.ROADMAP; break;
		}
	};
	
	var get_field = function(mapid, field) {
		var f = $('#'+mapid+'_'+field);
		
		if (f.length) {
			return f.val();
		}else{
			$('div[data-mapid='+mapid+']').append('<input type="hidden" name="'+mapid+'_'+field+'" id="'+mapid+'_'+field+'" value="" />');
		}
		
		return false;
	};
	
	var set_field = function(mapid, field, value) {
		get_field(mapid, field);
		$('#'+mapid+'_'+field).val(value);
	};

	return {
		init: init
	};
	
}();

jQuery(function($) { Perch.UI.Maps.init(); });