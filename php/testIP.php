<?php
$strIp = "70.112.130.153";
$strIp = "91.236.74.252";
$strIp = "108.90.117.158";
$strIp = "99.57.133.223";
$strIp = "66.193.121.197";
$strIp = "108.85.237.89";
$strIp = "97.79.141.250";
$intIp = ip2long($strIp);
$strOut = "";
//$arrTags = get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress={$strIp}');
echo $intIp."\n";
include 'ASTFdatabase.php';
$arrTags = getCityFromIpv4($intIp);
//$strCmd = "whois {$strIp}|grep Country|awk '{print \$2}'";
//echo $strCmd."\n";
//if ($arrTags['known'] == 'false') exec($strCmd, $strOut);
print_r($arrTags);
//echo $strOut."\n";
//print_r($strOut);
?>
