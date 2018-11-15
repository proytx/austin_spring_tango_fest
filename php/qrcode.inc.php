<?php 
    $strFile= dirname(dirname(__FILE__))."/tmp/cache/".md5("all".$strEnvironment);
    $strContent = file_get_contents($strFile);
    $objDom = new DOMDocument();
    $objDom->loadHTML($strContent);
    $strContent = $objDom->saveHTML();
    $objDom->preserveWhiteSpace = FALSE;
    $objDom->loadHTML($strContent);
?>

