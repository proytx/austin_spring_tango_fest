<?php
	include(dirname(dirname(dirname(__FILE__)))."/php/ASTFDatesOptions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Austin Spring Tango Festival :: Registration Payment Confirmed</title>
    <link href="css/teach_x.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="main">
   <div id="head">
     <h2>Austin Spring Tango Festival - 2015 </h2>
   </div>
<div id="body2">


<?php

//foreach ($_REQUEST as $key => $value) {
//  error_log( "$key = $value \n");
//}
// Only checking for the existence of these URL parameters should suffice, in register2.php empty strings are blocked from submissions
if ( isset($_REQUEST['lastName']) && isset($_REQUEST['firstName']) && isset($_REQUEST['sid']) ) {

	/* mark the record as completed */
	$fpath = "../../files/";
	$old_fname = $fpath . $_REQUEST['lastName'] . '_' . $_REQUEST['firstName'] . '_' . $_REQUEST['sid'];
	$new_fname = $old_fname . "_DONE";

	$good_file = 1;  // assume file exists & not damaged

	/* If file exists, append transaction ID and rename to _DONE */
	if (file_exists($old_fname)) {
	  // append the transaction id from PayPal
	  $fdata = "txn_id = {$_REQUEST['tx']} \n\n";
	  file_put_contents($old_fname, $fdata, FILE_APPEND);

	  rename($old_fname, $new_fname);
	  //echo 'Renamed! <br />';
	}
	else {  /* File not found! Create a new one given what we can salvage */
	  //echo '<br />File does not exist: ' . $old_fname . '<br />';
	  $good_file = 0;

	  // if file doesn't exist, we still want a registration record
	  foreach ($_REQUEST as $key => $value) {
	    $fdata .= "$key = $value \n";
	    if (strcmp($key,"classes")==0) {
	      foreach ($_REQUEST[classes] as $class) {
		$fdata .= "   $class \n";
	      }
	    }
	    if (strcmp($key,"milongas")==0) {
	      foreach ($_REQUEST[milongas] as $milonga) {
		$fdata .= "   $milonga \n";
	      }
	    }
	  }
	  file_put_contents($new_fname, $fdata);
	}

	/* Send email -- 2015 added email to registrant */
	$to = ($_REQUEST['email']) ? ($_REQUEST['email']) : 'proytx@yahoo.com';
	$subject = "Registration for {$_REQUEST['lastName']}, {$_REQUEST['firstName']}";
	$body = file_get_contents($new_fname);

	$header = 'Reply-To: marbore@michaelarbore.com' . "\r\n";
	$header .= 'Cc: astf@austintangosociety.org' . "\r\n";
	//$header .= 'Bcc: bgrot@yahoo.com,jjaguar05@sbcglobal.net' . "\r\n";

	mail($to, $subject, $body, $header);
}
?>


<h3 style="text-align:center">Your registration has been successfully processed!</h3>
<p><u>Your registration record: </u><br />

<?php
/* Print summary of user's transaction */

// Normal flow: read in the formatted info from the file 
if ($good_file) {
  $printFlag = 0;
  $fp = fopen($new_fname, "r");
  if ($fp) {
    $lineCtr = 0;
    while (!feof($fp)) {
      // Scan the file line by line until we hit class choices
      // These happen to start on line 13.
      $lineCtr++;
      $line = fgets($fp);
      if ($printFlag) {
        echo $line . '<br />';
      }
      else { // search for a pattern
        if (preg_match('/\Aregister/', $line)) {
	  $printFlag = 1;
	}
      }
    }
  }
}
else {  // File was damaged: do what we can
  if (strcmp($_REQUEST[selClasses],"fullPass") == 0) {
    echo 'Full pass <br />';
  }
  else if (strcmp($_REQUEST[selClasses],"passMinusAdv") == 0) {
    echo 'Weekend pass without Sunday\'s advanced class <br />';
  }
  // If individual classes/milongas were chosen, the info is available
  // but needs to be reconstructed from the REQUEST string. I was too lazy 
  // to cover this tedious & unlikely case and instead punt and just
  // Print the total
  echo '&nbsp;<br /><b>';
  printf("Total: \$%.2f", $_REQUEST[total]);
  echo '</b>&nbsp;';
  if ($_REQUEST[foodfee] > 0) {
    echo '(includes $10 potluck fee)';
  }
}

?>
</p>


<p><a href="http://austinspringtango.com">Go back to Austin Spring Tango Festival</a></p>

</div><!-- END 'main' div -->
</div><!-- END 'body2' div -->
</body>
</html>
