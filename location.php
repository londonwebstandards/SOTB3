<?php include("inc/pageopen.php"); ?>
<?php include("inc/navigation.php"); ?>

<div id="location" class="eleven columns">
    <h1>Venue - Ravensbourne</h1>
    <p id="address">6 Penrose Way, London, SE10 0EW</p>
    <section>
        <div id="transport">
            <h2>Getting There</h2>
                        <div class="about-the-venue">
                    <p>Ravensbourne is a stunning new building on the Greenwich Peninsula, located next door to The O2.
                    For more details on the space and how to get there, go to <a href="http://www.rave.ac.uk/">www.rave.ac.uk</a>.</p>
                    <div id="map">
                        <img src="/img/map-default.png" alt="Map showing Ravensbourne, next to North Greenwich tube station and the O2.">
                    </div>
                </div>
            <dl class="public-transport">
                <dt>Tube</dt>
                <dd>
                    <strong>North Greenwich station on the Jubilee Line</strong>.
                    Please note that although there are no scheduled closures on the Jubilee Line on 20th April, this could change at short notice. Please check
                    <a href="http://www.tfl.gov.uk/tfl/livetravelnews/realtime/tube/default.html" title="The TFL travel news">TfL's live travel news</a> before you travel.
                </dd>
                <dt>Overground Train</dt>
                <dd>
                    Take <a href="http://www.southeasterrailwai.co.uk" title="South Eastern's Website">South Eastern</a> rail to Charlton
                    station. Get the 486 bus from outside, in the direction of North
                    Greenwich (your destination). Trains run regularly from Canon
                    Street and London Bridge.
                </dd>
                <dt>Bus</dt>
                <dd>
                    These take slightly longer but are an option. The following buses
                    <a href="http://www.tfl.gov.uk/tfl/gettingaround/maps/buses/pdf/northgreenwich-2191.pdf"
                    title="Map of bus stop in North Geenwich">stop at North Greenwich</a>:
                    <dl>
                        <dt>From inner London</dt>
                        <dd>188 - from Russell Square</dd>
                        <dd>108 - from Stratford and Lewisham</dd>
                        <dt>From outer London</dt>
                        <dd>129 - from Greenwich (stops at Greenwich DLR station)</dd>
                        <dd>132 - from Bexleyheath</dd>
                        <dd>486 - from Bexleyheath (stops at Charlton station)</dd>
                        <dd>422 - from Bexleyheath</dd>
                        <dd>161 - from Chislehurst (stops near Charlton station)</dd>
                        <dd>472 - from Thamesmead (stops near Charlton station)</dd>
                    </dl>
                </dd>
                <dt>Docklands Light Railway (DLR)</dt>
                <dd>
                    If you're travelling from inner London, you can catch the <abbr title="Docklands Light Railway">DLR</abbr>
                    from Bank to Cutty Sark (a.k.a Greenwich, it's on the Lewisham line). From there you can catch the 129 bus to North Greenwich.
                </dd>
                <dt>Arriving by car?</dt>
                <dd>Please be aware there is no on-site parking at the venue.</dd>
            </dl>
        </div>

    </section>
</div>
<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script>
			function init() {
				var latlng = new google.maps.LatLng(51.501369999999994,0.0046129999999999999),
				opts = {
					zoom: 13,
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				},
				map = new google.maps.Map(document.getElementById("map"), opts);
				infowindow = new google.maps.InfoWindow({
					content: "Ravensbourne College of Design and Communication"
				})
				marker = new google.maps.Marker({
					position: latlng,
					map: map
				});
				google.maps.event.addListener(marker, 'click', function(){
					infowindow.open(map,marker);
				});
			}
			var addEvent = function( obj, type, fn ) {
				if (obj.addEventListener) obj.addEventListener(type, fn, false);
				else if (obj.attachEvent) obj.attachEvent('on' + type, function() { return fn.apply(obj, new Array(window.event));});
			}
			addEvent(window, 'load', init);
		</script>

<?php include("inc/sponsorsSide.php"); ?>
<?php include("inc/photosSide.php"); ?>
<?php include("inc/communityPartners.php"); ?>
<?php include("inc/footer.php"); ?>