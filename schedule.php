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
		<!--[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<meta name="viewport" content="width=1024" />
	</head>
	<body class="vevent">
		<script type="text/javascript">document.getElementsByTagName('body')[0].className+=' jsEnabled'</script>
		<?php include("inc/header.php"); ?>
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
                    <a href="./index.php#who">Who</a>
                </li>
                <li>
                    <a href="./index.php#register">Register</a>
                </li>        
                <li>
                    <a href="./index.php#location">Where</a>
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
			<section>
				<table>
					<tbody>
						<tr>
							<th>Time</td>
							<th>Session information</th>
						</tr>
						<tr>
							<td>9:00</td>
							<td><strong>Registration</strong></td>
						</tr>
						<tr>
							<td>9:45</td>
							<td><strong>Introduction from London Web Standards</strong></td>
						</tr>
						<tr>
							<td>10:00</td>
							<td><strong>Introduction to the day</strong><br>Terence Eden (<a href="http://twitter.com/edent">@edent</a>)</td>
						</tr>
						<tr>
							<td>10:15</td>
							<td><strong>The Google Chrome Browser</strong><br>Michael Mahemoff (<a href="http://twitter.com/mahemoff">@mahemoff</a>), Google<br>
								<br>	
								This talk will outline Google Chrome and the related Chromium and ChromeOS products. Michael will explain the philosophy 
								behind Chrome, along with the project's development process, before shifting gears to cover the opportunities for 
								developers to plug into the Chrome ecosystem. This includes Chrome Frame, Chrome Extensions, and the Chrome Web 
								Store.
							</td>
						</tr>
						<tr>
							<td>10:55</td>
							<td><strong>Break</strong></td>
						</tr>
						<tr>
							<td>11:10</td>
							<td><strong>Demonstrating the Future of Firefox</strong><br>Paul Rouget (<a href="http://twitter.com/paulrouget">@paulrouget</a>), Mozilla<br>
								<br>
								Using live demonstrations, Paul Rouget will show us the best bits of Firefox. He'll give us a short summary of Firefox Mobile,
								plus he'll also look into the future with Firefox 5.
							</td>
						</tr>
						<tr>
							<td>11:50</td>
							<td><strong>Lunch</strong> (a varied complementary lunch will be provided)</td>
						</tr>
						<tr>
							<td>12:45</td>
							<td>
								<strong>HTML5: A matter of good form</strong><br>Chris Mills (<a href="http://twitter.com/chrisdavidmills">@chrisdavidmills</a>), Opera<br>
								<br>
								One major enhancement HTML5 brings to the table is form improvements, including new control archetypes and built 
								in client-side validation. In this talk Chris Mills will discuss all the new features, looking at what's 
								available, what browser support is like, and where things are going in this area. He will then open up the 
								floor for feedback, which particular emphasis on the audience's thoughts about potential shortcomings, 
								such as styling and customization of error messages.</td>
						</tr>
						<tr>
							<td>13:25</td>
							<td><strong>Break</strong></td>
						</tr>
						<tr class="breakout">
							<td colspan="2">		
								13:40 <strong>Breakout session 1</strong> <br> 14:50 <strong>Breakout session 2</strong><br>
								Anything marked with <abbr title="repeated">*</abbr> will be repeated across both breakout sessions.
							</td>
						</tr>
						<tr class="breakout">
							<td colspan="2">
								<ul>
									<li>
										Room 205, 13:40 - <strong>Mozilla Add-on SDK (née Jetpack)</strong><br>
										Laurian Gridinoc (<a href="http://twitter.com/gridinoc">gridinoc</a>), Jetpack Ambassador<br>
										A walk-through an easy way of creating Firefox Add-ons only with HTML, CSS and JavaScript.
									</li>
									<li>
										Room 205, 14:50 - <strong>BlackBerry WebWorks Bootcamp</strong><br>
										Sanyu Kiruluta (<a href="http://twitter.com/blackberrydev">@BlackBerryDev</a>), Research In Motion<br>
									</li>
									<li>
										Room 208 - <strong>The Dos and Don'ts on the Mobile Web *</strong><br>
										Mathew Staikos (<a href="http://twitter.com/blackberrydev">@BlackBerryDev</a>); Manager, Browser &amp; Web Platform - Blackberry WebWorks Platform, Research In Motion
									</li>
									<li>
										Room 209 - <strong>HTML5 Canvas: The Future of Graphics on the Web *</strong><br>
										Rob Hawkes (<a href="http://twitter.com/robhawkes">@robhawkes</a>)
									</li>
									<li>
										Room 211 - <strong>An introduction to Automated Browser Testing *</strong><br>
										Simon Stewart (<a href="http://twitter.com/shs96c">@shs96c</a>), WebDriver Team Lead (Selenium)
									</li>
									<li>
										Room 212 - <strong>Performance Optimization for HTML5 Apps *</strong><br>
										Malte Ubl (<a href="http://twitter.com/cramforce">@cramforce</a>), Google
									</li>
									<li>
										Room 213 - <strong>New CSS3 features plus Opera DragonFly *</strong><br>
										Chris Mills, Opera
									</li>
								</ul>
							</td>
						</tr>
						<tr>
							<td>15:50</td>
							<td><strong>Break</strong></td>
						</tr>
						<tr>
							<td>16:10</td>
							<td><strong>Panel Q&amp;A Discussion</strong> Chaired by Jim O'Donnell (<a href="http://twitter.com/pekingspring">@pekingspring</a>) </td>
						</tr>
							<td>17:00</td>
							<td>Drinks at the Pilot Inn</td>
						</tr>
					</tbody>
				</table>
			<section>
		</div>
		<section id="supporters">
			<div class="content">
				<p>State of the Browser is a unique opportunity to get your message to the web standards community, opinion formers 
				and geeks who live and breathe the web. We have a variety of options available for sponsors to engage with our audience.
				If you'd like to know more or join the list of sponsors already helping to make this event happen, 
				<a href="mailto:organisers@londonwebstandards.org" title="Drop the organisers an email">contact us</a> today.</p>
			</div>
		</section>
		<?php include("inc/communityPartners.php"); ?>
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
				/*$('#nav a').click(function(e){
					e.preventDefault();
					var id = "#" + this.href.split("#")[1];
					$.scrollTo($(id),500);
				})*/
			})
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
