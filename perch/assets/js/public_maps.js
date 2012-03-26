if (typeof(CMSMap) == 'undefined') {
	CMSMap	= {};
}

CMSMap.UI	= function()
{
	var init	= function() {
		if (CMSMap.maps.length) {			
			plot_maps();
		}
	};
	
	var plot_maps = function() {
		var i, l;
		for (i=0,l=CMSMap.maps.length; i<l; i++) {
			var data = CMSMap.maps[i];
			var img = document.getElementById(data.mapid);
			var mapdiv = document.createElement('div');
			mapdiv.setAttribute('id', 'd'+data.mapid);
			img.parentNode.insertBefore(mapdiv, img);
			img.parentNode.removeChild(img);
			mapdiv.style.width = data.width+'px';
			mapdiv.style.height = data.height+'px';
			
		    var latlng = new google.maps.LatLng(data.clat, data.clng);
		    var opts = {
				zoom: parseInt(data.zoom,10),
				center: latlng
		    };
			switch(data.type) {
				case 'roadmap'	: opts.mapTypeId = google.maps.MapTypeId.ROADMAP; break;
				case 'satellite': opts.mapTypeId = google.maps.MapTypeId.SATELLITE; break;
				case 'hybrid'	: opts.mapTypeId = google.maps.MapTypeId.HYBRID; break;
				case 'terrain'	: opts.mapTypeId = google.maps.MapTypeId.TERRAIN; break;
				default			: opts.mapTypeId = google.maps.MapTypeId.ROADMAP; break;
			}

		    var map = new google.maps.Map(mapdiv, opts);
		
			var point = new google.maps.LatLng(data.lat, data.lng);
			var marker = new google.maps.Marker({
				position: point, 
				map: map, 
				title: data.adr.replace(/\\/g, '')
			});
		}
	};
	
	return {
		init: init
	};
	
}();

CMSMap.Loader = function(){
	var func = CMSMap.UI.init;
	
	var oldonload = window.onload;
	if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		window.onload = function() {
			if (oldonload) {
				oldonload();
			}
			func();
		};
	}
}();

// Load Google maps
document.write('<scr'+'ipt type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"><'+'/sc'+'ript>');