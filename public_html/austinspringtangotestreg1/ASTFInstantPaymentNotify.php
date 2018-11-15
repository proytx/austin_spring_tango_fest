<?php
/**
* PHP-PayPal-IPN Example
*
* This shows a basic example of how to use the IpnListener() PHP class to
* implement a PayPal Instant Payment Notification (IPN) listener script.
*
* For a more in depth tutorial, see my blog post:
* http://www.micahcarrick.com/paypal-ipn-with-php.html
*
* This code is available at github:
* https://github.com/Quixotix/PHP-PayPal-IPN
*
* @package PHP-PayPal-IPN
* @author Micah Carrick
* @copyright (c) 2011 - Micah Carrick
* @license http://opensource.org/licenses/gpl-3.0.html
*/
 
 
/*
Since this script is executed on the back end between the PayPal server and this
script, you will want to log errors to a file or email. Do not try to use echo
or print--it will not work!

Here I am turning on PHP error logging to a file called "ipn_errors.log". Make
sure your web server has permissions to write to that file. In a production
environment it is better to have that log file outside of the web root.
*/
ini_set('log_errors', true);
ini_set('error_log', dirname(dirname(dirname(__FILE__))).'/ipnlog/ipn_errors.log');


// instantiate the IpnListener class
include('IpnListener.php');
$listener = new IpnListener();


/*
When you are testing your IPN script you should be using a PayPal "Sandbox"
account: https://developer.paypal.com
When you are ready to go live change use_sandbox to false.
*/
$listener->use_sandbox = false;

/*
By default the IpnListener object is going to post the data back to PayPal
using cURL over a secure SSL connection. This is the recommended way to post
the data back, however, some people may have connections problems using this
method.

To post over standard HTTP connection, use:
$listener->use_ssl = false;

To post using the fsockopen() function rather than cURL, use:
$listener->use_curl = false;
*/

/*
The processIpn() method will encode the POST variables sent by PayPal and then
POST them back to the PayPal server. An exception will be thrown if there is
a fatal error (cannot connect, your server is not configured properly, etc.).
Use a try/catch block to catch these fatal errors and log to the ipn_errors.log
file we setup at the top of this file.

The processIpn() method will send the raw data on 'php://input' to PayPal. You
can optionally pass the data to processIpn() yourself:
$verified = $listener->processIpn($my_post_data);
*/
try {
    $listener->requirePostMethod();
    $verified = $listener->processIpn();
	//error_log("Verified = $verified");
} catch (Exception $e) {
    error_log($e->getMessage());
    exit(0);
}


/*
The processIpn() method returned true if the IPN was "VERIFIED" and false if it
was "INVALID".
*/
if ($verified) {
    /*
Once you have a verified IPN you need to do a few more checks on the POST
fields--typically against data you stored in your database during when the
end user made a purchase (such as in the "success" page on a web payments
standard button). The fields PayPal recommends checking are:
1. Check the $_POST['payment_status'] is "Completed"
2. Check that $_POST['txn_id'] has not been previously processed
3. Check that $_POST['receiver_email'] is your Primary PayPal email
4. Check that $_POST['payment_amount'] and $_POST['payment_currency']
are correct
*/
     $errmsg = '';   // stores errors from fraud checks
    
    // 1. Make sure the payment status is "Completed" / "Refunded"
	$strPaymentStatus = isset($_POST['payment_status']) ? $_POST['payment_status']: '';
 	//error_log("Payment status $strPaymentStatus" );
	if (($strPaymentStatus != 'Completed') && ($strPaymentStatus != 'Refunded')) { 
        // simply ignore any IPN that is not completed or refunded, note the capitalized first letter
		//error_log("Premature exit - status not Completed or Refunded");
        exit(0); 
    }

    // 2. Make sure seller email matches your primary account email.
    if ($_POST['receiver_email'] != 'david.boucher@alum.mit.edu') {
        $errmsg .= "'receiver_email' does not match: ";
        $errmsg .= $_POST['receiver_email']."\n";
    }
	
	//try {
	include(dirname(dirname(dirname(__FILE__)))."/php/ASTFdatabase.php");
	$arrAssoc = array();
	$strTotal = '';
	$strTransactionID = '';
	$strIdentifyingFieldName = '';
	$strIdentifyingFieldValue = '';
	$strRegistrationID = '';
	if ($strPaymentStatus == 'Completed') {
		$strIdentifyingFieldName = isset($_POST['option_name1']) ? ($_POST['option_name1']) :''; 
		$strIdentifyingFieldValue = isset($_POST['option_selection1']) ? $_POST['option_selection1']:'';
	}	else if ($strPaymentStatus == 'Refunded') {	// in case of a refund, find the parent transaction id
		$strIdentifyingFieldName = 'CheckOrConfirmationNo';	// this field 
		$strIdentifyingFieldValue = isset($_POST['parent_txn_id']) ? $_POST['parent_txn_id']:'';
	}
	//error_log("From POST, $strIdentifyingFieldName = $strIdentifyingFieldValue");
	if ( strlen($strIdentifyingFieldName)>0 && strlen($strIdentifyingFieldValue)>0 ){
		// if no results come back, could mean ipn has been called before the database 
		// written to by the registration page, highly unlikely
		$strSql=findTransactionById($strIdentifyingFieldName, $strIdentifyingFieldValue,$arrAssoc);
		//error_log($strSql);
		if ($arrAssoc) {
			$strTotal = $arrAssoc['Total'];
			$strTransactionID = $arrAssoc['TransactionID'];
			$strRegistrationID = $arrAssoc['RegID'];
			if ($strPaymentStatus == 'Refunded') { $strTotal = "-".$strTotal; }
			//error_log("Found registration ID = $strRegistrationID and transaction ID = $strTransactionID with total = $strTotal" );
		}
	} 
    //} catch (Exception $e) {
    //error_log($e->getMessage());
    //exit(0);	
	//}
	
    // 3. Make sure the amount(s) paid match, only if a transaction was found before, in case of refund, partial refunds possible?
    if ( strlen($strTotal)>0  && $_POST['mc_gross'] != $strTotal) {
        $errmsg .= "'mc_gross' does not match: ";
        $errmsg .= $_POST['mc_gross']."\n";
    }
    
    // 4. Make sure the currency code matches
    if ($_POST['mc_currency'] != 'USD') {
        $errmsg .= "'mc_currency' does not match: ";
        $errmsg .= $_POST['mc_currency']."\n";
    }

    // 5. Ensure the transaction is not a duplicate.
    
    if ( $strTransactionID && ($strTransactionID == $_POST['txn_id']) ) {
        $errmsg .= "'txn_id' has already been processed: ".$_POST['txn_id']."\n";
    }

	// Here we are deviating a bit from normal practice, the serious errors 
	// that stop us from recording a transaction are transaction ID, receiver email and currency
	// But if mc_gross does not match, it should not go unrecorded in the database because
	// there isn't a fixed price - also sometimes verbal agreements are made with festival
	// organizer to lower the price (i.e, scholarships). I have requested the organizer to settle
	// down on a few pre-determined scholarship levels to avoid this and other problems - Partha
    $strPosition = strpos($errmsg, "mc_gross");
    if (!empty($errmsg) && ($strPosition === false) ) {	// triple equals checks type also
		// i.e., there are errors, but not related to mc_gross, then enter here
        // manually investigate errors from the fraud checking
        $body = "IPN failed various checks: \n$errmsg\n\n";
        $body .= $listener->getTextReport();
        mail('proytx@yahoo.com', 'IPN Warning', $body);
		
    } else {   
		if (strlen($strRegistrationID)>0) {	// cannot record blank registration ID's
			$strResult = addIpnConfirmation($strRegistrationID, $_POST['mc_gross'], $_POST['txn_id'],$strPaymentStatus);       
			// send registrant an email 
			$to = filter_var($_POST['payer_email'], FILTER_SANITIZE_EMAIL);
			$subject = "Austin Spring Tango payment ".$strPaymentStatus;
			error_log($strResult);
			//mail($to, $subject, "Thank you.");
		}
		
    }
    //mail('proytx@yahoo.com', 'Verified IPN', $listener->getTextReport());
} else {

/* An Invalid IPN *may* be caused by a fraudulent transaction attempt. It's
a good idea to have a developer or sys admin manually investigate any
invalid IPN. */
    mail('proytx@yahoo.com', 'Invalid IPN', $listener->getTextReport());
}

?>
