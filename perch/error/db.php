<?php
    include('../config/config.php');

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
	<title>Log in - Perch</title>
	<link rel="stylesheet" href="<?php echo PERCH_LOGINPATH; ?>/assets/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo PERCH_LOGINPATH; ?>/assets/css/login.css" type="text/css" />   
	<style type="text/css" media="screen">
	    /* Custom settings */
	   #hd { 
	       background-color: #FFF;
	   }
	   #hd ul#nav li a:link, #hd ul#nav li a:visited,
	   #hd ul#metanav li a:link, #hd ul#metanav li a:visited  {
	       color: #333;
	   }
	</style>

</head>


<body class="login">
    <div id="content">    
    <div id="login">
        <div id="hd">
            <img src="<?php echo PERCH_LOGINPATH; ?>/assets/img/logo.png" alt="Perch" />
        </div>
        <div class="bd">
            <form class="error">
                <p class="alert-failure">Perch could not connect to the database</p>
            
                <p>Please check that the access details specified in <code>config.php</code> are correct.</p>
                
                <p><a href="<?php echo PERCH_LOGINPATH; ?>">Try again</a></p>
            </form>
        </div>
        
    </div>


    <div id="footer">
		<div class="credit">
			<p><a href="http://grabaperch.com"><img src="<?php echo PERCH_LOGINPATH; ?>/assets/img/perch.gif" width="35" height="12" alt="Perch" /></a>
			by <a href="http://edgeofmyseat.com">edgeofmyseat.com</a></p>
		</div>

    	</div>
</div>	
    </body>
</html>