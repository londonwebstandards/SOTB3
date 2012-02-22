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
    <style>
    #browser-live{
      width:848px;
      margin: 0 auto;
      padding-bottom:20px;
    }
    section#community {
      padding-top:10px;
      background:url("../images/backgrounds/content-bottom.png") repeat-x scroll 0 0 #313131;
    }
    </style>
    <!--[if lt IE 9]>
      <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <meta name="viewport" content="width=1024" />
    <script src="https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject_src.js"></script>
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
        <div id="browser-live">
          <object id="flash-player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="848" height="480">
            <param name="movie" value="http://inqb8r.tv/LWS/player-simpleLiveLWS.swf" />
            <!--[if !IE]>-->
            <object type="application/x-shockwave-flash" data="http://inqb8r.tv/LWS/player-simpleLiveLWS.swf" width="848" height="480">
            <!--<![endif]-->
              <p>Alternative content</p>
            <!--[if !IE]>-->
            </object>
            <!--<![endif]-->
          </object>
        </div>
    </div>
    <nav class="main">
      <ul id="nav">
        <li>
          <a href="./live.php">Live</a>
        </li>
        <li>
          <a href="/#who" class="scroll">Who</a>
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
  		</div>
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
    <script>
      swfobject.registerObject("flash-player", "10.2", "/flash/expressInstall.swf");
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
