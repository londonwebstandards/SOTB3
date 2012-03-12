<!doctype html>

<!--[if lt IE 7 ]><html lang="en" class="ie6"><![endif]-->
<!--[if IE 7 ]><html lang="en" class="ie7"><![endif]-->
<!--[if IE 8 ]><html lang="en" class="ie8"><![endif]-->
<!--[if IE 9 ]><html lang="en" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="en"><!--<![endif]-->
	<head>
		<meta charset="utf-8"><!-- test -->
		<title>State Of The Browser - London Web Standards</title>
		<link rel="stylesheet" href="./stylesheets/application.css">
		<link rel="stylesheet" href="/js/colorbox.css">
		<!--[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<meta name="viewport" content="width=1024" />
	</head>
	<body class="vevent">
		<script type="text/javascript">document.getElementsByTagName('body')[0].className+=' jsEnabled'</script>
		<header>
			<h1 class="summary">
				<span>State Of The Browser</span>
			</h1>
			<h2 class="date">
				<span>Ravensbourne, 9 to 5pm Saturday, March 19th 2011</span>
			</h2>
		</header>
		<div class="browser-logos">
			<ul class="logos">
				<li>
					<a href="http://www.mozilla.com/en-US/firefox/" class="firefox">
						<img src="./images/firefox.png" alt="Firefox">
					</a>
				</li>
				<li>
					<a href="http://www.opera.com/" class="opera">
						<img src="./images/opera.png" alt="Opera">
					</a>
				</li>
				<li>
					<a href="http://www.beautyoftheweb.com/" class="ie">
						<img src="./images/ie9.png" alt="Internet Explorer">
					</a>
				</li>
				<li>
					<a href="http://www.google.com/chrome/intl/en-GB/more/index.html" class="chrome">
						<img src="./images/chrome.png" alt="Chrome">
					</a>
				</li>
			</ul>
		</div>
		<nav class="main">
			<ul id="nav">
				<li>
					<a href="#who" class="scroll">Who</a>
				</li>
				<li>
					<a href="#register" class="scroll">Register</a>
				</li>        
				<li>
					<a href="#location" class="scroll">Where</a>
				</li>
				<li>
					<a href="./sponsors.php">Sponsors</a>
				</li>
				<li>
                    <a href="./schedule.php">Schedule</a>
                </li>
			</ul>
		</nav>
		<div class="content">
			<section id="tickets">
				<p><a href="http://www.vimeo.com/tag:lwsbrowser">Watch videos from the day</a></p>
			</section>
			<div class="most-important">
				<section id="register">
					<h1>Sign Up For More Information</h1>
					<p>Join the mailing list for this event.</p>
	  				<!-- Begin MailChimp Signup Form -->
					<!--[if IE]>
						<style type="text/css" media="screen">
							#mc_embed_signup fieldset {position: relative;}
							#mc_embed_signup legend {position: absolute; top: -1em; left: .2em;}
						</style>
					<![endif]--> 
					<!--[if IE 7]>
						<style type="text/css" media="screen">
							.mc-field-group {overflow:visible;}
						</style>
					<![endif]-->
					<div id="mc_embed_signup">
						<form action="http://londonwebstandards.us2.list-manage.com/subscribe/post?u=c450c49f183aa866b73ae2844&amp;id=acfefc4dd6" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
							<div class="mc-field-group">
								<label for="mce-EMAIL">Email Address</label>
								<input type="text" value="" name="EMAIL" class="required email" id="mce-EMAIL">
							</div>
							<div id="mce-responses">
								<div class="response" id="mce-error-response"></div>
								<div class="response" id="mce-success-response"></div>
							</div>
							<div>
								<input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="btn">
							</div>
							<a href="#" id="mc_embed_close" class="mc_embed_close" style="display: none;">Close</a>
						</form>
					</div>
				</section>
<section id="blog">
<h1>From the blog:</h1>
<?php
        require_once("rss_fetch.inc");
        $url = "http://www.londonwebstandards.org/category/lwsbrowser/feed/";
        $rss = fetch_rss( $url );

	$post = $rss->items[0];
        $href = $post['link'];
        $title = $post['title'];
        echo "<p><a href=$href>$title</a></p>"; 
?>
</section>

			</div>
			<section id="what">
				<h1>What is State of the Browser?</h1>
				<p>Representatives from <a href="http://www.opera.com/">Opera</a>, <a href="http://www.google.com/chrome/intl/en-GB/more/index.html">Google</a>, <a href="http://www.mozilla.com/en-US/firefox/">Mozilla</a> and <a href="http://www.beautyoftheweb.com/">Microsoft</a> will be on hand to walk attendees through each of their web browsers. You'll get the skinny on how they've implemented exciting new technologies and find out where the web is headed at breakneck speed. Through keynotes, Q&amp;As, breakout sessions and socialising, you'll get a better understanding than ever before of the browser in 2011.</p>
				<p>Speakers include:</p>
				<ul>
					<li>Microsoft, Martin Beeby (<a href="http://twitter.com/thebeebs">@thebeebs</a>)</li>
					<li>Opera, Chris Mills (<a href="http://twitter.com/chrisdavidmills">@chrisdavidmills</a>)</li>
					<li>Google, Michael Mahemoff (<a href="http://twitter.com/mahemoff">@mahemoff</a>)</li>
					<li>Mozilla, Paul Rouget (<a href="http://twitter.com/paulrouget">@paulrouget</a>)</li>
				</ul>
			</section>
			<section id="sponsors">
				<h1>Sponsors</h1>
				<ul class="sponsors">
					<li>
						<a href="./sponsors.php#squiz"><img src="./images/squiz.png" height="50" width="181" alt="Squiz UK."/></a>
					<li>
						<a href="./sponsors.php#webdirections"><img src="./images/webdirections.png" height="67" width="250" alt="At media web directions."/></a>
					<li>
						<a href="./sponsors.php#campaignmonitor"><img src="./images/campaignmonitor.png" height="39" width="250" alt="Campaign Monitor."/></a>
					<li>
						<a href="./sponsors.php#pluslion"><img src="./images/plus_Lion.png" height="60" width="64" alt="Plus Lion."/></a>
					</li>
					<li>
						<a href="./sponsors.php#top10"><img src="./images/top_10.png" height="60" width="149" alt="Top 10 dot co."/></a>
					</li>
					<li>
						<a href="./sponsors.php#nineweb"><img src="./images/9web.png" height="70" width="136" alt="9 web"/></a>
					</li>
					<li>
						<a href="./sponsors.php#ubelly"><img src="./images/ubelly.png" height="70" width="163" alt="ubelly"/></a>
					</li>
				</ul>
				<? /* p>You're welcome to <a href="#supporters">talk to us about sponsorship</a>.</p */ ?>
			</section>
			<section id="who">
				<h1>Who Is This Event For?</h1>
				<div class="description">
					<p>State Of The Browser (#lwsbrowser) is being organised by <a href="http://www.londonwebstandards.org">London Web Standards</a>, 
						the group for London's creative web industry. We welcome anyone with an interest in creating content for the next generation of 
						browsers. Whether you're an HTML5 guru, an <abbr title="Information Architect">IA</abbr> looking to understand what new experiences 
						are being opened up, or you dream in HTTP<!-- Hi Kornel -->, this event will give you the inside track on what promises to be one 
						of the most exciting years yet for the browser.
					</p>
				</div>
				<div id="lws-logo">
					<a href="http://www.londonwebstandards.org" class="lws"><img src="./images/lws_grey_bg.png" alt="London Web Standards" width="250" height="82" /></a>
				</div>
			</section>
			<section id="location">
				<h1>Venue - Ravensbourne</h1>
				<p id="address">6 Penrose Way, London, SE10 0EW</p>
				<div id="transport">
					<h2>Getting There</h2>
					<dl class="public-transport">
						<dt>
							Tube
						</dt>
						<dd>
							<strong>North Greenwich station on the Jubilee Line</strong>.
							Please note that although there are no scheduled closures on the Jubilee Line on March 19th, this could change at short notice. Please check
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
							title="Map of bus stop in north geenwich">stop at North Greenwich</a>:
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
				<div class="about-the-venue">
					<p>Ravensbourne is a stunning new building on the Greenwich Peninsula, located next door to The O2. 
						For more details on the space and how to get there, go to <a href="http://www.rave.ac.uk/">www.rave.ac.uk</a>.</p>
					<div id="map">
						<img src="./images/map-default.png" alt="Map of venue">
					</div>
				</div>
			</section>
		</div>
		<section id="supporters">
			<div class="content">
				<p>State of the Browser is a unique opportunity to get your message to the web standards community, opinion formers 
				and geeks who live and breathe the web. We have a variety of options available for sponsors to engage with our audience.
				If you'd like to know more or join the list of sponsors already helping to make this event happen, 
				<a href="mailto:organisers@londonwebstandards.org" title="Drop the organisers an email">contact us</a> today.</p>
			</div>
		</section>
		<section id="community">
			<div class="content">
				<h2>Thanks to our Community Partners</h2>
				<ul>
					<li><a href="http://www.londonwebmeetup.org"><img src="./images/londonweb.png" height="85" width="99" alt="London Web."/></a></li>
					<li><a href="http://www.phplondon.org"><img src="./images/phplondon.png" height="85" width="142" alt="PHP London."/></a></li>
				</ul>
			</div>
		</section>
		<footer>
			<div class="content">
				<p>© Copyright <a href="http://www.londonwebstandards.org">London Web Standards</a> <?php if (date('Y') == "2011"){echo date('Y');} else{echo '2011 - '.date('Y');}?>, all rights reserved.</p>
			</div>
		</footer>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
		<script src="./js/jquery.scrollTo-1.4.2-min.js"></script>
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
		<script src="./js/jquery.shuffle.js"></script>
		<script>
			$(document).ready(function(){
				$('ul.logos').shuffle();
				$('ul.logos').css('visibility','visible');
				$('#nav a.scroll').click(function(e){
					e.preventDefault();
					var id = "#" + this.href.split("#")[1];
					$.scrollTo($(id),500);
				});
				$('#register').colorbox().trigger('click');
			});
		</script>
		<script  type="text/javascript">
			try {
				var jqueryLoaded=jQuery;
				jqueryLoaded=true;
			} catch(err) {
				var jqueryLoaded=false;
			}
			if (!jqueryLoaded) {
				var head= document.getElementsByTagName('head')[0];
				var script= document.createElement('script');
				script.type= 'text/javascript';
				script.src= 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js';
				head.appendChild(script);
			}
		</script>
		<script src="http://downloads.mailchimp.com/js/jquery.form-n-validate.js"></script>
		<script src="/js/jquery.colorbox-min.js"></script>
		<script>
			var fnames = new Array();var ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';var err_style = '';
			try{
				err_style = mc_custom_error_style;
			} catch(e){
				err_style = 'margin: 1em 0 0 0; padding: 1em 0.5em 0.5em 0.5em; background: ERROR_BGCOLOR none repeat scroll 0% 0%; font-weight: bold; float: left; z-index: 1; width: 80%; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial; color: ERROR_COLOR;';
			}
			var head= document.getElementsByTagName('head')[0];
			var style= document.createElement('style');
			style.type= 'text/css';
			if (style.styleSheet) {
				style.styleSheet.cssText = '.mce_inline_error {' + err_style + '}';
			} else {
				style.appendChild(document.createTextNode('.mce_inline_error {' + err_style + '}'));
			}
			head.appendChild(style);
			$(document).ready( function($) {
				var options = { errorClass: 'mce_inline_error', errorElement: 'div', onkeyup: function(){}, onfocusout:function(){}, onblur:function(){}  };
				var mce_validator = $("#mc-embedded-subscribe-form").validate(options);
				options = { url: 'http://londonwebstandards.us2.list-manage1.com/subscribe/post-json?u=c450c49f183aa866b73ae2844&id=acfefc4dd6&c=?', type: 'GET', dataType: 'json', contentType: "application/json; charset=utf-8",
				beforeSubmit: function(){
					$('#mce_tmp_error_msg').remove();
					$('.datefield','#mc_embed_signup').each( function(){
						var txt = 'filled';
						var fields = new Array();
						var i = 0;
						$(':text', this).each(function(){
							fields[i] = this;
							i++;
						});
						$(':hidden', this).each(
							function(){
								if ( fields[0].value=='MM' && fields[1].value=='DD' && fields[2].value=='YYYY' ){
									this.value = '';
								} else if ( fields[0].value=='' && fields[1].value=='' && fields[2].value=='' ){
									this.value = '';
								} else {
									this.value = fields[0].value+'/'+fields[1].value+'/'+fields[2].value;
								}
							});
						});
						return mce_validator.form();
					}, 
					success: mce_success_cb
					};
				$('#mc-embedded-subscribe-form').ajaxForm(options);
			});
			function goToByScroll(id){
				$('html,body').animate({scrollTop: $("#"+id).offset().top},'slow');
			}
			function mce_success_cb(resp){
				$('#mce-success-response').hide();
				$('#mce-error-response').hide();
				if (resp.result=="success"){
					$('#mce-'+resp.result+'-response').show();
					$('#mce-'+resp.result+'-response').html(resp.msg);
					$('#mc-embedded-subscribe-form').each(function(){
						this.reset();
					});
				} else {
					var index = -1;
					var msg;
					try {
						var parts = resp.msg.split(' - ',2);
							if (parts[1]==undefined){
							msg = resp.msg;
						} else {
							i = parseInt(parts[0]);
							if (i.toString() == parts[0]){
								index = parts[0];
								msg = parts[1];
							} else {
								index = -1;
								msg = resp.msg;
							}
						}
					} catch(e){
						index = -1;
						msg = resp.msg;
					}
					try {
						if (index== -1){
							$('#mce-'+resp.result+'-response').show();
							$('#mce-'+resp.result+'-response').html(msg);            
						} else {
							err_id = 'mce_tmp_error_msg';
							html = '<div id="'+err_id+'" style="'+err_style+'"> '+msg+'</div>';
							var input_id = '#mc_embed_signup';
							var f = $(input_id);
							if (ftypes[index]=='address'){
								input_id = '#mce-'+fnames[index]+'-addr1';
								f = $(input_id).parent().parent().get(0);
							} else if (ftypes[index]=='date'){
								input_id = '#mce-'+fnames[index]+'-month';
								f = $(input_id).parent().parent().get(0);
							} else {
								input_id = '#mce-'+fnames[index];
								f = $().parent(input_id).get(0);
							}
							if (f){
								$(f).append(html);
								$(input_id).focus();
							} else {
								$('#mce-'+resp.result+'-response').show();
								$('#mce-'+resp.result+'-response').html(msg);
							}
						}
					} catch(e){
						$('#mce-'+resp.result+'-response').show();
						$('#mce-'+resp.result+'-response').html(msg);
					}
				}
			}
		</script>
		<script>
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-15690034-10']);
			_gaq.push(['_trackPageview']);
			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
	</body>
</html>
