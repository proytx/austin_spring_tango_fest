<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php 
    require(dirname(dirname(dirname(__FILE__))).'/php/qrcode.inc.php');

    if (preg_match("/^2[0-9]{8}/", $_GET["id"])) { // check for the 9 digit registration ID, if this gets longer change code 
        $strOutput = "";
        $arrDiscountText = array( "0" => "No Discount",
                             "1" => "Student Discount (30%)",
                             "2" => "Group Discount (10%)", 
                             "4" => "Full Discount",
                             "8" => "Host Discount (50%)"
                             );
        $arrItemIds = array( "class1", "class2", "class3", "class4", "class5", "class6", "milonga1", "milonga4", "milonga2", "milonga3");
        $arrItems = array( "Friday Class 8:00-9:30 PM",
                             "Saturday Class 11:00-12:30 PM",
                             "Saturday Class 1:00-2:30 PM",
                             "Sunday Class 1:00-2:30 PM",
                             "Sunday Class 3:00-4:30 PM",
                             "Sunday Class 5:00-6:30 PM",
                             "Friday Milonga 10:00PM-2:00AM",
                             "Saturday Afternoon Milonga 3:00PM-6:00PM",
                             "Saturday Milonga 9:00PM-6:00AM",
                             "Sunday Asado Milonga 7:00PM-1:00AM"
                            );
        // create xpath selector
        $objSelector = new DOMXPath($objDom);
        $objResultsHeader = $objSelector->query('//thead/tr/th');
        $arrColNames = array();
        foreach ($objResultsHeader as $objNode) {
            $arrColNames[] = $objNode->nodeValue; // supposedly much  faster than array_push
        }

        //$objResults = $objSelector->query('//tr/td[position() = 5 and ../td[position() = 1 and @title = "'.$_GET['id'].'"]]/@title');
        $objResults = $objSelector->query('//tr[./td[position() = 1 and @title = "'.$_GET["id"].'"]]');
        foreach ($objResults as $objNode) {
            $intColumn = 0;
            $objCells = $objNode->getElementsbyTagName("td");
            foreach ($objCells as $objCell) {
                if ($objCell->hasAttribute("title")) {
                    if ($arrColNames[$intColumn] == "SignedUpFor") {
                        $strOutput .= "<br/>";
                        $arrSplit = split(",", $objCell->getAttribute("title"));
                        for ($i = 0; $i < count($arrSplit); ++$i) {
                            $strOutput .= "<span id=\"".$arrItemIds[$i]."\" class=\"".$arrSplit[$i]."\">".$arrItems[$i]."</span>";
                            if ($i == 5) $strOutput .= "<br/>";
                        }
                    } else {
                        $strOutput .= "<span id=\"".$arrColNames[$intColumn]."\">".$objCell->getAttribute("title")."</span>";
                    }
                } else {
                    if ($arrColNames[$intColumn] == "DiscountCode") {
                        $strOutput .= "<span id=\"".$arrColNames[$intColumn]."\">".$arrDiscountText[$objCell->textContent]."</span>";
                    } else if ($arrColNames[$intColumn] == "History") {
                        $strTmp = "";
                        $arrElements = $objCell->childNodes;
                        foreach ($arrElements as $objElement) {
                            if (strpos($objElement->textContent, "Paypal confirmed") !== false ||
                                strpos($objElement->textContent, "Paypal refund confirmed") !== false ||
                                strpos($objElement->textContent, "Cheque received") !== false) {
                                $strTmp .= (strlen($strTmp>0) ? ("<br/>") : "").$objElement->textContent;
                            }
                        }
                        $strOutput .= "<span id=\"".$arrColNames[$intColumn]."\">".$strTmp."</span>";
                    } else {
                        $strOutput .= "<span id=\"".$arrColNames[$intColumn]."\">".$objCell->textContent."</span>";
                    }
                }
            	++$intColumn;
            }
        } // end of cycling through query results in case more than one, which is pretty much impossible
    } else {
        $strOutput = "<span>garbage in garbage out</span>";
    }
    
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title> Individual Registration Details Page </title>
    <link href="css/regqrcode.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <?php echo $strOutput; ?>
</body>
</html>  
