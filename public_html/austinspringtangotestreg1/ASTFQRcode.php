<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php 
    $strOutput = "";
    $strEnvironment = "";
    if (!isset($_SERVER["HTTP_HOST"]) || preg_match("/^test/i", $_SERVER["HTTP_HOST"])) $strEnvironment="test";
    if ((isset($_GET["id"]) && $_GET["id"] == "all") || !isset($_GET["id"])) {
        require(dirname(dirname(dirname(__FILE__))).'/php/generateQRCodes.php');

	$blnForceAll = false;
	if (isset($_GET["id"])) $blnForceAll=true;
        $arrOutput = generateQRCodesIfNotFound($blnForceAll);
        foreach ($arrOutput as $key=>$value) {
            $strOutput .= "<div class=\"outer\"><img src=\"images/".$strEnvironment.$key."L4.png\" alt=\"HTML5 icon\" width=\"150px\" height=\"150px\">";
            $strOutput .= "<div class=\"inner\">".$value."</div></div>";
        }
    } else if (isset($_GET["id"])) {
        require(dirname(dirname(dirname(__FILE__))).'/php/qrcode.inc.php');

        if (preg_match("/^2[0-9]{8}/", $_GET["id"])) { // check for the 9 digit registration ID, if this gets longer change code 
            $strOutput = "<img src=\"images/".$strEnvironment.$_GET['id']."L4.png\" alt=\"HTML5 icon\" width=\"150px\" height=\"150px\">";
        }
    }
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title> Participant Registration QR codes </title>
    <link href="css/qrcodes.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <?php echo $strOutput; ?>
</body>
</html>  
