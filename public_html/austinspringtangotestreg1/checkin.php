<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
        error_reporting(E_ALL & ~E_NOTICE);
        ini_set('log_errors','1');
        ini_set('display_errors','0');
  include(dirname(dirname(dirname(__FILE__)))."/php/ASTFDatesOptions.php");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title> Austin Spring Tango Festival :: Checkin</title>
  <link rel='stylesheet' id='googleFontsASTF-css'  href='http://fonts.googleapis.com/css?family=Oswald%3A300%2C400&#038;ver=3.7.1' type='text/css' media='all' />
	<link href="css/checkin.css" rel="stylesheet" type="text/css" />
</head>

<body>  
 <div id="main">
   <div id="head">
        <h3><small> Welcome to the checkin page of The Austin Spring Tango Festival</small> </h3>
   </div>
   <div id="body2" class="c4">
	<div><a href = "register.php?type=Preregistered&yr=2015">Already registered at our WEBSITE this year? <br/><br/>Click or touch to checkin</a></div>
	<div><a href = "register.php?type=Repeat&yr=2015">Not registered this year, but at least once at our WEBSITE between 2010-14? <br/><br/>Click or touch to re-register</a></div>
	<div><a href = "register.php?type=New&yr=2015">Never registered at our WEBSITE for this festival? <br/><br/> Click or touch to register</a></div>
   </div>
 </div>
</body>
</html>
