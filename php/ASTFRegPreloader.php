<?php

  require_once("ASTFdatabase.php");
  $arrAssoc = array();
  // the year is not used, so not to worry
  if (strlen($strVisitorId)>0) getOnePerson($strVisitorId,'2010',$arrAssoc);
  $arrAssoc['city']= strtoupper($arrTags['city']);
  $arrAssoc['state']= $arrTags['regioncode'];

  
  // finally if it made it upto this, it is not being redirected
  // NOTE:- Only people with the unlock ID or invite ID get to this point after the festival, or if they match the IP requirements before the festival
  $dtmEarlyBirdDate = strtotime(" - 19 days",$dtmFestStartDate);
  $strEarlyBirdDate = strftime("%l:%M%p %b %d,%Y",strtotime(" - 1 min", $dtmEarlyBirdDate)); 
  if ($dtmFestStartDate) {
    $strFestYear = strftime("%Y", $dtmFestStartDate);
    $strFestStartDate = strftime("%B %e", $dtmFestStartDate);
  }
  if ($dtmFestStartDate && $dtmFestEndDate) {
    if (strftime("%b", $dtmFestStartDate) != strftime("%b", strtotime(" - 1 min", $dtmFestEndDate))) {
      $strFestEndDate = strftime("%b %e", strtotime(" - 1 min", $dtmFestEndDate));
    } else {
      $strFestEndDate = strftime("%e", strtotime(" - 1 min", $dtmFestEndDate));
    }
  }
  $arrClasses = array();
  $arrPrivates = array();
  $arrTeachers = array();
  $arrTimeSlots = array();
  $arrPrivatesTimeSlots = array();
  $strDebug = getTeachersClasses($dtmFestStartDate,$arrTeachers,$arrTimeSlots,$arrClasses);
  $strPrivates = getTeachersPrivates($dtmFestStartDate,$arrPrivatesTimeSlots,$arrPrivates);

?>
