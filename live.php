<?php include("inc/pageopen.php"); ?>
<?php include("inc/navigation.php"); ?>
<div id="brand" class="sixteen columns">
    <h1>Live Coverage</h1>
</div>
<div id="live">
    <div class="ten columns">
		<script type="text/javascript"> 
			swfobject.registerObject("player", "10.2.0", "http://susu.tv/flash/expressInstall.swf");
		</script>
		<div id="videoContainer" class="container_12" style="z-index:10;">
			<div style="text-align: center; margin-bottom: 10px;">
				<object type="application/x-shockwave-flash" data="http://inqb8r.tv/player/lws/player-lws2012.swf" width="852" height="480" id="player" name="player" class="player">
				<param name="movie" value="/flash/susutv-player.swf?PL=1200">
				<param name="wmode" value="direct">
				<param name="allowfullscreen" value="true">
				<param name="allowscriptaccess" value="always">

				<!--[if !IE]>-->
					<object type="application/x-shockwave-flash" data="http://inqb8r.tv/player/lws/player-lws2012.swf" width="852" height="480" class="player"> 
						<param name="wmode" value="direct"> 
						<param name="allowscriptaccess" value="always"> 
						<param name="allowfullscreen" value="true"> 
				<!--<![endif]-->

				<video width="852" height="480" poster="http://77.244.130.44/nfts/images/nfts_now.jpg" controls onplay="statIpad(1200)">
					<!--IPhone 3-->
					<source src="http://77.244.130.41/liveChat/livestream2/playlist.m3u8" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"' />
				</video>

				<!--[if !IE]>--> 
					</object> 
				<!--<![endif]-->
				</object>
			</div>
	 </div>
</div>
<?php include("inc/communityPartners.php"); ?>
<?php include("inc/footer.php"); ?>