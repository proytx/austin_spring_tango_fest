<?php
  date_default_timezone_set("America/Chicago");
// This line is changed automatically by a cronjob every week -- cronjob disabled 2014
  $G_DISCOUNTCODE_THIS_WEEK = "BOLEO";

// TEMPORARY FIX NIXED - DISCOUNT CODES THEMSELVES AS WELL AS THE TIMES WHEN THEY TRANSITION ARE TOTALLY DIFFERENT
// THE CRON JOB HAS ALREADY BEEN DEBUGGED ~ FEB 25TH AND WORKS, BUT WE'LL SEE SUNDAY MIDNIGHT
// WISH THE COMMENT BELOW HAD SOMETHING MORE ELABORATE, MAYBE MICHAEL, YOU FOUND SOMETHING THAT
// LOOKED SUSPICIOUS? ..that's supposed to be writing the $G_DISCOUNTCODE_THIS_WEEK...well it did write the value GANCHO
// Is it because it didn't match your calculations below? Could it be because the algorithms are different?
// The code below starts from a certain Tuesday, Jan 28th and keeps changing every 7 days...what about the 
// cron job algorithm? 
/*
 * TEMPORARY FIX — I think there's something wrong with the CRON Job. that's supposed 
 * to be writing the $G_DISCOUNTCODE_THIS_WEEK value above...???
 */

/**
 * Determines discount code based on current date
 * 
 * @param string $startDate A DateTime-readable string stating the beginning of the dicount period.
 * @param array $weeklyCodes An array discount codes, one for each week.
 * @return string The current discount code.
 */
/*
function getDiscountCode($startDate,$weeklyCodes) {
  $now = new DateTime();
  $weekOne = new DateTime($startDate); 
  $weekTwo = clone $weekOne;
  $weekTwo->add(new DateInterval('P7D'));

  foreach ($weeklyCodes as $code) {
    if ($weekOne <= $now && $now < $weekTwo) {
        return $code;
    } else {
      $weekOne->add(new DateInterval('P7D'));
      $weekTwo->add(new DateInterval('P7D'));
    }
  }
  return '';
}

//$G_DISCOUNTCODE_THIS_WEEK = getDiscountCode('Tue, 28 Jan 2014 00:00:01', array('OCHOCORTADO','SACADA','GANCHO','BARRIDA','COLGADA','ENROSQUE','BOLEO','VOLCADA'));
*/
/* END TEMPORARY FIX */
// Moved environment settings to this file
$strEnvironment = "";
if (preg_match("/^test/i", $_SERVER["HTTP_HOST"])) $strEnvironment="test";
$strCacheFile= dirname(dirname(__FILE__))."/tmp/cache/".md5("all".$strEnvironment);

// these 2 variables are needed in if and else branches, hence this code is on top - Partha 2011-11-12
  $dtmNow = strtotime("now");
  $dtmThisYear = strftime("%Y");
  // the festival has always happened on the last weekend of March, instead of hardcoded dates 
  // that someone has to change 2x per year, let the computer figure it out, if things change later, come back to it - Partha 2011-11-12
  $dtmFestStartDate = strtotime($dtmThisYear."-04-02 last Friday");  // time is 12:00 midnight of that last Friday in March 
  // FINALLY 2013 the festival date does not follow the rule, have to hardcode
  //if ($dtmThisYear == '2013') $dtmFestStartDate = strtotime("22 March 2013");
  $dtmFestEndDate = strtotime("next Monday", $dtmFestStartDate);  // time is 12:00 midnight of the following Monday
  // this code added by Partha Feb 21 2011, to autofill form with database info from past year
  // note - people with invites can get in even if registration is not open, which sounds OK, they get special status
  $blnAccessAllowed = false;
  $blnOnsiteOperation = false;
  $strVisitorId = "";
  if (preg_match("/^(test|intranet).austinspringtango/i", $_SERVER["HTTP_HOST"])) {
        $arrIpList = Array('64.12.116.0',
            '64.12.117.0',
            '173.174.122.8', //Vance
            '205.188.116.0', //Vance
            '72.179.44.136', //Vance Time warner
            //'72.177.145.252', //Jo
            //'50.84.126.194', //Jo Hotel
            '66.68.152.167', //Monza old home
            '108.224.40.138', //Monza new home
            '99.62.33.37', //Monza new home
            '70.112.130.153', //Monza's new ISP in new? home, hopefully stable
            '70.112.16.56',
            '66.68.17.247',
            '64.134.253.102',
            '216.12.246.0',
            '216.12.244.0',
            '208.54.86.0',
            '99.57.132.84',
            '99.5.213.38', // San Antonio
            '108.82.80.44', // Dance Inst
            '99.108.109.78', // Dance Inst - 2014
            '108.73.6.68', // Simona
            '216.12.228.0',
            '216.12.249.0',
            '97.77.123.139',  //whipin
            '23.114.58.192',  //fair bean
            '24.242.143.6',  //summermoon
            '65.36.75.198', // Michael
            '72.182.57.246', // Jan
            '71.22.126.239', // Michael?
            '99.190.133.138', // David 
            '24.155.160.209', // David / Michael
	    '107.214.151.87', //  Michael's bro
	    '128.30.52.73', //html validator 
	    '71.21.115.162', //freedompop 4G hub
	    '108.248.87.252', //Francesco conf room
	    '108.90.116.82', //Rosemary Ang
	    '208.64.38.55'); // whatsmyip compresiion tester

	 
        $lngIpAddress = ip2long($_SERVER['REMOTE_ADDR']);
        foreach ($arrIpList as $strIpMember) {
            if ((ip2long($strIpMember) ^ ($lngIpAddress)) < 256) {
                $blnAccessAllowed = true;
		//error_log("$strIpMember worked for access");
                break;
            }
        }
	if (!$blnAccessAllowed) {header('Location: http://austinspringtango.com/'); exit();}
  } elseif (isset($_GET['id'])){ // added special key to unlock the page (available any time of the year) - Partha 2011-11-11
	$strVisitorId = $_GET['id'];
	// used invite codes one year which were truncated versions of these long strings, haven't used them since, so thought using one for unlocking access
	if ($_GET['id']=='00d1dc87b40539415edd2fc7679b75cec75c4d57') $blnAccessAllowed = true;
	//error_log ("Getting access via id");
  } elseif (isset($_COOKIES['RegistrationID'])){
	$strVisitorId = $_COOKIES['RegistrationID'];
	//error_log ("Getting access via cookie");
  }	// end  of checks for special access priveleges

  // this branch of code to redirect used to run 2st, but now this is only run if there is no id attached
  // Logic changed to allow the dates to be calculated for every visitor, regardless, and kicked out unless the above special access criteria met
$intIpAddress = ip2long($_SERVER["REMOTE_ADDR"]);
//$d2 = strtotime("25 March 2011");  // time is 12:01 am - obsolete hardcoding Partha 2011-11-12
if ($dtmNow > $dtmFestEndDate) {
	// now check if next years registration is open yet, i.e. Thanksgiving yet? Won't reach this code in Jan, Feb, Mar upto fest startdate
	$dtmThanksgiving = strtotime("November $dtmThisYear Thursday + 3 weeks");
	// left the following line as a debug example
	// echo "Thanksgiving is ".date("Y-m-d",$dtmThanksgiving);
	if (!$blnAccessAllowed && ($dtmNow < $dtmThanksgiving)) {
		// Sorry, bye bye, registration for next year's spring fest is not yet open, redirect to register3.php
		// Make sure that code below does not get executed when we redirect. 
		header( 'Location: http://www.austinspringtango.com/thankyou.shtml' );
		exit;
	} // end if earlier than thanksgiving
	// this year's festival has gone by, calculate the festival date that is of real interest to this website visitor
	$dtmFestStartDate = strtotime(($dtmThisYear+1)."-04-02 last Friday"); 
	$dtmFestEndDate = strtotime("next Monday", $dtmFestStartDate);  // time is 12:00 midnight of the following Monday
} elseif ($dtmNow > $dtmFestStartDate) {  // if not after the end of the festival, is it after the startdate midnight?
	// if the person has no invite ID, check where they are connecting from, and don't entertain outside US (in future might be a list of allowed countries)
	//...they might also be here right before the festival - the IP address for the Festival changes year to year 
	$dtm12HrsBeforeFestival = strtotime("+ 8 hours", $dtmFestStartDate);
	if (!$blnAccessAllowed && ($dtmNow > $dtm12HrsBeforeFestival)) {
		$blnOnsiteOperation = true;
		//if  (ip2long('108.82.80.44') != $intIpAddress) {header('Location: http://austinspringtango.com/thankyou.shtml');
		//				exit;}
	}

}
// if the person has no invite ID or unlock ID, first see if they arrived after festival ended
// if ($dtmNow > $dtmFestEndDate) {header('Location: http://austinspringtango.com/index.html')
//						exit;}


require_once("ASTFdatabase.php");
// The IP address checking code follows, this is exercised only when a visitor has no id, invite ID, and it's after thanksgiving
$arrTags = getCityFromIpv4($intIpAddress);
error_log(print_r($arrTags, true));
if (!$arrTags) {  //  we use 2 services, geobytes to get city state country, and if failed, whois to get country only; empty array will return false
	// geobytes silently changed their API so the old version didn't work, discovered today, Feb 18 2015
	//$arrTags = get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress='.$_SERVER["REMOTE_ADDR"]);
	$arrTags =json_decode(file_get_contents('http://gd.geobytes.com/GetCityDetails?fqcn='.$_SERVER["REMOTE_ADDR"]), true);
	//error_log(print_r($arrTags, true));
	//if ($arrTags['known'] == 'false') {  // noticed some IP's were still not in the database even though US based so get just the country for now
	if (!isset($arrTags['geobytesinternet']) || !$arrTags['geobytesinternet'] ) {  // new API , new check for unknown results
		$arrOut = array();
		exec("whois {$_SERVER['REMOTE_ADDR']}|grep Country|awk '{print \$2}'", $arrOut);
		if ($arrOut) {
			//$arrTags['iso2'] = $arrOut[0];
			$arrTags['geobytesinternet'] = $arrOut[0];
			//$arrTags['regioncode'] = $arrOut[0];
			$arrTags['geobytescode'] = '';
			//$arrTags['city'] = $arrOut[0];
			$arrTags['geobytescity'] = '';
		}
	}
}

// This mandatory database write ensures that the entries are updated in case they change (likely??) and to update number of visits
if ( isset($arrTags['geobytesinternet']) ) {
	// Old fields map to newly named fields
	//storeIpv4(array($intIpAddress, "'".$arrTags['iso2']."'", "'".$arrTags['regioncode']."'", "'".$arrTags['city']."'"));
	storeIpv4(array($intIpAddress, "'".$arrTags['geobytesinternet']."'", "'".$arrTags['geobytescode']."'", "'".$arrTags['geobytescity']."'"));

}
//print_r($arrTags);
//if (($arrTags['iso2'] != 'US') && ($arrTags['iso2'] != 'CA') && ($arrTags['iso2'] != 'MX')) {header('Location: http://austinspringtango.com/index.php');exit();}
if (($arrTags['geobytesinternet'] != 'US') && ($arrTags['geobytesinternet'] != 'CA') && ($arrTags['geobytesinternet'] != 'MX')) {header('Location: http://austinspringtango.com/index.php');exit();}
?>
