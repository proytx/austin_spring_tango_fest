<?php
 error_reporting(E_ALL & ~E_NOTICE);
 ini_set('log_errors','1');
 ini_set('display_errors','0');
 include(dirname(dirname(dirname(__FILE__)))."/php/ASTFDatesOptions.php");
 include(dirname(dirname(dirname(__FILE__)))."/php/ASTFRegPreloader.php");

function validateDnsRecord($strEmailAddress) {
	$domain = substr($strEmailAddress, strpos($strEmailAddress, '@') + 1);
	if  (checkdnsrr($domain, "MX") === FALSE) {
    		return "(email address domain seems questionable)";
	}
	if ($domain == 'yaho.com') { // this is a valid domain with a mail server, but alert registrant
		return "(maybe you meant yahoo.com? Paypal emails receipts here)";
	}
	return "";
}

// All the dynamically generated page content must be stored separately in a string, to be output later,
// so that setcookie can work
// cookies are sent out as part of HTTP headers, before any other tags, even a whitespace is sent out
 $strPageContent = "";
 $dtmNow = strtotime("now");
 //$dtmThisYear = strftime("%Y");
 // the festival has always happened on the last weekend of March, instead of hardcoded dates 
 // that someone has to change 2x per year, let the computer figure it out, if things change later, come back to it - Partha 2011-11-12
 //$dtmFestStartDate = strtotime($dtmThisYear."-04-01 last Friday");  // time is 12:00 midnight of that last Friday in March 
 // FINALLY 2013 the festival date does not follow the rule, have to hardcode
 //if ($dtmThisYear == '2013') $dtmFestStartDate = strtotime("22 March 2013");
 //$intIpAddress = ip2long($_SERVER["REMOTE_ADDR"]);
 //$dtmEveningFestivalStart = strtotime("+ 17 hours", $dtmFestStartDate);
 if ($dtmNow > $dtmEveningFestivalStart ) {
        // Only the Dance Institute or Esquina IP's are allowed after festival starts 
        //if  (ip2long('108.82.80.44') != $intIpAddress) {
        //if  (ip2long('99.108.109.78') != $intIpAddress) { // 2014
		//header('Location: http://austinspringtango.com/');
		//exit;
	//}
 	include(dirname(dirname(dirname(__FILE__)))."/php/ASTFonsiteregister.php");
	$strRemoteFormAction = "http://test.austinspringtango.com/register2.php?yr=2015";
	if ( isset($_GET['dbg']) ) { $strRemoteFormAction = $strRemoteFormAction.'&dbg='.$_GET['dbg']; }
	$newpost = array();
	http_build_query_for_curl($_POST,$newpost);
	$arrReturnRemote = http_send_query_via_curl($newpost, $strRemoteFormAction);
 }

 /* check if users bypassed the registration form */
 /* spammers from Russia Romania were getting in without selecting classes, putting websites in address field, random firstname=lastname patterns 
 hence added extra spam checks, there are some in javascript too, but they probably turn it off - Partha */
 if ( isset($_GET['dbg']) && ($_GET['dbg']=='all') ) $strPageContent .=  strftime("%F %T",$dtmNow);
 $strLast=strtolower(trim($_POST['lastName']));
 $strFirst=strtolower(trim($_POST['firstName']));
 $strEmail=strtolower(trim($_POST['phonenumber']));
 $strEmail=strtolower(trim($_POST['email']));
 if ((count($_POST) == 0) || ( (count($_POST['classes']) + count($_POST['milongas']) )==0 ) || !isset($_POST['payment']) || 
 //( strcasecmp(substr($_POST['address'],0,7),'http://') === 0 ) || ( strcasecmp(substr($_POST['address'],0,4),'www.') === 0 ) ||
    ((strlen($strFirst) < 2 ) && (strlen($strLast) < 2 )) || ($strFirst == $strLast) ||
    (preg_match("/(asd|ads|adf|dsf|fdg|sfg)/",$strFirst)) || (preg_match("/(asd|sdf|adf|dsf|fdg|afg)/",$strLast)) ||(preg_match("/(asd|sdf|adf|dsf|fdg|afg)/",$strEmail)) ||
    (preg_match("/[lkjh]{3,4}/",$strFirst)) || (preg_match("/[lkjh]{3,4}/",$strLast)) ||
    (preg_match("/(poiu|test|nil|none)/",$strFirst)) || (preg_match("/(poiu|test|nil|none)/",$strLast)) ||
    (preg_match("/[&*#@]/",$strFirst)) || (preg_match("/[&*#@]/",$strLast)) ||
    (preg_match("/[^aeiouy]{5}/",$strFirst)) || (preg_match("/[^aeiouy]{5}/",$strLast)) ) { //relaxed restriction from 4 to 5 consecutive consonants 'Bankston' broke it
    if ( isset($_GET['dbg']) && ($_GET['dbg']=='all') ) $strPageContent .=  "<br/>Bye"; 
    header( 'Location: register.php');
    $strPageContent .=  "<html><head>Bye</head><body>".$strPageContent."</body></html>"; 
    exit;
  /* header() has to be called before any output to the browser has begun */
  }

  $strRegistrationID = '';
  /* Class fees  */
  // Deal with early- vs late-bird pricing
  //$d1 = strtotime("now");
  //$d2 = $strEarlyBirdDate;

// SOMEDAY, when we are not too busy creating extra work transferring between webhosts , the data below should come from database
  
  $earlyBird = ($dtmNow < $dtmEarlyBirdDate) ? 1 : 0;
    
  $full_pass_fee = $earlyBird ? 235 : 275;
  $milonga_pass_fee = $earlyBird ? 105 : 125;

  $class_fee = $earlyBird ? 30 : 35;
  $milonga_fees["Friday"] = $earlyBird ? 25 : 25;
  $milonga_fees["Saturday5050"] = $earlyBird ? 25 : 25;
  $milonga_fees["Saturday"] = $earlyBird ? 35 : 35;
  $milonga_fees["Sunday"] = $earlyBird ? 40 : 40;

  // File records path
  $fpath = "../../files/";


  session_start();

  /* Create a file record. If one exists, delete it. */
  $fname = $fpath . $_POST['lastName'] . '_' . $_POST['firstName'] . '_' . session_id();
  clearstatcache();  // file status is cacheable. Clear the cache.
  if (file_exists($fname)) {
    unlink ($fname);  
    //$strPageContent .=  '<b>' . "Deleted $fname!" . '</b><br />';
  }
  $fdata = "";  // data to be written to a file;
  foreach ($_POST as $key => $val) {
    // handle class choices seperately
    //if ((strcmp($key,"selClasses") != 0) && --adieu from 2013
     if ( (strcmp($key,"classes")    != 0) &&
  (strcmp($key,"milongas")   != 0)) 
    {
      $fdata .= "$key = $val \n";
    }
  }


  /* Echo back user selections and compute the price */
  $total = 0;         // total price
  $fullPass = 0;      // full pass?
  $milongaPass = 0;   // milonga pass?
  $groupDiscountOK = 0;  // track selections to see
                         // if they qualify for a group discount
  $sundayMilonga = 0; // free w/ any class participation
  $food_fee = 0;      // $10. Only for sunday milonga and
                      //  only if not bringing food.

  $strPageContent .=  '<p>';
  //$strPageContent .=  "UNDER MAINTENANCE";
  // midName and address bye bye since 2013
  //$strPageContent .=  "$_POST[firstName] $_POST[midName] $_POST[lastName]" . '<br />';
  $strPageContent .=  "{$_POST['firstName']}  {$_POST['lastName']}" . '<br />';
  //$strPageContent .=  "$_POST[address]" . '<br />';
  $strPageContent .=  "{$_POST['city']} {$_POST['state']}, {$_POST['zip']}" . '<br />';
  $strPageContent .=  "{$_POST['phonenumber']}" . '<br />';
  $strPageContent .=  "{$_POST['email']}" .(validateDnsRecord($_POST['email'])). '<br />';
  $strPageContent .=  '&nbsp;<br />';

  $strPageContent .=  "Lead/follow: {$_POST['lefol']}" . '<br />';
  $strPageContent .=  "Dance level: {$_POST['danceLvl']}" . '<br />';
  $strPageContent .=  '&nbsp;<br />';

  /* Process class & milonga choices */
  /*
  // FULL PASS
  if (strcmp($_POST[selClasses],"fullPass") == 0) {
    $fullPass = 1;
    $groupDiscountOK = 1;
    $strPageContent .=  "Full pass = \$$full_pass_fee" . '<br />';
    $fdata .= "Full pass = \$$full_pass_fee \n";
    $total = $full_pass_fee;
  }
  // MILONGA PASS
  if (strcmp($_POST[selClasses],"milongaPass") == 0) {
    $milongaPass = 1;
    $groupDiscountOK = 1;
    $strPageContent .=  "Milonga pass = \$$milonga_pass_fee" . '<br />';
    $fdata .= "Milonga pass = \$$milonga_pass_fee \n";
    $total = $milonga_pass_fee;
  }
   */  
  // A-LA CARTE CLASSES
  if ($fullPass == 0) {
    $classCount = count($_POST['classes']);
    if ($classCount == 0) {
      $strPageContent .=  "No classes selected." . '<br />';
      $fdata .= "No classes selected \n";
    }
    else {
      $strPageContent .=  "A la carte classes:" . '<br />';
      $fdata .= "Classes: \n";
      foreach ($_POST['classes'] as $class) {
        $strPageContent .=  '&nbsp; &nbsp;' . "$class = \$" . $class_fee . '<br />';
  $fdata .= "   $class = \$" . $class_fee . "\n";
  $total += $class_fee;
      }
      if ($classCount > 1) {
        $groupDiscountOK = 1;
      }
    }
  }
  // A-LA CARTE MILONGAS
  if (($fullPass == 0) && ($milongaPass == 0)) {
    $strPageContent .=  '<br />';
    if (count($_POST['milongas']) == 0) {
      $strPageContent .=  "No milongas selected." . '<br />';
      $fdata .= "No milongas selected \n";
    }
    else {
      $strPageContent .=  "A la carte milongas:" . '<br />';
      $fdata .= "Milongas: \n";
      foreach ($_POST['milongas'] as $milonga) {
        $milonga_fee = $milonga_fees[$milonga];
/*
  // Sunday milonga is free with any class enrollment
  if ( (strcmp($milonga,"Sunday") == 0) &&
       (count ($_POST[classes]) > 0)) {
    $milonga_fee = 0;
    $sundayMilonga = 1;
  }
*/
        $strPageContent .=  '&nbsp; &nbsp;' . "$milonga = \$" . $milonga_fee . '<br />';
  $fdata .= "   $milonga = \$" . $milonga_fee . "\n";
  $total2 += $milonga_fee;
      }
      // added code to max out at package price - Partha
      if ( $total2 > $milonga_pass_fee ) $total2 = $milonga_pass_fee;
    }
  }
  //...and again, nobody should pay more than the full package - Partha 2012-03-28
  $total += $total2;
  if ($total > $full_pass_fee) $total = $full_pass_fee;

  /* Class & milonga fees: subtotal */
  $strPageContent .=  '<br />';
  $strPageContent .=  '<em />';
  $strPageContent .= sprintf("Class and Milonga subtotal: = \$%.2f", $total);
  $strPageContent .=  '</em />';
  $strPageContent .=  '<br />';
  $fdata .= sprintf("Subtotal = \$%.2f \n", $total);


  /* Process discounts */
  if ($_POST['isStudent']) {
    $studentDiscount = $total * 0.30;
    $strPageContent .= sprintf("30%s student discount = -\$%.2f \n", "%", $studentDiscount);
    $strPageContent .=  '<br />';
    $fdata .= sprintf("30%s student discount = -\$%.2f \n", "%", $studentDiscount);
    $total -= $studentDiscount;
  }
  $groupCode = $_POST['groupDiscount'];
  if (!($_POST['isStudent']) && strlen($groupCode) > 0) {
    // DO NOT MODIFY weekly discountcode here, breaks database code; see php/ASTFDatesOptions.php for explanation -- Partha
    if (strcmp(strtoupper($groupCode), $G_DISCOUNTCODE_THIS_WEEK) == 0) {
      if ($groupDiscountOK == 1) {
        $groupDiscount = $total * 0.10;
        $strPageContent .= sprintf("10%s group discount = -\$%.2f", "%", $groupDiscount);
        $strPageContent .=  '<br />';
        $fdata .= sprintf("10%s group discount = -\$%.2f \n", "%", $groupDiscount);
        $total -= $groupDiscount;
      }
      else {
        $strPageContent .=  '<b />' . "You must select at least 2 classes or a package to qualify for a group discount." . '</b /><br />';
      }
    }
    else {
      $strPageContent .=  '<b />' . "Bad group code. If you wish to re-enter the code, please use the link above to preserve other data." . '</b /><br />';
    }
  }

  /* Process any additional fees */

  // potluck fee?
/*
  if ( $sundayMilonga                               ||
       (strcmp($_POST[selClasses],"fullPass") == 0) ||
       (strcmp($_POST[selClasses],"passMinusAdv") == 0) ) {
     if ($_POST[potluckFee]) {
       $food_fee = 10;
       $strPageContent .=  '<br />';
       $strPageContent .=  "Sunday potluck fee: \$$food_fee" . '<br />';
       $fdata .= "Potluck fee: \$$food_fee \n";
       $total += $food_fee;
     }
  }
*/

  // Credit card fee?
 // if (strcmp($_POST[payment],"card")==0) {
 //   $card_fee = $total * 0.03;
 //   $strPageContent .=  '&nbsp;<br />';
 //   $strPageContent .= sprintf("3%s credit card fee = \$%.2f", "%", $card_fee);
 //   $strPageContent .=  '<br />';

 //   $fdata .= sprintf("3%s credit card fee = \$%.2f \n", "%", $card_fee);

 //   $total += $card_fee;
 // }

  /* Print total */
  $strPageContent .=  '<br /><b>';
  $strPageContent .= sprintf("Total: \$%.2f %s", $total, ($earlyBird ? " (Early bird rate)" : ""));
  //$strPageContent .=  '</b>';
  $fdata .= sprintf("Total = \$%.2f %s \n", $total, ($earlyBird ? " (Early bird rate)\n" : "\n"));

  
  //--------added by Partha to write to the database, wait until this point where
  // total is calculated and about to be transferred to paypal
  // careful - any error in script and the rest of the script does not go through
  require_once(dirname(dirname(dirname(__FILE__)))."/php/ASTFdatabase.php");
  try {
  if ( isset($_GET['dbg']) && ($_GET['dbg']=='post' || $_GET['dbg']=='all') ) print_r($_POST);
  // check a unique timestamp that's posted from the register.php page, to prevent duplicate database entry due to refresh or back button re-post
  if ( isset($_POST["firstSubmitToken"])  ) {
    if ( !isset($_SESSION["firstSubmitToken"]) || 
    ( isset($_SESSION["firstSubmitToken"]) && ( $_SESSION["firstSubmitToken"] != $_POST["firstSubmitToken"] ) ) )  {
      $_SESSION["firstSubmitToken"] = $_POST["firstSubmitToken"];
      if (isset($_SESSION["lastPost"])) {
        $lastPost = $_SESSION["lastPost"];
        //if (($lastPost['firstName'] != $_POST['firstName']) || ($lastPost['midName'] != $_POST['midName']) || ($lastPost['lastName'] != $_POST['lastName'])) {
        if (($lastPost['firstName'] != $_POST['firstName']) || ($lastPost['lastName'] != $_POST['lastName'])) {
          $_SESSION["attemptNo"] = 1;  // new person, reset no of attempts
        } else {
          $_SESSION["attemptNo"] += 1; // previous person, increase attempts
        }
      } else { $_SESSION["attemptNo"] = 1;} // first visit ever, session variable created
      $_SESSION["lastPost"] = $_POST;  // saved the last $_POST, in case registrant decides to go back
      // print debug statements
      if ( isset($_GET['dbg']) && ($_GET['dbg']=='attempts' || $_GET['dbg']=='all') ) $strPageContent .=   $_SESSION["attemptNo"];
      // will need the name of the cache file 
      //$strCacheFile= dirname(dirname(__FILE__))."/tmp/cache/".md5($_GET['yr']);

      if ($_SESSION["attemptNo"] > 1) {  // if this logic works, this is at least the 2nd visit to this page for this person for this session
        if ( isset($_GET['dbg']) && $_GET['dbg'] == 'nodb') {
          $strPageContent .=  "Not first attempt; bye, cache file {$strCacheFile}";
          $strRegistrationID = '0000000000';
        } else {
          $arrReturn = writeToDatabase($total,$_GET['yr'] ,'add');
          $strRegistrationID = $arrReturn['RegistrationID'];
	  if ($arrReturn['IsAbortedEventChoicesBad']) {
		$strPageContent .= "<br/>PROBLEM: At least one of your choices is already paid for, please talk to our staff<br/>";
		$strPageContent .= $arrReturn['BadChoices'];
	  }
          if (isset($_GET['dbg']) && $_GET['dbg']=='db') $strPageContent .=  "<br/>".print_r($arrReturn, true);
          // delete cached file if writing to database, data will be stale
          if (file_exists($strCacheFile)) {
            unlink($strCacheFile);
          }            
        }
      } else { // this branch is for the first visit in this session
        $_POST["ParticipantID"] = ""; // reset the participantID,, otherwise n+1 person overwrites nth persons data, from experience
        if ( isset($_GET['dbg']) && $_GET['dbg'] == 'nodb' ) {
          $strPageContent .=  "First attempt; bye, cache file {$strCacheFile}";
          $strRegistrationID = '0000000000';
        } else {
          $arrReturn = writeToDatabase($total,$_GET['yr'] ,'add');
          $strRegistrationID = $arrReturn['RegistrationID'];
	  if ($arrReturn['IsAbortedEventChoicesBad']) {
		$strPageContent .= "<br/>PROBLEM: At least one of your choices is already paid for, please talk to our staff<br/>";
		$strPageContent .= $arrReturn['BadChoices'];
	  }
          if (isset($_GET['dbg']) && $_GET['dbg']=='db') $strPageContent .=  "<br/>".print_r($arrReturn, true);
          // delete cached file if writing to database, data will be stale
          if (file_exists($strCacheFile)) {
              unlink($strCacheFile);
              //$strCacheFile1 = dirname(dirname(__FILE__))."/tmp/cache/".md5("all");
              //if (file_exists($strCacheFile1)) unlink($strCacheFile1);
          }            
        }
	// BUG discovered - 2014-02-22 case of Evelyn Xandre mixed with Roberts James after she corrected zipcode
        $_SESSION["lastPost"]["ParticipantID"] = ltrim(substr($strRegistrationID,4),"0");
      }
    }
  }
  }
  catch(Exception $e) {
    // move on - no use alarming user with error messages
  }

  // At this point we should have a registration id, write it to a cookie for a year, NEW in 2014 -- Partha
  setcookie("RegistrationID", $strRegistrationID, time() + (365*86400));

  $strPageContent .=  "<br/>RegistrationID: {$strRegistrationID}";
  $fdata .= sprintf("RegistrationID = %s \n", $strRegistrationID);
  $strPageContent .=  '</b>';
  $strPageContent .=  '</p>';
  /* generate the return URL */
  $s1 = $_POST;
  $s1['foodfee'] = $food_fee;
  $s1['total'] = $total;
  $s1['sid'] = session_id();
  $s1['registrationid'] = $strRegistrationID;
  $s2 = http_build_query($s1, '', '&amp;');

  $rurl = "http://www.austinspringtango.com/confirm.php?" . $s2;  // PayPal return URL
  $curl = "http://www.austinspringtango.com/cancel.php?" . $s2; // PayPal cancel URL
  $notifyurl = "http://www.austinspringtango.com/ASTFInstantPaymentNotify.php"; // PayPal payment notification URL
  //$strPageContent .=  $rurl . '<br />';


  //////// DELETE!!!!!! /////
  //$total = 0.01;


  /* Display the payment info */

  if (strcmp($_POST['payment'],"card")==0) {
    /* Write the registration record to a file */
    file_put_contents($fname, $fdata);

	if (!($arrReturn['IsAbortedEventChoicesBad'])) {
	$strPageContent .=  '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">';
	$strPageContent .=  '<input type="hidden" name="cmd" value="_xclick">';
	$strPageContent .=  '<input type="hidden" name="business" value="david.boucher@alum.mit.edu">';
	$strPageContent .=  '<input type="hidden" name="lc" value="US">';
	$strPageContent .=  '<input type="hidden" name="item_name" value="Austin Spring Tango Festival">';
	$strPageContent .=  '<input type="hidden" name="amount" value="' . number_format($total,2) . '">';
	$strPageContent .=  '<input type="hidden" name="currency_code" value="USD">';
	$strPageContent .=  '<input type="hidden" name="button_subtype" value="services">';
	$strPageContent .=  '<input type="hidden" name="no_note" value="1">';
	$strPageContent .=  '<input type="hidden" name="no_shipping" value="1">';
	$strPageContent .=  '<input type="hidden" name="rm" value="1">';
	$strPageContent .=  '<input type="hidden" name="return" value="' . $rurl . '">';
	$strPageContent .=  '<input type="hidden" name="cancel_return" value="' . $curl . '">';
	$strPageContent .=  '<input type="hidden" name="tax_rate" value="0.000">';
	$strPageContent .=  '<input type="hidden" name="shipping" value="0.00">';
	$strPageContent .=  '<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted">';
	$strPageContent .=  '<input type="hidden" name="notify_url" value="http://www.austinspringtango.com/ASTFInstantPaymentNotify.php">';
	$strPageContent .=  '<input type="hidden" name="on0" value="RegistrationID">';
	$strPageContent .=  '<input type="hidden" name="os0" value="' . $strRegistrationID . '">';
	$strPageContent .=  '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">';
	$strPageContent .=  '<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">';
	$strPageContent .=  '</form>';

	    /* Ask user to return to the site */
	    $strPageContent .=  '<p>';
	    $strPageContent .=  '&nbsp; <br>';
	    $strPageContent .=  '<em>' . "* Once the PayPal payment is processed, please click \"Return to Austin Spring Tango\" to finalize the order and return to this web site." . '</em>';
	}
  }
  else if (strcmp($_POST['payment'],"check")==0) {
    /* Write the registration record to a file */
    $fname .= "_CHECK";
    file_put_contents($fname, $fdata);

	if (!($arrReturn['IsAbortedEventChoicesBad'])) {
	    $strPageContent .=  '<p align="center">';
	    $strPageContent .=  'Please print this page and mail it with your payment.<br />';
	    $strPageContent .=  'Make the check out to "Austin Tango Society".<br />';
	    $strPageContent .=  '1618 W. 9 1/2 st <br />';
	    $strPageContent .=  'Austin, TX, 78703 <br />';
	    $strPageContent .=  '</p>';
	}
  }
  else if (strcmp($_POST['payment'],"chequeatdoor")==0) {
      $fname .= "_CHEQDOOR";
      file_put_contents($fname, $fdata);

      $strPageContent .=  '<p align="center">';
      $strPageContent .=  'Please pay at the front desk. Volunteers are standing by to assist you.<br/>';
      $strPageContent .=  '</p>';
  }
  else if (strcmp($_POST['payment'],"cashatdoor")==0) {
      $fname .= "_CASHDOOR";
      file_put_contents($fname, $fdata);

      $strPageContent .=  '<p align="center">';
      $strPageContent .=  'Please pay at the front desk. Volunteers are standing by to assist you.<br/>';
      $strPageContent .=  '</p>';
  }

?>
<!DOCTYPE html>
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
  <title>Spring Tango Festival :: Registration</title>
      <link href="css/teach_x.css" rel="stylesheet" type="text/css" />
  <!--[if lt IE 9]>
  <script src="http://austinspringtango.com/wp-content/themes/twentytwelve/js/html5.js" type="text/javascript"></script>
  <![endif]-->
  <link rel='stylesheet' id='googleFontsASTF-css'  href='http://fonts.googleapis.com/css?family=Oswald%3A300%2C400&#038;ver=3.7.1' type='text/css' media='all' />
  <link href="css/register.css" rel="stylesheet" type="text/css" />
  <link href="css/reg_print.css" rel="stylesheet" type="text/css" media="print" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes"/>
</head>
<body class="page custom-background full-width"> 

 <div id="main" class="hfeed site">
	<header id="masthead" class="site-header" role="banner">
		<hgroup>
			<h1 class="site-title"><a href="http://austinspringtango.com/" title="Austin Spring Tango Festival" rel="home">AUSTIN SPRING <span class="red">TANGO</span> FESTIVAL</a></h1>
			<h2 class="site-description">2015 • MARCH 27 • 28 • 29 • AUSTIN, TEXAS</h2>
		</hgroup>

	</header><!-- #masthead -->
   <div id="primary" class="site-content">
   <article class="page hentry">
   <header class="entry-header">
     <h1 class="entry-title">Verify Registration</h1>
   </header> 
   <div class="entry-content">  
   <p><strong style="color:#900;">NOTE:</strong> If you do have to go back to the previous page to change something, please do not use the browser's Back button, your changes might not be recorded properly, instead use the link here: <a href="register.php">BACK</a>.</p>

<?php
    echo $strPageContent;
?>

</div><!-- End div class="entry-content" -->
</article><!-- End article class="page hentry" -->
</div><!-- End div id="primary" -->
</div> <!-- End div id="main" -->

<!-- Navigation Script from WP TwentyTwelve Theme -->
<script type='text/javascript' src='http://austinspringtango.com/wp-content/themes/twentytwelve/js/navigation.js?ver=1.0'></script>
<!-- End Script -->
</body>
</html>

