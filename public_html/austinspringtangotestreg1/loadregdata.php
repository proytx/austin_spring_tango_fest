<?php
        error_reporting(E_ALL & ~E_NOTICE);
        ini_set('log_errors','1');
        ini_set('display_errors','0');
	include(dirname(dirname(dirname(__FILE__)))."/php/ASTFDatesOptions.php");
	//$strTableDiv = file_get_contents("${strCacheFile}.tbl");
	$strLZWDiv =  file_get_contents("${strCacheFile}.lzw");
	$strEmailDiv =  file_get_contents("${strCacheFile}.Email");
	$strPhonenumberDiv =  file_get_contents("${strCacheFile}.Phonenumber");
?>
<html>
	<head>
	</head>
	<body>
		<?php 
  			if (stristr( $_SERVER["HTTP_REFERER"], "register.php" )) {
				echo $strLZWDiv; 
			}
			echo $strEmailDiv; 
			echo $strPhonenumberDiv; 
		?>
	</body>
</html>
