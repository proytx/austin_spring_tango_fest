<?php
        error_reporting(E_ALL & ~E_NOTICE);
        ini_set('log_errors','1');
        ini_set('display_errors','0');
  include(dirname(dirname(dirname(__FILE__)))."/php/ASTFDatesOptions.php");
  include(dirname(dirname(dirname(__FILE__)))."/php/ASTFRegPreloader.php");
// All redirects from this file moved to the above include file
//  if ($dtmNow > $dtmFestEndDate) {
//	header("Location: http://austinspringtango.com/thankyou.shtml");
//	exit;

  // Check if this page is being visited from checkin page, if not let in only if testsite or check onsite flag not set 
  // If it is, then test sites are send straight in, otherwise check Onsite flag
  if (stristr( $_SERVER["HTTP_REFERER"], "checkin.php" )) {
	if (preg_match("/^test.austinspringtango/i", $_SERVER["HTTP_HOST"]) || $blnOnsiteOperation) {
		$strTitle2 = "Onsite Welcome Page";
		$strTitle3 = "";
		$strAutofillchecked = "";
		if (isset($_GET["type"])) {
			$strTitle3 .= " For ".$_GET["type"];
			if ($_GET["type"] != "New") $strAutofillchecked = "checked";
		}
		$strFormAction = "register2.php?yr=".strftime("%Y", $dtmFestStartDate);
		$strJScheckin = '<script type="text/javascript" src="ASTFtools.mini.js"></script>';
		$strJScookies = '<script type="text/javascript" src="ASTFcookies.js"></script>';
		$strJScheckin = '<script type="text/javascript" src="ASTFcheckinhelp.js"></script>';
		$strJSdbhelp = '<script type="text/javascript" src="ASTFdbhelp.js"></script>';
		$strAutofill = '<label for="autofillonname">Auto fill on typing name</label>';
		$strAutofill .= '<input type="checkbox" name="autofillonname" value="autofill" id="autofillonname" '.$strAutofillchecked;
		$strAutofill .= ' onchange="blnAutoSuggestNames=this.checked; return true;"><br />';
		$strTableIframe = '<iframe id="regdata" height="0" width="0"></iframe>';
	} else {
		$strClosedRegistration = "Onsite not yet open";
		$strSubmitDisabled = "disabled";	
		$strFormAction = "";
	}
  } else {
	if (preg_match("/^test.austinspringtango/i", $_SERVER["HTTP_HOST"]) || !($blnOnsiteOperation)) {
  		$strTitle2 = "Schedule, Rates &amp; Registration";
    		$strFormAction = "register2.php?yr=".strftime("%Y", $dtmFestStartDate);
	} else {
		$strClosedRegistration = "Closed - reopens onsite";
		$strSubmitDisabled = "disabled";	
		$strFormAction = "";
	}
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[if IE 7]>
<html class="ie ie7" lang="en-US">
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" lang="en-US">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html lang="en-US">
<!--<![endif]-->
<head>
  <meta charset="UTF-8" />
  <title> Austin Spring Tango Festival :: <?php echo $strTitle2.$strTitle3; ?></title>

  <!--[if lt IE 9]>
  <script src="http://austinspringtango.com/wp-content/themes/twentytwelve/js/html5.js" type="text/javascript"></script>
  <![endif]-->
  <link rel='stylesheet' id='googleFontsASTF-css'  href='http://fonts.googleapis.com/css?family=Oswald%3A300%2C400&#038;ver=3.7.1' type='text/css' media='all' />
 
<?php
  $strUserAgent = strtolower($_SERVER["HTTP_USER_AGENT"]);
  //$blnDesktopBrowser = strpos($strUserAgent, 'Linux') || strpos($strUserAgent, 'Windows') || strpos($strUserAgent, 'Macintosh');
  // cannot omit != false part because if the string is found in position 0, that could erroneously be reported as false too 
  // || should short circuit, as soon as it encounters the first match, hence the string matches are arranged in order of max probability
  // of finding these phones. This is a different approach from trying to capture all possible mobile browsers on planet earth -
  // market survey shows android, then iphone, then blackberry windows etc. - but first look for mobi and opera mini, e.g, iPhone will be detected by 'mobi' before it gets to iPhone
  $blnMobileBrowser = (strpos($strUserAgent, 'mobi') != false ) || (strpos($strUserAgent, 'opera mini') != false ) || (strpos($strUserAgent, 'android') != false ) 
            || (strpos($strUserAgent, 'iphone') != false ) || (strpos($strUserAgent, 'ipod') != false ) || (strpos($strUserAgent, 'blackberry') != false )
            || (strpos($strUserAgent, 'windows phone') != false ) || (strpos($strUserAgent, 'windows ce') != false );
  if (!$blnMobileBrowser) {
    $intTextareaWidth ="40";
    echo '<link href="css/register.css" rel="stylesheet" type="text/css" />';
    echo '<link href="css/forms.css" rel="stylesheet" type="text/css" />';
    echo '<link href="css/reg_print.css" rel="stylesheet" type="text/css" media="print" />';
  } else {  // do not allow non-desktop mobile phone riff-raff register, without javascript input sanity checks, database fills up with garbage - Partha
    // later it will be reactivated via javascript, i.e., no javascript, no worky
    $intTextareaWidth ="32";
    //$strFormAction = "register.php";
//header("Location: http://www.tangoteacherexchange.com/"); /* Redirect browser (TEMPORARY, FIXING STUFF) */
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes"/>'; // tips from http://www.seabreezecomputers.com/tips/mobile-css.htm
    echo '<meta http-equiv="X-UA-Compatible" content="IE=edge" />';  // needed for IE10
    echo '<link media="handheld, only screen and (max-width: 1024px), only screen and (max-device-width: 1024px)" href="css/forms_mob.css" type="text/css" rel="stylesheet" /> ';
  }
  if ( isset($_GET['dbg']) ) { $strFormAction = $strFormAction.'&dbg='.$_GET['dbg']; }
  session_start();
  //session_unset();
  if (isset($_SESSION["lastPost"])) {
    $lastPost = $_SESSION["lastPost"];
    foreach($lastPost as $k=>$v) {
      if ($k == "lefol") {
        if ($v == "lead") $arrAssoc[$k]='1'; elseif ($v == "follow") $arrAssoc[$k]='2';
      } elseif ($k == "danceLvl") {
        $arrAssoc[$k]=substr(ucfirst($v),0,1);
      } else {
        if ($k != "firstSubmitToken") $arrAssoc[$k]=$v;
      }
    }
  }

?>

  <script type="text/javascript" src="ASTFregisterhelp.js?<?php echo date('W'); ?>"></script>
  <script type="text/javascript" src="DOMhelp.js"></script>
  <script type="text/javascript" src="formHelper2.js"></script>
  <?php echo $strJScheckin; ?>
  <?php echo $strJScookies; ?>
  <?php echo $strJSdbhelp; ?>
</head>

<body class="page custom-background full-width" <?php echo $strBodyLoad; ?> > 

 <div id="main" class="hfeed site">
	<header id="masthead" class="site-header" role="banner">
		<hgroup>
			<h1 class="site-title"><a href="http://austinspringtango.com/" title="Austin Spring Tango Festival" rel="home">AUSTIN SPRING <span class="red">TANGO</span> FESTIVAL</a></h1>
			<h2 class="site-description"><?php echo $strFestYear?> • <?php echo strtoupper($strFestStartDate)?> • <?php echo $strFestEndDate?> • AUSTIN, TEXAS</h2>
		</hgroup>

</header><!-- #masthead -->
	 <div id="primary" class="site-content">
   <article class="page hentry">
   <header class="entry-header">
     <h1 class="entry-title">Registration <?php echo $strClosedRegistration; ?> </h1>
   </header> 
   <div id="secondary"  class="entry-content">    
   <p id="topnote" style="margin-top:1em;">Scroll down for class topics, times etc. This page is best viewed in Firefox or Chrome. <br/>
   <!-- NOTE: Smartphone no. field is new this year, and optional, and we have ideas to put it to cool uses during the festival, but only if you choose to. In other words, we won't send you anything to that number. -->
   <form action="<?php echo $strFormAction; ?>" method="post" name="tango1" id="tango1" >
   <div id="Total">
    <div id="Amt">TOTAL: $0</div>
    <p>If page is reloaded, total will update AFTER the next choice is made.</p>
  </div>
<fieldset id="aboutYou">
<legend> About You: </legend>
    <input type="hidden" name="firstSubmitToken" id="firstSubmitToken" value="<?php echo time(); ?>" />
    <input type="hidden" name="ParticipantID" id="ParticipantID" value="<?php echo $arrAssoc['ParticipantID']; ?>" />
	<?php echo $strAutofill; ?>
        <label for="firstName">First Name:</label> <input type="text" name="firstName" id="firstName" onblur="setSaneInput(this);" value="<?php echo $arrAssoc['firstName'];?>" /> <br />
        <!--Goodbye middle name confusion starting 2013 ASTF
        <label for="midName">Middle Initial:</label>
        -->
        <input name="midName" type="hidden" id="midName" value="<?php echo $arrAssoc['midName'];?>"  />
  <label for="lastName">Last Name:</label> <input type="text" name="lastName" id="lastName" onblur="setSaneInput(this);" value="<?php echo $arrAssoc['lastName'];?>"   /><br />
  <label for="phonenumber"><img src="android.png"/><img src="iphone.png"/><img src="winphone.png"/> 10-digit No.:</label> <input type="text" name="phonenumber" id="phonenumber" onblur="setSaneInput(this);" value="<?php echo $arrAssoc['phonenumber'];?>" /><br />
  <label for="email">Email:</label> <input type="text" name="email" size="30" id="email" onblur="setSaneInput(this);" value="<?php echo $arrAssoc['email'];?>" /><br />
  
  <p>Will you be dancing primarily as a Lead or as a Follow?
  </p>
    <label for="lefol1">Lead:</label>
    <input name="lefol" type="radio" value="lead" id="lefol1" <?php if ( $arrAssoc['lefol']=='1') echo "checked" ;?> /><br />
    <label for="lefol2">Follow:</label>
    <input name="lefol" type="radio" value="follow" id="lefol2" <?php if ( $arrAssoc['lefol']=='2') echo "checked" ;?> /><br />
  <p>What is your approximate dance level?</p>
    <label for="danceLvl1">Beginner:</label>
    <input name="danceLvl" type="radio" value="beginner" id="danceLvl1" <?php if ( $arrAssoc['danceLvl']=='B') echo "checked" ;?> /><br />
    <label for="danceLvl2">Intermediate:</label>
    <input name="danceLvl" type="radio" value="intermediate" id="danceLvl2" <?php if ( $arrAssoc['danceLvl']=='I') echo "checked" ;?>/><br />
    <label for="danceLvl3">Advanced:</label>
    <input name="danceLvl" type="radio" value="advanced" id="danceLvl3" <?php if ( $arrAssoc['danceLvl']=='A') echo "checked" ;?>/>

</fieldset>
<fieldset id="address">
  <legend> Your Address: </legend>
        <p>ZIP, please...we'll try to guess the rest. Do correct us if we guess wrong! For State: use TX for Texas, OK for Oklahoma, etc.</p>
        <!--Address fields not used anyway, except for cheque writers, maybe, so drop it starting 2013
        <label for="address">Address:</label> 
        -->
        <input type="hidden" name="address"  id="address" value="<?php echo $arrAssoc['address'];?>" />
  <label for="city">City:</label> <input type="text" name="city" id="city" onblur="setSaneInput(this);" value="<?php echo $arrAssoc['city'];?>" /><br />
  <label for="state">State:</label> <input type="text" name="state" id="state" onblur="setSaneInput(this);" value="<?php echo $arrAssoc['state'];?>" /><br />
  <label for="zip">Zip Code:</label> <input type="text" name="zip" id="zip" onblur="setSaneInput(this);" value="<?php echo $arrAssoc['zip'];?>" />
</fieldset>

<fieldset id="selClasses">
  <legend> Prices (18 classes, 4 milongas): </legend>
  
<!------ BRG
    <p class="firstp"><strong>Package (select one):</strong></p>  
------>
    <label for="package1"><strong><a href="#selClasses1" onclick="fh.p1on(); return setTotal();">Full Pass</a></strong><br /> 
    <em>(all 18 classes, 4 milongas)</em><br />
    By <?php echo $strEarlyBirdDate; ?>: $235<br />
    After that: $275
    </label>
    <!--Goodbye package radio buttons, 2013
    <input name="selClasses" type="radio" value="fullPass" id="package1" <?php if ( $arrAssoc['selClasses']=='fullPass') echo "checked" ;?> onchange="return setTotal();"/><br />
    -->

<!------ BRG
CHANGE TO formHelper.js IF ENABLING A 2ND PACKAGE.
HACK formHelper.js IF ADDING MORE THAN 2 PACKAGES.
------>
    <label for="package2"><strong><a href="#selMilongas" onclick="fh.p2on(); return setTotal();">Milonga Pass </a></strong><br />
    <em>(all 4 milongas)</em><br />
    $105<br /> 
    $125
    </label>
    <!--Goodbye package radio buttons, 2013
    <input name="selClasses" type="radio" value="milongaPass" id="package2" <?php if ( $arrAssoc['selClasses']=='milongaPass') echo "checked" ;?> onchange="return setTotal();"/><br />
    -->
   <label for="package3"><strong>Classes a la Carte</strong><br />
    <em>(pick any number)</em><br />
    $30 ea.<br />
    $35 ea.</label>
    <!--Goodbye package radio buttons, 2013
    <input name="selClasses" type="radio" value="alaCarteClasses" id="package3" onchange="return setTotal();"/><br />
    -->
   <label for="package4"><strong>Milongas a la Carte</strong><br />
    <em>(pick any number)</em><br />
    $25/$25/$35/$40<br />
    $25/$25/$35/$40</label>

</fieldset>

<fieldset id="selClasses1">

    <legend> Classes: </legend>
   <p>Full pass and Milonga pass automatically selected based on which checkboxes are selected. Tentative room numbers shown within (), followed by class levels, A=Advanced, I=Intermediate, U=All-level.</p>
  
  <div <?php if ( isset( $arrAssoc['classes'])) echo 'style="display: visible"' ;?> id="indivClasses" >
  <label >&nbsp;</label><div class="dummyspace" >&nbsp;&nbsp;</div><?php foreach ($arrTeachers as $strTeachers) echo '<div class="classtopics">'.$strTeachers.'</div>';?>
  
  <p><em>Friday Class: </em></p>
  <label for="class1">8:00-9:30 PM:</label> <input type="checkbox" name="classes[]" value="Fri 8:00-9:30 PM" id="class1" <?php if (isset($arrAssoc['classes']) && in_array("Fri 8:00-9:30 PM",$arrAssoc['classes'])) echo "checked" ;?> onchange="return setTotal();"/>
  <?php foreach ($arrClasses['Fri 08:00 PM'] as $strClassInfo) echo '<div class="classtopics">'.$strClassInfo.'</div>';?><br />

  <p><em>Saturday Classes: </em></p>
  <label for="class2">11:00-12:30 PM:</label> <input type="checkbox" name="classes[]" value="Sat 11:00-12:30 PM" id="class2" <?php if ( isset($arrAssoc['classes']) && in_array("Sat 11:00-12:30 PM",$arrAssoc['classes']) ) echo "checked" ;?> onchange="return setTotal();"/>
  <?php foreach ($arrClasses['Sat 11:00 AM'] as $strClassInfo) echo '<div class="classtopics">'.$strClassInfo.'</div>';?><br />
  <label for="class3">1:00-2:30 PM:</label> <input type="checkbox" name="classes[]" value="Sat 1:00-2:30 PM" id="class3" <?php if (isset($arrAssoc['classes']) &&  in_array("Sat 1:00-2:30 PM",$arrAssoc['classes'])  ) echo "checked" ;?> onchange="return setTotal();"/>
  <?php foreach ($arrClasses['Sat 01:00 PM'] as $strClassInfo) echo '<div class="classtopics">'.$strClassInfo.'</div>';?><br />

  <p><em>Sunday Classes:</em></p>
  <label for="class4">1:00-2:30 PM:</label> <input type="checkbox" name="classes[]" value="Sun 1:00-2:30 PM" id="class4" <?php if (isset($arrAssoc['classes']) && in_array("Sun 1:00-2:30 PM",$arrAssoc['classes']) ) echo "checked" ;?> onchange="return setTotal();"/>
  <?php foreach ($arrClasses['Sun 01:00 PM'] as $strClassInfo) echo '<div class="classtopics">'.$strClassInfo.'</div>';?><br />
  <label for="class5">3:00-4:30 PM:</label> <input type="checkbox" name="classes[]" value="Sun 3:00-4:30 PM" id="class5" <?php if (isset($arrAssoc['classes']) &&  in_array("Sun 3:00-4:30 PM",$arrAssoc['classes']) ) echo "checked" ;?> onchange="return setTotal();"/>
  <?php foreach ($arrClasses['Sun 03:00 PM'] as $strClassInfo) echo '<div class="classtopics">'.$strClassInfo.'</div>';?><br />
  <label for="class6">5:00-6:30 PM:</label> <input type="checkbox" name="classes[]" value="Sun 5:00-6:30 PM" id="class6" <?php if (isset($arrAssoc['classes']) &&  in_array("Sun 5:00-6:30 PM",$arrAssoc['classes']) ) echo "checked" ;?> onchange="return setTotal();"/>
  <?php foreach ($arrClasses['Sun 05:00 PM'] as $strClassInfo) echo '<div class="classtopics">'.$strClassInfo.'</div>';?><br />
  </div>
</fieldset>
<fieldset id="selMilongas">

    <legend> Milongas: </legend>
  <!--
    <label for="package4"><strong>Milongas a la Carte</strong>:<br /></label>
    <input name="selMilongas" type="radio" value="alaCarteMilongas" id="package4" /><br />
  <div id="indivMilongas"><br />
  -->

  <label for="milonga1"><strong>Friday Milonga <br />
  10:00PM-2:00AM</strong><br />
    By <?php echo $strEarlyBirdDate; ?>: $25<br />  
  After that: $25</label>
   <input type="checkbox" name="milongas[]" value="Friday" id="milonga1"  <?php if (isset($arrAssoc['milongas']) &&  in_array("Friday",$arrAssoc['milongas']) ) echo "checked" ;?> onchange="return setTotal();"/><br />

  <label><strong>Saturday Afternoon Milonga</strong><br />
  <strong> 3:00PM-6:00PM</strong><br />
  By <?php echo $strEarlyBirdDate; ?>: $25<br />  
  After that: $25</label>
  <input type="checkbox" name="milongas[]" value="Saturday5050" id="milonga4" <?php if (isset($arrAssoc['milongas']) &&  in_array("Saturday5050",$arrAssoc['milongas']) ) echo "checked" ;?> onchange="return setTotal();"/><br />
  </label> <br />
   
  <label for="milonga2"><strong>Saturday  Milonga</strong><br />
  <strong> 9:00PM-6:00AM</strong><br />
  <em>(includes performance)</em><br />
    By <?php echo $strEarlyBirdDate; ?>: $35<br />  
  After that: $35</label> 
  <input type="checkbox" name="milongas[]" value="Saturday" id="milonga2" <?php if (isset($arrAssoc['milongas']) &&  in_array("Saturday",$arrAssoc['milongas']) ) echo "checked" ;?> onchange="return setTotal();"/><br />

  <label for="milonga3"> <strong>Sunday Asado Milonga</strong><br />
  <strong> 7:00PM-1:00AM</strong><br />
  <em>(includes food)</em><br />
    By <?php echo $strEarlyBirdDate; ?>: $40<br />  
  After that: $40</label>   
  <input type="checkbox" name="milongas[]" value="Sunday" id="milonga3" <?php if (isset($arrAssoc['milongas']) &&  in_array("Sunday",$arrAssoc['milongas']) ) echo "checked" ;?> onchange="return setTotal();"/><br /><br/>
</fieldset>

<fieldset id="discount">
     <legend> Discount: </legend>
     
        <p class="firstp"><strong>Student discount 30% off total </strong>with current ID.</p>
  <label for="isStudent">Full-time student</label> 
  <input type="checkbox" name="isStudent" value="true" id="isStudent" <?php if ( $arrAssoc['isStudent'] == 'true') echo "checked" ;?> onchange="return setTotal();"/><br /><br />
    
  <p >
  <p><strong>10% off total</strong> for groups of 5 or more for out-of-towners</p>
  <label for="groupDiscount">Group discount code:</label> <input type="text" name="groupDiscount" id="groupDiscount" /><br /><br />
</fieldset>
                  

<fieldset id="payMethod">
  <legend> Payment Method: </legend>
  <p>Credit-Card payment is handled through Paypal. Checks must be mailed (full info after clicking register).</p>
    <label for="payment1">Check:</label>
    <input name="payment" type="radio" value="check" id="payment1" <?php if ( $arrAssoc['payment'] == 'check') echo "checked" ;?> onclick="return setTotal();"/><br />
    <label for="payment2">Credit-Card/Paypal:</label>
    <input name="payment" type="radio" value="card" id="payment2" <?php if ( $arrAssoc['payment'] == 'card') echo "checked" ;?> onclick="return setTotal();"/><br />
  <label for="submit">CLICK TO REGISTER:&nbsp;</label>
  <input name="register" type="submit" value="Register" id="register" <?php echo $strSubmitDisabled; ?> onClick="return (isEmptyCheck('firstName,lastName,email') && isValidEmail() && isWebURL('anytext') && isSelectedMinOne() && isPaymentSelected() );" /><br />
  <p>After pressing the Register button, if you use the browser's Back button to return to this page and change some data, the changes might not be recorded properly, so please check the data before you click Register. For your convenience, the running total is displayed in a gray box.</p>
</fieldset>
</form>
<div id="footnote" >
  <h2>Private Lessons</h2>
  <p>Once you have registered, and if you want to set up a private with our teachers, contact Vance Rightmire at <a href="mailto:margvance@aol.com">margvance@aol.com</a>.</p>
</div>
</div>
   </article>
   </div>
</div>
<?php echo $strTableIframe; ?>

<!-- JQuery Script Includes -->
<script type='text/javascript' src='http://austinspringtango.com/wp-includes/js/jquery/jquery.js?ver=1.10.2'></script>
<script type='text/javascript' src='http://austinspringtango.com/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.2.1'></script>
<!-- Navigation Script from WP TwentyTwelve Theme -->
<script type='text/javascript' src='http://austinspringtango.com/wp-content/themes/twentytwelve/js/navigation.js?ver=1.0'></script>
<!-- End Script -->
<?php
      	if (preg_match("/^test/i", $_SERVER["HTTP_HOST"])) {
		echo "<script type='text/javascript' src='http://test.austinspringtango.com/html2canvas.js'></script>";
		echo "<div id=\"clickImage\" onclick=\"return showImage();\">Click me</div>";
		echo "<script type='text/javascript'> 
			function showImage() {
				html2canvas([document.body], {
					onrendered: function(canvas) {
						document.body.appendChild(canvas);
					}
				});
			}
		</script>";
	}
?>
</body>
</html>
