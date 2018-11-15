<?php

    function generateQRCodesIfNotFound($blnForceAll=false) { 
        // create xpath selector
    	$strEnvironment = "";
    	if (!isset($_SERVER["HTTP_HOST"]) || preg_match("/^test/i", $_SERVER["HTTP_HOST"])) $strEnvironment="test";
    	require(dirname(__FILE__).'/qrcode.inc.php');
        $objSelector = new DOMXPath($objDom);
        $objResults = $objSelector->query('//@title[starts-with(., "2015")]');

        $strUrlPart = "http://".(isset($_SERVER["HTTP_HOST"]) ? ($_SERVER["HTTP_HOST"]) : "test.austinspringtango.com")."/ASTFRegistrationFromQRUrl.php?id=";
        $strTmpdir = dirname(dirname(__FILE__)).'/www/testaustinspringtango/images/';
        $strErrorCorrectionLevel = 'L';
        $intMatrixPointSize = 4;
        $strFilesuffix = $strErrorCorrectionLevel.$intMatrixPointSize.".png";
        $arrReturn = array();
        include "phpqrcode/qrlib.php";
        foreach ($objResults as $objNode) {
            //echo $objNode->nodeValue.PHP_EOL;
            $strFilename = $strTmpdir.$strEnvironment.$objNode->nodeValue.$strFilesuffix;
            if ($blnForceAll || (!$blnForceAll && !file_exists($strFilename))) {
                $objResults2 = $objSelector->query('//td[position() = 2 and ../td[position() = 1 and @title = "'.$objNode->nodeValue.'"]]');
                $strName = "";
                foreach ($objResults2 as $objNode2) { $strName = $objNode2->nodeValue; } // should have only 1 match
                $arrReturn[$objNode->nodeValue] = $objNode2->nodeValue;
            }
            if (!file_exists($strFilename)) {
                QRcode::png($strUrlPart.$objNode->nodeValue, $strFilename, $strErrorCorrectionLevel, $intMatrixPointSize, 2); 
            }
        }
        return $arrReturn;
    }

?>
