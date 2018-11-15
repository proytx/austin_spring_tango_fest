<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title> Austin Spring Tango Festival :: Checkin</title>
	<?php
	date_default_timezone_set('America/Chicago');
	$strUserAgent = strtolower($_SERVER["HTTP_USER_AGENT"]);
	//$blnDesktopBrowser = strpos($strUserAgent, 'Linux') || strpos($strUserAgent, 'Windows') || strpos($strUserAgent, 'Macintosh');
	// cannot omit != false part because if the string is found in position 0, that could erroneously be reported as false too 
	// || should short circuit, as soon as it encounters the first match, hence the string matches are arranged in order of max probability
	// of finding these phones. This is a different approach from trying to capture all possible mobile browsers on planet earth -
	// market survey shows android, then iphone, then blackberry windows etc. - but first look for mobi and opera mini, e.g, iPhone will be detected by 'mobi' before it gets to iPhone
	$blnMobileBrowser = (strpos($strUserAgent, 'mobi') != false ) || (strpos($strUserAgent, 'opera mini') != false ) || (strpos($strUserAgent, 'android') != false ) 
						|| (strpos($strUserAgent, 'iphone') != false ) || (strpos($strUserAgent, 'ipod') != false ) || (strpos($strUserAgent, 'blackberry') != false )
						|| (strpos($strUserAgent, 'windows phone') != false ) || (strpos($strUserAgent, 'windows ce') != false );
	$strFormAction = __FILE__;
	$strRemoteFormAction = "http://testdb.austinspringtango.com/checkin.php?yr=2013";
	if ( isset($_GET['dbg']) ) { 
		$strFormAction = $strFormAction.'?dbg='.$_GET['dbg'];
		$strRemoteFormAction = $strRemoteFormAction.'&dbg='.$_GET['dbg'];
	}
	if ( isset($_POST['firstSubmitToken']) ) { 
		http_build_query_for_curl($_POST,$newpost);
		$chnCurl = curl_init();
		curl_setopt($chnCurl, CURLOPT_URL, $strRemoteFormAction);
		curl_setopt($chnCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($chnCurl, CURLOPT_POST, true);
		curl_setopt($chnCurl, CURLOPT_POSTFIELDS, $newpost);
	
		// waiting until file will be locked for writing (3000 milliseconds as timeout)
		if ($fp = fopen($_SERVER['DOCUMENT_ROOT'].'/tmp.lock', 'a')) {
		  $startTime = microtime();
		  do { 
		    $canWrite = flock($fp, LOCK_EX); 
		    // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load 
		    if(!$canWrite) usleep(round(rand(0, 100)*1000)); 
		  } while ((!$canWrite)and((microtime()-$startTime) < 3000)); 
		}	

		$strResponse = curl_exec($chnCurl);
		$strInfo = curl_getinfo($chnCurl);

		fclose($fp);
		//unlink($_SERVER['DOCUMENT_ROOT'].'/tmp.lock');

		curl_close($chnCurl);

		$strExtra = 'If you do have to go back to the previous page to change something,';
		$strExtra .= ' please do not use the browser\'s Back button, your changes might not be recorded properly,';
		$strExtra .= ' instead use the link <a href="register.php">here</a>';
		$strResponse = str_replace('css/teach_x.css','css/ASTFdb.css',$strResponse);
		//$strResponse = str_replace($strExtra, '', $strResponse);
		$strResponse = preg_replace('/If you do have *? here<\/a>/', '', $strResponse);
		echo $strResponse;
		exit;} 
	if (!$blnMobileBrowser) {
		$intTextareaWidth ="40";
	//	echo '<link href="css/teach_x.css" rel="stylesheet" type="text/css" />';
	//	echo '<link href="css/forms.css" rel="stylesheet" type="text/css" />';
	//	echo '<link href="css/reg_print.css" rel="stylesheet" type="text/css" media="print" />';
		echo '<link href="css/ASTFdb.css" rel="stylesheet" type="text/css" />';
	} else {
		$intTextareaWidth ="32";
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes"/>'; // tips from http://www.seabreezecomputers.com/tips/mobile-css.htm
	//	echo '<link media="handheld, only screen and (max-width: 320px), only screen and (max-device-width: 320px)" href="css/teach_mob.css" type="text/css" rel="stylesheet" /> ';
	//	echo '<link media="handheld, only screen and (max-width: 320px), only screen and (max-device-width: 320px)" href="css/forms_mob.css" type="text/css" rel="stylesheet" /> ';
		echo '<link media="handheld, only screen and (max-width: 320px), only screen and (max-device-width: 320px)" href="css/ASTFdb_mob.css" type="text/css" rel="stylesheet" /> ';
		if (strpos($strUserAgent, 'android') != false) { // horrible check box and radio button on Android in dark background
			echo '<link media="handheld, only screen and (max-width: 320px), only screen and (max-device-width: 320px)" href="css/ASTFdb_and.css" type="text/css" rel="stylesheet" /> ';
		}
	}
//==============================================================================================================================
function http_build_query_for_curl( $arrays, &$new = array(), $prefix = null ) {

    if ( is_object( $arrays ) ) {
        $arrays = get_object_vars( $arrays );
    }

    foreach ( $arrays AS $key => $value ) {
        $k = isset( $prefix ) ? $prefix . '[' . $key . ']' : $key;
        if ( is_array( $value ) OR is_object( $value )  ) {
            http_build_query_for_curl( $value, $new, $k );
        } else {
            $new[$k] = $value;
        }
    }
}
//===============================================================================================================================
	?>
	<script type="text/javascript" src="ASTFregisterhelp.js"></script>
	<script type="text/javascript" src="ASTFdbhelp.js"></script>
	<script type="text/javascript" src="ASTFcookies.js"></script>
<!--
    <script type="text/javascript" src="DOMhelp.js"></script>
    <script type="text/javascript" src="formHelper2.js"></script>
    <style type="text/css">
.style1 {
	color: #FF0000;
	font-weight: bold;
}
-->
    </style>
</head>

<body onload = "initSetup()">
<?php include 'dbtable.inc.php'; ?> 
 <div id="main">
   <div id="head">
	<h3><small> Welcome to the checkin page of The Austin Spring Tango Festival</small> </h3>
   </div>
   <div id="body2" class="c4">
   <form action="<?php echo $strFormAction; ?>" method="post" name="ASTF2" id="ASTF2">
<fieldset id="aboutYou">
<legend> About You: </legend>
    	<input type="hidden" name="RegistrationID" id="RegistrationID" value="" />
	<input type="text" name="lastName" id="lastName" placeholder="Last Name" onblur="setSaneInput(this);" />
	<label for="lastName">Last Name</label><br />
	<input type="text" name="firstName" id="firstName" placeholder="First Name" onblur="setSaneInput(this);" />
	<label for="firstName">First Name</label><br />
	<br/>
</fieldset>

<fieldset id="selClasses1">
    <legend> Your Choices for the Weekend: </legend>
	<div id="indivClasses" >
	
	<span>Friday Class $30:</span><br/>
	<label for="class2">8:00-9:30 PM:</label> <input type="checkbox" name="classes[]" value="Fri 8:00-9:30 PM" id="class1" onchange="return getTotal();"/><br/><br/>
	<span>Saturday Classes $30 each:</span><br/>
	<label for="class2">12:00-1:30 PM:</label> <input type="checkbox" name="classes[]" value="Sat 12:00-1:30 PM" id="class2" onchange="return getTotal();"/>
	<label for="class3">2:00-3:30 PM:</label> <input type="checkbox" name="classes[]" value="Sat 2:00-3:30 PM" id="class3" onchange="return getTotal();"/><br/><br/>

	<span>Sunday Classes $30 each:</span><br/>
	<label for="class4">1:00-2:30 PM:</label> <input type="checkbox" name="classes[]" value="Sun 1:00-2:30 PM" id="class4" onchange="return getTotal();"/>
	<label for="class5">3:00-4:30 PM:</label> <input type="checkbox" name="classes[]" value="Sun 3:00-4:30 PM" id="class5" onchange="return getTotal();"/>
	<label for="class6">5:00-6:30 PM:</label> <input type="checkbox" name="classes[]" value="Sun 5:00-6:30 PM" id="class6" onchange="return getTotal();"/><br/><br/>
	</div>

	<span>Friday Milonga $25:</span>
	<label for="milonga1"> 10:00PM-2:00AM </label> 
	<input type="checkbox" name="milongas[]" value="Friday" id="milonga1"  onchange="return getTotal();"/><br /><br/>

	<span>Saturday Milonga $27 <em>(includes performance)</em>: </span>
	<label for="milonga2"> 9:00PM-3:00AM </label> 
	<input type="checkbox" name="milongas[]" value="Saturday" id="milonga2" onchange="return getTotal();"/><br /><br/>

	<span>Sunday Asado Milonga $25 <em>(includes food)</em>:</span>
	<label for="milonga3"> 7:00PM-1:00AM </label> 	
	<input type="checkbox" name="milongas[]" value="Sunday" id="milonga3" onchange="return getTotal();"/>
</fieldset>

<fieldset id="discount">
     <legend> Checkin: </legend>
    	<input type="hidden" name="checkintime" id="checkintime" value="" />
	<label for="submit">CLICK TO CHECKIN:&nbsp;</label>
	<input name="checkin" type="submit" value="Checkin" id="checkin" /><br />
	</fieldset>
	</form>
	</div>
	</div>
</body>
</html> 

