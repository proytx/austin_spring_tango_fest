<?php
include 'class.Festival.inc.php';

$objFestival = new clsFestival('prod');
//echo print_r($objFestival->showDescription());
$arrFestivals = ($objFestival->getAll());
echo "<table style=\"border: 5px groove black; border-collapse: collapse;\">";
$blnHeader = 0;
foreach ($arrFestivals as $key => $val )
{
    unset($val['ID']);
    unset($val['EarlyBirdDay']);
    if ($blnHeader++ == 0) 
    {
        echo "<tr><th>".implode("</th><th>", array_keys($val))."</th></tr>";
    }
    echo "<tr><td>".implode("</td><td>", $val)."</td></tr>";
}
echo "</table>";
?>
