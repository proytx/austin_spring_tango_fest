<?php
	include(dirname(dirname(dirname(__FILE__)))."/php/ASTFDatesOptions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Austin Spring Tango Festival :: Registration Payment Cancelled</title>
    <link href="css/teach_x.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="main">
   <div id="head">
     <h2>Spring Tango Festival - 2015 </h2>
   </div>
<div id="body2">

<h3 align="center">Your order has been cancelled.</h3>
<?php
/*************************************************
 * User cancelled the transactio on PayPal's side
 *************************************************/

//foreach ($_REQUEST as $key => $val) {
//  echo "$key - $val" . '<br />';
//}

if (!empty($_GET)) {

	/* move the file record to _CANCEL */
	$fpath = "../../files/";
	$old_fname = $fpath . $_REQUEST['lastName'] . '_' . $_REQUEST['firstName'] . '_' . $_REQUEST['sid'];
	$new_fname = $old_fname . "_CANCEL";
	if (file_exists($old_fname)) {
	  rename($old_fname, $new_fname);
	  //echo 'Renamed! <br />';
	}
	//else {
	//  echo "File doesn't exist: $old_fname" . '<br />';
	//}
}
?>


<p style="text-align:center;margin-top:2em;"><a href="index.html">Return to the Austin Spring Tango Festival  Webpage</a></p>

</div><!-- END 'main' div -->
</div><!-- END 'body2' div -->
</body>
</html>
