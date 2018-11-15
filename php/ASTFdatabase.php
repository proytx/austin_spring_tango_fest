 <?php
//##############################################################################   
    class ASTFdatabase {
      private $hndDatabase;
      private $strEnvironment;
      
      //use the constructor to set environment. default is provided
      //as production so it continues to work with old code. 
      //For test it has to be overridden
      public function __construct($strEnvironment="prod") {
        if (preg_match("/^test/i", $_SERVER["HTTP_HOST"])) {
		$strEnvironment="test";
		$this->strEnvironment = $strEnvironment;
	}
        require(dirname(__FILE__).'/config.astfdb_'.$strEnvironment.'.inc.php');
        date_default_timezone_set("America/Chicago");
        $this->hndDatabase=mysql_connect($arrCredentials['DBhost'], $arrCredentials['DBuser'], $arrCredentials['DBpass']);
        if ($this->hndDatabase) {
             //echo "now opening database";
             mysql_select_db($arrCredentials['DBname'],$this->hndDatabase);
        } // end if $this->$hndDatabase
          else {
             echo (mysql_error());
             mysql_close($this->hndDatabase);
        } // end else $this->$hndDatabase
      }

      //clean up in the destructor
      public function __destruct() {
        //echo "now closing database";
        mysql_close($this->hndDatabase);
      }
 
      //general purpose function to take wellformed SQL and act on ASTF
      public function runSQL($strSql,$blnRawResults=false, &$varRowsAffectedOrXml = null) {
         //return $this->transformToXml(mysql_query($strSql));
         if ($blnRawResults) {
             $objResult = mysql_query($strSql);
             // SELECT, SHOW, DESCRIBE, EXPLAIN return resultset if successful, otherwise false
             if ((!strncmp($strSql,"SELECT",6) || 
		!strncmp($strSql,"SHOW",4) || 
		!strncmp($strSql,"DESCRIBE",8) || 
		!strncmp($strSql,"EXPLAIN",7)) 
				&& $objResult === false) {
				return array();
	     }
             // INSERT, UPDATE, DELETE return true on success, false on failure
             if (!strncmp($strSql,"INSERT",6) || 
		!strncmp($strSql,"UPDATE",6) || 
		!strncmp($strSql,"DELETE",6)) {
				$varRowsAffectedOrXml = mysql_affected_rows();
				return $objResult;
	     }
	     if (is_resource($objResult)) {
		$intNumFields = mysql_num_fields($objResult);
		if ($intNumFields == 1) $strFieldName = mysql_field_name ( $objResult, 0 );
		if (mysql_num_rows($objResult) < 2) { // return 1-D array
		return mysql_fetch_assoc($objResult);
		} else {    // return 2-d array if more than one result 
		$arrReturn = array();
		if ($intNumFields > 1) {
		    while ($row = mysql_fetch_assoc($objResult)) $arrReturn[]=$row;  
		} elseif ($intNumFields == 1) {
		    while ($row = mysql_fetch_assoc($objResult)) $arrReturn[]=$row[$strFieldName];  
		}
		return $arrReturn;
		}
	    } elseif (is_bool($objResult) && $objResult === false) { // falls through to this case in case something is not caught at the top 
		error_log("Error in query: {$strSql} - ".mysql_error());
	    }
         }
         else {
            //  return $this->transformToXml(mysql_query($strSql));
	// Needed to add a variable to prevent PHP STRICT STANDARD complainig only variables can be passed by reference
	     $objResult = mysql_query($strSql);
             $strXml = $this->transformToXml($objResult, $varRowsAffectedOrXml );
             return $this->transformToHtml($strXml);
         }
      }

      //specific purpose function to add to ASTF if record not found
      public function addIfNotFound($strTable, $arrFieldNames, $arrValues) {
         return ("INSERT INTO ".$strTable." ("
                        .join(",",$arrFieldNames).") 
                        SELECT ".join(",",$arrValues).
                        " FROM DUAL WHERE NOT EXISTS 
                        (SELECT ID FROM ".$strTable." WHERE ".
                        $this->joinFieldValue($arrFieldNames,$arrValues).")");
      }

      //specific purpose function to add to ASTF table
      public function addAnyway($strTable, $arrFieldNames, $arrValues) {
         return ("INSERT INTO ".$strTable." ("
                        .join(",",$arrFieldNames).") 
                        VALUES (".join(",",$arrValues).
                        ")");
      }

      //function to call for entering new row or updating existing by indirect references
      // from a dependent table
      public function addUniqueRowOrUpdate($strTable, $arrFieldNames, $arrValues,$arrUpdateFields,$strUpdateSpecial="") {
         return ("INSERT INTO ".$strTable." ("
                        .join(",",$arrFieldNames).") 
                        SELECT ".join(",",$arrValues).
                        " ON DUPLICATE KEY UPDATE ".
                        $strUpdateSpecial.
                       $this->joinFieldValue($arrFieldNames,$arrUpdateFields,"VALUES",",") );
      }

      //specific purpose function to get a field in a table, given constraints
      public function getField($strTable, $strField, $arrFieldNames, $arrValues) {
         return ("SELECT ".$strField." FROM "
                        .$strTable." WHERE ". 
                        $this->joinFieldValue($arrFieldNames,$arrValues));
      }

      // helper function to string together
      // note if there are fewer values, then only those many fields will be returned
      private function joinFieldValue (&$arrFields, &$arrValues,$strFunction="",$strJoinType="AND") {
            $arrReturn=array();
            for ($i=0 ; $i < count($arrValues) ; $i++) {
               if (strlen($strFunction) >0) {
                   $arrReturn[]= $arrFields[$i]."=".$strFunction."(".$arrValues[$i].")";
               }

               else {
                   $arrReturn[]= $arrFields[$i]."=".$arrValues[$i];
               }
            }
            return join(" ".$strJoinType." ",$arrReturn);
      }

      //transformtoXML function 
      private function transformToXml(&$rst, &$arrSpecial) {
         if (is_resource($rst)) {
             $xml='';
             while ($stuff = mysql_fetch_assoc($rst)) {
                 $xml .= "<row id=\"{$stuff['ID']}\">";
                 foreach($stuff as $key=>$value) {
		   if ( array_key_exists($key, $arrSpecial) ) {
                     $arrSpecial[$key][] = xxhash32($value);
		   } else {
                     $value = htmlspecialchars($value);
                     $xml.="<{$key}>{$value}</{$key}>";
		   }
                 }
                 $xml .= "</row>";
              }  // end of while loop through all results
          return "<dataset>".$xml."</dataset>";
         }  
         elseif (is_bool($rst) || is_integer($rst)) {
         // echo mysql_error($this->hndDatabase);
          return "<dataset><row id=\"1\"><return>".$rst."</return></row></dataset>";
         }      
      }   // end of function transformToXml

       //transformtoXML function 
      private function transformToHtml(&$strXml) {
         // instantiate an xslt processor first
         $objXsltProcessor=new XsltProcessor();
         // then load the xslt stylesheet into the processor
         $objXsltDOM=new DomDocument;
         $objXsltDOM->load(dirname(__FILE__).'/ASTFdb.xslt');
         $objXsltProcessor->importStylesheet($objXsltDOM);
         // now load the Xml DOM document passed as string reference
         $objXmlDOM=new DomDocument;
         $objXmlDOM->loadXML($strXml);

         // now transform
         return $objXsltProcessor->transformToXml($objXmlDOM);
      }   // end of function transformToHtml

      //email invite function 
      public function getEmailInvites(&$arrTos, &$arrNames, &$arrInviteIDs, $strFilter="") {
      // First, manually run these 3 SQL's to mark the old inviteID's for repeat registrants and update the inviteID to the latest on (so we get latest email)
      // change the year to current year festival
      // 1. UPDATE `tblRegistrations` SET InviteID=CONCAT('Old_',InviteID) 
      //    WHERE ParticipantID IN (
      //        SELECT A.ParticipantID FROM (
      //            SELECT ParticipantID FROM tblRegistrations GROUP BY ParticipantID HAVING COUNT(ParticipantID) > 1) AS A)
      //                 AND SUBSTR(ID,1,4)!='2012' AND InviteID!='' AND SUBSTR(InviteID,1,4)!='Old_'
      //
      // 2. UPDATE `tblRegistrations` SET InviteID=SHA(ID) WHERE ParticipantID IN (
      //        SELECT A.ParticipantID FROM (
      //            SELECT ParticipantID FROM tblRegistrations GROUP BY ParticipantID HAVING COUNT(ParticipantID) > 1) AS A) 
      //                AND SUBSTR(ID,1,4)!='2012' AND InviteID=''
      //
      // 3. UPDATE`tblRegistrations` SET InviteID=SUBSTR(SHA(ID),1,10) WHERE SUSBSTR(ID,1,4)='2011' AND InviteID=''
      // after this manually empty out the InviteID if you don't want invites to go out to specific people, like people who already registered etc.
      // Check for any collisions in InviteID, however unlikely
      // SELECT * FROM tblRegistrations WHERE InviteID IN (SELECT InviteID FROM `tblRegistrations` GROUP BY InviteID HAVING COUNT(InviteID)>1)
         $rst = mysql_query( "SELECT 
                       tblRegistrations.InviteID AS ID,
                       tblParticipants.FirstName AS firstName,
                       tblEmails.Email AS email
                         FROM 
                          tblEmails INNER JOIN
                          (tblParticipants INNER JOIN
                           tblRegistrations
                           ON tblRegistrations.ParticipantID=tblParticipants.ID
                           )
                           ON tblRegistrations.EmailID=tblEmails.ID
                           WHERE NOT(tblEmails.Email='' OR tblEmails.Email='none' OR tblEmails.Email='no e-mail provided' 
                                OR tblRegistrations.InviteID = '' OR SUBSTR(tblRegistrations.InviteID,1,4)='Old_')  
                           ".$strFilter);
          while ($row = mysql_fetch_assoc($rst)) {
             $arrTos[]=$row['email'];
             $arrNames[]=$row['firstName'];
             $arrInviteIDs[]=$row['ID'];
          }
      }

      public function getDescription($strTableName) {

          $strSql = 'SHOW COLUMNS FROM '.($strTableName);
          return $this->runSQL($strSql, true);
      }
      public function getNumberOfConnections() {
        return $this->runSQL('SHOW PROCESSLIST', true);
      }
    public function getAll($arrFieldNames, $strTableName)
    {
        $strSql = 'SELECT '.implode(',', $arrFieldNames).' FROM '.($strTableName);
        return $this->runSQL($strSql, true);
    }

    // The clients of this class must call this before any INSERT / UPDATE queries
    public function setDataEntryPersonId($intPersonID)
    {
        $strSql = 'SET @DataEntryPersonID='.$intPersonID;
        return $this->runSQL($strSql, true);
    }

    public function isTestEnvironment()
    {
	return ($this->strEnvironment == "test");
    }

   } // class definition ends
//##############################################################################   
//This is the function to call to get full info, assuming procedural programming style
    function getTable($strYear, &$arrColumns) {
         $objDatabase = new ASTFdatabase();
         // the first SELECT subquery extracts the relevant registrant info
         // by an inner join, the rest is all SQL constructs to postprocess the result field values
         // the second SELECT subquery was to conveniently display payment history using GROUP_CONCAT
         $strConstraint1 = ' ';
         $strConstraint2 = ' ';
         //if ($strYear != 'all') {
        //    $strConstraint1 = " WHERE SUBSTR(tblRegistrations.ID,1,4)={$strYear} AND SUBSTR(tblRegistrations.ID,5,5)!='00199' "; 
        //    $strConstraint2 = " WHERE YEAR(tblMoneyActivities.ActivityDate)='{$strYear}' "; 
        //}
         echo $objDatabase->runSQL("SELECT A.TransactID AS ID, A.Name AS Name, A.Level AS Level,
                                      A.LeadFollow AS LeadFollow,A.SignedUpFor AS SignedUpFor,A.Email AS Email,A.Phonenumber as Phonenumber,
                                      tblAddresses.City AS City,tblAddresses.State AS State,tblAddresses.Zipcode AS Zipcode,
                                      A.DiscountCode AS DiscountCode,B.History AS History FROM 
                                      (
                                    (SELECT 
                                      tblParticipants.ID, 
                                      CONCAT(tblParticipants.LastName,', ',tblParticipants.FirstName,' ',tblParticipants.MiddleName) AS Name,
                                      tblRegistrations.Level AS Level,
                                      ELT(tblParticipants.LeadFollow,'Lead','Follow','Both?') AS LeadFollow,
                                      EXPORT_SET(tblRegistrations.EventChoices,'Y','N',',',10) AS SignedUpFor,
                                      tblRegistrations.DiscountCode AS DiscountCode,
                                      tblRegistrations.ID AS TransactID,
                                      tblRegistrations.AddressID AS AddressID,
                                      tblRegistrations.TelephoneNo AS Phonenumber,
                                      tblEmails.Email AS Email
                                     FROM 
                                      tblParticipants INNER JOIN
                                        tblRegistrations 
                                        ON tblRegistrations.ParticipantID=tblParticipants.ID
					INNER JOIN tblEmails ON tblRegistrations.EmailID = tblEmails.ID
                                        {$strConstraint1}
                                        ) AS A 
                                    INNER JOIN
                                    (SELECT 
                                      tblMoneyActivities.RegistrationID AS ID, 
                                      REPLACE(
                                        GROUP_CONCAT(
                                            CONCAT(
                                                DATE_FORMAT(tblMoneyActivities.ActivityDate,' %Y-%b-%d'),
                                                ' $',tblMoneyActivities.Amount,' ',
                                                tblMoneyActivityCodes.Description,' ',
                                                tblMoneyActivities.CheckOrConfirmationNo
                                                )
                                            ),
                                            ',','<br/>') AS History
                                     FROM 
                                      tblMoneyActivities INNER JOIN 
                                        tblMoneyActivityCodes ON tblMoneyActivityCodes.ID=tblMoneyActivities.ActivityType 
                                        GROUP BY ID) AS B
                                    ON B.ID=A.TransactID
                                    ) INNER JOIN tblAddresses 
                                    ON tblAddresses.ID = A.AddressID ORDER BY Name
                                    ", false, $arrColumns);
    }
//This is the function to call to get full info, assuming procedural programming style
    function getOnePerson($strPersonID, $strYear, &$arrAssoc) {
         $objDatabase = new ASTFdatabase();
         $strId = mysql_real_escape_string($strPersonID);
         $strYr = mysql_real_escape_string($strYear);
	 if ( (strlen($strId) == 9) && preg_match('/20[0-9]{7}/',$strId) ) {
	 	$strWhereClause = "tblRegistrations.ID='{$strId}'";
	 } else {
	 	$strWhereClause = "tblRegistrations.InviteID='{$strId}'";
	 }
         // the second SELECT subquery 
         $arrAssoc = $objDatabase->runSQL(
                                     "SELECT 
                                      tblParticipants.ID AS ParticipantID,
                                      tblRegistrations.InviteID AS InviteID, tblParticipants.LastName AS lastName,
                                      tblParticipants.FirstName AS firstName,
                                      tblParticipants.MiddleName AS midName,
                                      tblRegistrations.Level AS danceLvl,
                                      tblParticipants.LeadFollow AS lefol,
                                      tblAddresses.Address AS address,
                                      tblAddresses.City AS city,
                                      tblAddresses.State AS state,
                                      tblAddresses.Zipcode AS zip,
                                      tblEmails.Email AS email
                                     FROM 
                                      tblEmails INNER JOIN
                                      (tblAddresses INNER JOIN
                                      (tblParticipants INNER JOIN
                                       tblRegistrations
                                        ON tblRegistrations.ParticipantID=tblParticipants.ID
                                      )
                                      ON tblRegistrations.AddressID=tblAddresses.ID
                                      ) 
                                      ON tblRegistrations.EmailID=tblEmails.ID
                                     WHERE {$strWhereClause} 
                                    "
                                    , true);
    }

//This is the big function to write from form to database onSubmit in register.php
// The function probably is too long, but tries to handle adding data to
// multiple ISAM tables which are not capable of foreign keys, i.e. referential
// integrity is the responsibility of this piece of code. This is definitely
// not foolproof, in that there are multiple SQL statements, and if simultaneous
// multiple accesses are made by different registrants, their individual SQL
// statements might interleave in a way that compromises referential integrity.
// However,an attempt has been made to sequence the SQL statements in a way that 
// the chances of that are minimized. Also ISAM table-level locking is relied upon.
// The second parameter is a way to make this function aware of the total payment 
// from the calling script, i.e., register2.php, since it goes through elaborate
// machinations to come up with a total, in case of credit card, it includes 3% fee
// In the long run, the database has enough info to calculate the total independently
    function writeToDatabase($curTotal, $strYear, $strOption) {
         //print_r($_POST);
         $arrSql=array();
         $strEmail = "";
         $strAddress = "'".''."'";
         $strCity = "'".''."'";
         $strState = "'".''."'";
         $strZip = "'".''."'";
         $strParticipantID='';
         $strLevel = "'".''."'";
         $strFirstName = "'".''."'";
         $strMidName = "'".''."'";
         $strLastName = "'".''."'";
         $intLeadFollow = 0;
         $intActivityKey = 0;
         $strRegistrationId = '';
         $strDataEntryPersonId = '';
         $intDiscountCode = 0;
	 $intRowsAffected = 0;
         $intEventChoices = 0;
	 global $G_DISCOUNTCODE_THIS_WEEK;
         $objDatabase = new ASTFdatabase();
	 $arrReturn = array(	'RegistrationID' => '000000000',
				'IsNewParticipant' => false,
				'IsNewEmail' => false,
				'IsNewAddress' => false,
				'IsAbortedEventChoicesBad' => false,
				'BadChoices' => '',
				'IsNewRegistration' => false );

        // Need to set the DataEntryPersonID mysql session variable early on
        // This is no problem when the POST comes from the intranet form.
        // The volunteer who is entering data must also be in the database, his/her ID is used
        // When this comes from registration site, and this user doesn't have an ID in the database
        // the History table will use a different ID (maybe webbot ID) then switch over to the newly
        // acquired ID after the next step. Even if it is a repeat visitor they will not necessarily
        // have a set POST variable, although this can be facilitated when user submits the registration
        // and is subsequently taken to the page before heading to Paypal
        if (isset($_POST['DataEntryPersonID'])) {
            $strDataEntryPersonId = $_POST['DataEntryPersonID'];
            $objDatabase->setDataEntryPersonId($strDataEntryPersonId);
        } else {
            // In this case, the strDataEntryPersonId is not touched on purpose, it needs to be changed very soon
            $arrResults = $objDatabase->runSQL("SELECT ID FROM tblParticipants WHERE LastName='Bot' AND FirstName='Registration' AND LeadFollow=0",true);
            $objDatabase->setDataEntryPersonId($arrResults['ID']);
	}
        // this is where a check is made if participant is known (if not, added 1st)
        if (isset($_POST['ParticipantID'])) {
            $strRegistrationId = $strYear.str_pad($_POST['ParticipantID'],5,"0",STR_PAD_LEFT);
            $strParticipantID = mysql_real_escape_string($_POST['ParticipantID']);
            if ($strParticipantID=='') {
                $strFirstName = mysql_real_escape_string($_POST['firstName']);
                $strMidName = mysql_real_escape_string($_POST['midName']);
                $strLastName = mysql_real_escape_string($_POST['lastName']);
                $intLeadFollow = ($_POST['lefol']=='lead')?(1):(($_POST['lefol']=='follow')?2:0);
                //$arrFields = array( 'LastName','FirstName','MiddleName');
                $arrFields = array( 'LastName','FirstName','LeadFollow');  // as of 2013 match should be only on 1st and last name
                //$arrValues = array("'".$strLastName."'","'".$strFirstName."'","'".$strMidName."'");
                $arrValues = array("'".$strLastName."'","'".$strFirstName."'",$intLeadFollow);
		$intRowsAffected = 0;
                $objDatabase->runSQL( $objDatabase->addIfNotFound('tblParticipants',$arrFields, $arrValues), true, $intRowsAffected);
		$arrReturn['IsNewParticipant'] = ($intRowsAffected == 1);
                $arrResults = $objDatabase->runSQL(
                                ($objDatabase->getField('tblParticipants','ID',
                                $arrFields, $arrValues)),true);
                $strParticipantID = $arrResults['ID'];
            } 
	    // At this point even if the Participant ID was blank to begin with, they should have one.
	    // So check the DataEntryPersonID, it will be empty only if temporary bot ID is used, when participant registering
	    // himself or herself, the first entry will be recorded in the tblHistory table as a bot entry
	    if ($strDataEntryPersonId=='') {
                $strDataEntryPersonId = $strParticipantID;
                $objDatabase->setDataEntryPersonId($strDataEntryPersonId);
            }
         }

         // enter new emails in the table for emails
         if (isset($_POST['email']) && $_POST['email']!='') {
           // avoid SQL injection
             $strEmail = mysql_real_escape_string(strtolower($_POST['email']));
             $arrFields = array( 'Email');
             $arrValues = array("'".$strEmail."'");
             //$arrSql[] = $objDatabase->addIfNotFound('tblEmails',$arrFields, $arrValues);
             // The thought here is that a previous participant could come back  a following year
             // with a different email and/or address, so the new address is added unless 'edit' specified
             // From the normal registration page, we would add, from the data entry page, edit
	     $intRowsAffected = 0;
             if ($strOption == "edit") {
                if (strlen($strEmail) > 0) {    // only update if email is not a blank string, javascript already trims it
                    //$objDatabase->runSQL(
                    //                "UPDATE tblEmails SET Email ='".$strEmail.
                    //                "',ModifyDateTime='".strftime("%F %T")."', ParticipantIDModifiedBy =".$strDataEntryPersonId.
                    //                " WHERE ID =(SELECT EmailID FROM tblRegistrations WHERE ID='".$strRegistrationId."')");
                    $objDatabase->runSQL($objDatabase->addIfNotFound('tblEmails',$arrFields, $arrValues), true, $intRowsAffected);
                }
             } else if ($strOption == "add") {
                $objDatabase->runSQL($objDatabase->addIfNotFound('tblEmails',$arrFields, $arrValues), true, $intRowsAffected);
                //$arrSql[] = $objDatabase->addIfNotFound('tblEmails',$arrFields, $arrValues);
             }
         }
	$arrReturn['IsNewEmail'] = ($intRowsAffected == 1);

        // then enter new addresses in the table for addresses
        if (isset($_POST['city']) && isset($_POST['state']) && isset($_POST['address']) && isset($_POST['zip'])) {
            // avoid SQL injection
            $strAddress = mysql_real_escape_string(strtoupper($_POST['address']));
            $strCity = mysql_real_escape_string(strtoupper($_POST['city']));
            $strState = mysql_real_escape_string(strtoupper($_POST['state']));
            $strZip = mysql_real_escape_string($_POST['zip']);
            $arrFields = array( 'Address','City','State','Zipcode');
            $arrValues = array("'".$strAddress."'","'".$strCity."'","'".$strState."'","'".$strZip."'");
            //------------shady hacks, on second thoughts eliminated (PR) -------
            // at least zip code and one other field is supplied, make new row -better 
            // than last year where only zipcodes were entered in the table
            // if ( $strZip!='' && ($strAddress!='' || $strCity!='' || $strState!='') ) {
                 //$arrSql[] =$objDatabase->addAnyway('tblAddresses',$arrFields, $arrValues);
             //    $objDatabase->runSQL($objDatabase->addAnyway('tblAddresses',$arrFields, $arrValues));
             //}
             // if only zipcode entered no point making another row if it is already there
             //elseif ( $strZip!='' && $strAddress=='' && $strCity=='' && $strState=='' ) {
                 //$arrSql[]= $objDatabase->addIfNotFound('tblAddresses',$arrFields, $arrValues);
           //------------end of shady hacks, on second thoughts eliminated (PR) -------
            if ($strOption == "edit") {
                $strAddressSetter = '';    // the address field might not be set, from the data entry form, for example, don't want to overwrite with empty string
                if (strlen($strAddress) > 0) $strAddressSetter = "Address ='{$strAddress}',";
                $objDatabase->runSQL(
                                    "UPDATE tblAddresses SET {$strAddressSetter} City ='{$strCity}', 
                                     State ='{$strState}', Zipcode ='{$strZip}',
                                     ModifyDateTime='".strftime("%F %T").
                                     "' WHERE ID =(SELECT AddressID FROM tblRegistrations WHERE ID='{$strRegistrationId}')");
             } else if ($strOption == "add") {
	    	$intRowsAffected = 0;
                $objDatabase->runSQL($objDatabase->addIfNotFound('tblAddresses',$arrFields, $arrValues));
	    	$arrReturn['IsNewAddress'] = ($intRowsAffected == 1);
             }
             //}

         }

         // participant already in the database by this point, i.e, has an ID in the database 
         // now the registration details are captured in table of registrations.
         // each participant gets only 1 registration per year, but under that unique
         // registration ID, they can have multiple transactions
         if (isset($_POST['ParticipantID'])) {

             $arrEventCodes=array("Fri 8:00-9:30 PM","Sat 11:00-12:30 PM","Sat 1:00-2:30 PM", "Sun 1:00-2:30 PM","Sun 3:00-4:30 PM","Sun 5:00-6:30 PM","Friday","Saturday","Sunday","Saturday5050");
             $arrSignUp = array();
             $intEventChoices = 0;
             if (isset($_POST['classes'])) $arrSignUp=array_merge($arrSignUp,$_POST['classes']);
             if (isset($_POST['milongas'])) $arrSignUp=array_merge($arrSignUp,$_POST['milongas']);
             foreach($arrSignUp as $strEntry)
                   {$intEventChoices += 1 << array_search($strEntry,$arrEventCodes);}
             if (!($_POST['isStudent']) && isset($_POST['groupDiscount'])) {   
                 $strGroupDiscount = mysql_real_escape_string($_POST['groupDiscount']);
		 // In the DB interface this is a checkbox, so the value attribute is returned, check for that first
		 if ($strGroupDiscount == "true") {
			$intDiscountCode = 2;
		 } else {
                 	$intDiscountCode = (strtoupper($strGroupDiscount) == $G_DISCOUNTCODE_THIS_WEEK)?2:0;
		 }
             }
             if (isset($_POST['isStudent'])) $intDiscountCode   
                                                 +=($_POST['isStudent']==true)?1:0;
             if (isset($_POST['hostDiscount'])) $intDiscountCode   
                                                 +=($_POST['hostDiscount']==true)?8:0;
             if (isset($_POST['fullAward'])) $intDiscountCode = 4;
             //$strUniqueID ="CONCAT(YEAR(DATE_ADD(NOW(), INTERVAL 2 HOUR)),LPAD(".$strParticipantID.",5,'0'))";
             $strUniqueID = $strYear.str_pad($strParticipantID,5,"0",STR_PAD_LEFT);
             if (isset($_POST['danceLvl'])) {
                $strLevel = "'".strtoupper(substr($_POST['danceLvl'],0,1))."'";
             }
             else {  // some people leave this one out
                //$strLevel ='NULL';
		// 2015 - In case they registered before set it to the last choice. 
                $strLevel = '(SELECT Y.Level FROM (SELECT Level FROM tblRegistrations WHERE ParticipantID='.$strParticipantID.' ORDER BY ID DESC LIMIT 1) AS Y)';
             }
             
	    $arrReturn['RegistrationID'] = $strUniqueID;

	    // We abort the tblRegistration update in case one of the selected items for this participant is already in the database
	    $intPriorChoices = 0;
            $arrResults = $objDatabase->runSQL("SELECT EventChoices FROM tblRegistrations WHERE ID = '{$strUniqueID}'", true);     
            $arrResults1 = $objDatabase->runSQL("SELECT ID FROM tblMoneyActivities WHERE RegistrationID = '{$strUniqueID}' AND ActivityType IN (4,5,7,8,9)", true);     
            $intPriorChoices = $arrResults['EventChoices'];
	    // XNOR shows 1 where there is a match (i.e. conflict) 0 where there isn't; 
	    // mask (AND) by the selections because we only care about conflicts for the selected items. The whole number should be zero if no conflicts exist
            $intBadChoices = (~((int)$intPriorChoices ^ (int)$intEventChoices)) & (int)$intEventChoices;
	    // the logic is not precise - if overlapping items chosen, and there is at least one Paypal confirmed or cheque cleared
	    if (($intBadChoices > 0) && !empty($arrResults1) && $strOption == "add") {
		$arrReturn['IsAbortedEventChoicesBad'] = true;
		$arrBadChoices = str_split(str_pad( decbin( (int) $intBadChoices), count($arrEventCodes), '0', STR_PAD_LEFT));
		$arrReturn['BadChoices'] = implode("<br/>",array_keys(array_filter(array_combine($arrEventCodes, $arrBadChoices), function($value) { return ($value == 1) ;})));
		return $arrReturn;
	    }

            $intTelephoneNo = (preg_match('/\d{10}/', $_POST['phonenumber'])) ? (int)($_POST['phonenumber']) : 'NULL';
             // Didn't need 2 arrays really, associative arrays are meant for this kind of stuff
            $arrUpdateFields = array( 'ParticipantID','Level',  'DiscountCode', 'EmailID', 'AddressID','TelephoneNo');
            $arrUpdateValues = array($strParticipantID,$strLevel,$intDiscountCode,
                            '(SELECT ID FROM tblEmails WHERE Email=\''.$strEmail.'\')',
                            '(SELECT ID FROM tblAddresses WHERE Address =\''.$strAddress.'\' AND City=\''.$strCity.'\' AND State=\''.$strState.'\' AND Zipcode=\''.$strZip.'\' LIMIT 1)',$intTelephoneNo);
            if ($strOption == "edit") {
                if (strlen($strAddress) == 0) { // the assumption is, address field if blank, surely wouldn't want to alter city, state, zip, right??, ( --so drop AddressID field--)
                    // ++instead of dropping it find the previously entered address field and sync up++
                    $strPosition = array_search('AddressID',$arrUpdateFields);
                    //array_splice($arrUpdateFields, $strPosition, 1);
                    //array_splice($arrUpdateValues, $strPosition, 1);
                    // MYSQL does not let SELECT and UPDATE happen on the same table, so the nested SELECT is a workaround to create a temporary in-memory table
                    $arrUpdateValues[$strPosition] = '( SELECT X.AddressID FROM (SELECT AddressID FROM tblRegistrations WHERE ParticipantID='.$strParticipantID.' ORDER BY ID DESC LIMIT 1) AS X)';
                }            
                if (strlen($strEmail) == 0) { // if e-mail left blank, we (-- don't want to update that field--) ++ pull it from previous registration
                    $strPosition = array_search('EmailID',$arrUpdateFields);
                    //array_splice($arrUpdateFields, $strPosition, 1);
                    //array_splice($arrUpdateValues, $strPosition, 1);
                    $arrUpdateValues[$strPosition] = '(SELECT Y.EmailID FROM (SELECT EmailID FROM tblRegistrations WHERE ParticipantID='.$strParticipantID.' ORDER BY ID DESC LIMIT 1) AS Y)';
                }            
            } else if ($strOption == "add") { // no code here
            }
            $arrFields = array_merge($arrUpdateFields,array('EventChoices','ID')); // add the ID element for updates/new row, if there is ID match, ensures only 1 row per year per participant
                        $arrValues = array_merge($arrUpdateValues,array("{$intEventChoices}","'{$strUniqueID}'")); // add the ID value
	    // In case of event choices, we want volunteers to be able to change the entire field without regard to what was there before,
	    // However, for participants from the registration page, they should be able to add classes by visiting the page a 2nd time
	    // 2015-01-18 This continues to be a sticking point, when registrants add an item in their first visit and then try to make a different
	    // selection, the old selection(s) still stick, so use the fact that they have not paid yet, to decide whether add or edit mode
	    if ( preg_match("/register.php/i", $_SERVER["HTTP_HOST"]) ) {
		if (empty($arrResults1)) {
			$strUpdateSpecial = "EventChoices = VALUES(EventChoices),";
		} else {
                        $strUpdateSpecial = "EventChoices = EventChoices | {$intEventChoices},";
		}
	    } else if ($strOption == "add") {
                        $strUpdateSpecial = "EventChoices = EventChoices | {$intEventChoices},";
	    } else  if ($strOption == "edit") {
			$strUpdateSpecial = "EventChoices = VALUES(EventChoices),";
	    } 

            //$arrSql[] = $objDatabase->addUniqueRowOrUpdate( 'tblRegistrations',$arrFields, $arrValues,$arrUpdateFields,$strUpdateSpecial);
	    $intRowsAffected = 0;
            if ($objDatabase->isTestEnvironment()) error_log( print_r($_POST,true)); 
            if ($objDatabase->isTestEnvironment()) error_log(  $objDatabase->addUniqueRowOrUpdate( 'tblRegistrations',$arrFields, $arrValues,$arrUpdateFields,$strUpdateSpecial) );
            $objDatabase->runSQL( $objDatabase->addUniqueRowOrUpdate( 'tblRegistrations',$arrFields, $arrValues,$arrUpdateFields,$strUpdateSpecial), true, $intRowsAffected);
	    $arrReturn['IsNewRegistration'] = ($intRowsAffected == 1);  // if the row is updated this returns 2 as per MYSQL documentation
	    if ($curTotal != 0) {
		     // and now to record the money details in MoneyActivities table
		     // 1st declare an associative array that knows the different kinds 
		     // transactions. These should ideally come from the database, but the
		     // the descriptive text used in the database does not match the POST
		     // values from register.php, and only 2 and 3 are recognized in register.php
		     // 4 and 5 added in late 2011, the rest are from the database as is, hence this mish-mash.
		     if (isset($_POST['payment'])) {
			 $arrActivityTypes = array(1=>'cash', 2=>'check', 3=>'card', 4=>'cashatdoor',
					      5=>'chequeatdoor', 6=>'paypalatdoor', 7=>'Cheque received',
					      8=>'Cheque cleared', 9=>'Paypal confirmed', 10=>'Cancelled',
					      11=>'cashrefunddoor', 12=>'chequerefundmailed', 13=>'paypalrefundstart');
			 $intActivityKey = array_search($_POST['payment'],$arrActivityTypes);
		     } else {    // registration page lacks cash payment button, for compatibility so does the data entry form, assume cash if nothing selected
			$intActivityKey = 1;
		     }
		     // for any type of activity, there might be a negative sign in front we need to strip off
		     if ( $curTotal < 0 ) {
			$curAmount = -$curTotal;
		     } else {
			$curAmount = $curTotal;
		     }
		     $curFee = 0.00;
		     if ($intActivityKey == 3) {
			// Paypal uses some inscrutable math, but there is a $0.30 per transaction in addition to  the 3%, it turns out
			 //$curAmount = round(($curAmount - 0.30) / 1.0299,2);
			 $curAmount = round($curAmount / 1.03,2);
			 $curFee = $curTotal - $curAmount;
		     }
		    $strDate = getInferredDate(isset($_POST['yearregn'])?$_POST['yearregn']:'',
						isset($_POST['monthregn'])?$_POST['monthregn']:'' ,
						isset($_POST['dayregn'])?$_POST['dayregn']:'') ;
		    $strConfirmationNo = (isset($_POST['ChequeOrConfirmationNo'])) ? ($_POST['ChequeOrConfirmationNo']) : ('');
		    $arrFields = array( 'RegistrationID','ActivityDate', 'ActivityType', 'EventChoices', 'Amount', 'ExtraFee','CheckOrConfirmationNo');
		    $arrValues = array("'{$strUniqueID}'",$strDate,$intActivityKey,$intEventChoices,$curAmount,$curFee,"'{$strConfirmationNo}'" );
		    //$arrSql[] = $objDatabase->addAnyway( 'tblMoneyActivities',$arrFields, $arrValues);
		    //if ($strOption == "add") {
			//echo "<span>".$objDatabase->addAnyway( 'tblMoneyActivities',$arrFields, $arrValues)."</span>";
			$objDatabase->runSQL( $objDatabase->addIfNotFound( 'tblMoneyActivities',$arrFields, $arrValues));
	    }
            //} elseif ($strOption == "edit") {
		/*
                $objDatabase->runSQL(
                                    "UPDATE tblMoneyActivities SET ActivityDate ={$strDate}, 
                                     ActivityType ={$intActivityKey}, Amount ={$curAmount},ExtraFee={$curFee}, CheckOrConfirmationNo ='{$strConfirmationNo}'
                                     WHERE ID =(SELECT X.ID FROM (SELECT ID FROM tblMoneyActivities WHERE RegistrationID='{$strRegistrationId}'
                                     ORDER BY ActivityDate DESC LIMIT 1) AS X)"
                                     );
           	*/ 
            //}
       }
         
        //echo "Success!"; 
         //echo (join(";<HR/>",$arrSql));
         //$objDatabase->runSQL(join(";",$arrSql));
        return $arrReturn;
    }

    function sendInvites() {
         // use this function itself to generate the body of an HTML page with a form that can be used as a mass-mailer of sorts
         echo "<html><head><title>Mailing centre</title></head><body>";
         if (!isset($_POST['postData']) ) {
            echo '<form action="" method="post" name="frmMailer" id="frmMailer">
            <input type="hidden" name="postData" id="postData" value="" /><br />
            <label for="filterBy">Filter:</label> <input type="text" name="filterBy" id="filterBy" size="80" value="" /><br />
            <label for="emailText">Email text:</label> <textarea name="emailText" cols="60" rows="3" id="emailText"></textarea><br />
            <label for="prependText">Prepend text:</label> <input type="checkbox" name="prependText" value="prependText" id="prependText" /><br />             
            <label for="reallySend">Really send:</label> <input type="checkbox" name="reallySend" value="reallySend" id="reallySend" /><br />             
            <input name="fireOff" type="submit" value="SUBMIT" id="fireOff" /></form>';             
         } else {
             $arrTos=array();
             $arrInviteIDs=array();
             $arrNames=array();
             //$strMessage = "It's that time of the year again. Spring's around the corner and so is the Austin Spring Tango Festival. 
            //    More importantly, the early bird deadline is 4 days away! This year we are trying (hope it works) to save you 2010 participants some typing - 
            //    if you click on the link at the bottom of this e-mail it should take you to your special registration form - please fill in the missing details 
            //    (we missed the address, city, state last time), and you'll be on your way. And if the link lost it's clicky-ness on the way to your mailbox, 
            //    you might have to cut and paste the entire thing on to your browser's address bar ...  see you March 25th!"; 
             $strMessage = "Take advantage of the Early bird Registration (up until March 1st)!
                Mario Consiglieri y Anabella Diaz-Hojman, Florencia Taccetti y Evan Griffiths, Claudia Codega y Esteban Moreno 
                will be with us at the Austin Spring Tango Festival 2012. We are very lucky to have them, you can be lucky too!!
                You have previously attended this festival, so we have a special link just for you to save some typing; click the link below and reach the registration page" ;
             $strLink1 = '<br/><a href="http://austinspringtango.com/register.php?id=';
             $strLink2 = '">http://austinspringtango.com/register.php?id=';
             $strLink3 = '</a>';
             $strHeaders = "MIME-Version: 1.0\r\n";
             $strHeaders .= "Content-type: text/html; charset=iso-8850-1\r\n";
             $strHeaders .='From: webmaster@tangoteacherexchange.com' . "\r\n";
             $strHeaders .='Reply-To: margvance@aol.com' . "\r\n";
             //$strHeaders .='X-Mailer: PHP/'.phpversion();
             if ( strlen(trim($_POST['emailText'])) > 0 ) {
                if (  isset($_POST['prependText']) ) {
                    $strMessage = $_POST['emailText']."\n<br/><br/>".$strMessage;
                } else {
                    $strMessage = $_POST['emailText'];
                }
             }
             $strFilterSql = "";
             if ( strlen(trim($_POST['filterBy'])) > 0 ) {
                $varPos =  strpos($_POST['filterBy'], "AND ") ;
                if ( $varPos === false  ) {
                    $strFilterSql = " AND ".$_POST['filterBy'];
                } else {
                    if ($varPos == 0) {
                        $strFilterSql = " ".$_POST['filterBy'];
                    }
                }
             }
             $objDatabase = new ASTFdatabase();
             // the e-mail query results
             $objDatabase->getEmailInvites( $arrTos,$arrNames,$arrInviteIDs, $strFilterSql);
             echo $strHeaders."<br/><br/>".$strMessage."<br/><table rules=\"cols\">";
             for ($i=0; $i < count($arrTos); $i++) {
             //for ($i=199; $i < count($arrTos); $i++) {
               echo "<tr bgcolor=\"#CCCCCC\"><td>{$i}</td><td>{$arrTos[$i]}</td><td>{$arrNames[$i]}</td><td>{$strLink1}{$arrInviteIDs[$i]}{$strLink2}{$arrInviteIDs[$i]}{$strLink3}</td><td>";
               $strMessage1 = "Hello ".$arrNames[$i].",<br/>".$strMessage.$strLink1.$arrInviteIDs[$i].$strLink2.$arrInviteIDs[$i].$strLink3."\n\n";
               if (isset($_POST['reallySend']) ) {
                    echo mail($arrTos[$i],'Austin Spring Tango 2012',$strMessage1,$strHeaders,"-fwebmaster@tangoteacherexchange.com");
                    sleep(1);
                }
                echo "</td></tr>";
             }
              echo "<tr><td colspan=\"4\">$i total</td></tr></table>";
         } // end of else block for check isset postdata 
         echo "</body></html>";
    }

    function addTransaction() {
        $objDatabase = new ASTFdatabase();
        $strSaneRegistrationID = mysql_real_escape_string($_POST['RegistrationID']);
        $strSaneActivityType = mysql_real_escape_string($_POST['ActivityType']);
        $strSaneAmount = mysql_real_escape_string($_POST['Amount']);
        $strSaneXactionID = mysql_real_escape_string($_POST['CheckOrConfirmationNo']);
        //if (strlen(trim($strSaneXactionID)) == 0) $strSaneXactionID = "''";
        $strFee = 0.00;
        $strDate = getInferredDate(isset($_POST['yearxact'])? $_POST['yearxact']:'',
                                    isset($_POST['monthxact'])? $_POST['monthxact']:'',
                                    isset($_POST['dayxact'])? $_POST['dayxact']:'') ;
        if (isset($_POST['DataEntryPersonID1'])) {
            $strDataEntryPersonId = $_POST['DataEntryPersonID1'];
            $objDatabase->setDataEntryPersonId($strDataEntryPersonId);
        }
        // for Paypal a fee must be calculated from the amount
        if (($strSaneActivityType == 3 || $strSaneActivityType == 9) && is_numeric($strSaneAmount)) {
            $strFee = round(($strSaneAmount * 0.03),2); // 3% fee is charged, round off to 2 digits
            //$strSaneAmount = $strSaneAmount - $strFee; 
        }
        $arrFields = array( 'RegistrationID','ActivityDate', 'ActivityType', 'Amount','ExtraFee','CheckOrConfirmationNo');
        $arrValues = array($strSaneRegistrationID,$strDate,$strSaneActivityType,$strSaneAmount,$strFee,"'".$strSaneXactionID."'" );
	$intRowsAffected = 0;
        $objDatabase->runSQL( $objDatabase->addIfNotFound( 'tblMoneyActivities',$arrFields, $arrValues), true, $intRowsAffected);
	return $intRowsAffected;
        //echo "<span>".$objDatabase->addAnyway( 'tblMoneyActivities',$arrFields, $arrValues)."</span>";
        //echo "Success!";
     }

    function findTransactionById($strIdentifyingFieldName, $strIdentifyingFieldValue,&$arrAssoc) {
        $objDatabase = new ASTFdatabase();
        $strSaneFieldName = mysql_real_escape_string($strIdentifyingFieldName);
        $strSaneFieldValue = mysql_real_escape_string($strIdentifyingFieldValue);
        $strSql = "SELECT RegistrationID AS RegID, (Amount + ExtraFee) AS Total, CheckOrConfirmationNo AS TransactionID
                                            FROM tblMoneyActivities WHERE ".$strSaneFieldName."='".$strSaneFieldValue."'
                                             ORDER BY ActivityDate DESC LIMIT 1";
        $arrAssoc = $objDatabase->runSQL( "SELECT RegistrationID AS RegID, (Amount + ExtraFee) AS Total, CheckOrConfirmationNo AS TransactionID
                                            FROM tblMoneyActivities WHERE ".$strSaneFieldName."='".$strSaneFieldValue."'
                                             ORDER BY ActivityDate DESC LIMIT 1",true);
        return $strSql;
        //echo "Success!";
     }

    function addIpnConfirmation($strRegistrationID, $strGrossAmount, $strTransactionID, $strTransactionType) {
        $objDatabase = new ASTFdatabase();
        // This is paypal instant payment notification robot id
        $arrResults = $objDatabase->runSQL("SELECT ID FROM tblParticipants WHERE LastName='Pal' AND FirstName='Pay' AND LeadFollow=1",true);
	$objDatabase->setDataEntryPersonId($arrResults['ID']);
        $strSaneRegistrationID = mysql_real_escape_string($strRegistrationID);
        $strSaneAmount = mysql_real_escape_string($strGrossAmount);
        $strSaneXactionID = mysql_real_escape_string($strTransactionID);
        $strTransactionCode = 0;
        if ( $strTransactionType == 'Completed') {
            $strTransactionCode = 9;
        } else if ( $strTransactionType == 'Refunded') {
            $strTransactionCode = 15;        
        }
        $strFee = 0.00;
        if (is_numeric($strSaneAmount)) {    //always enclose if logical expression in parens
            if ($strTransactionType == 'Completed') {
                $strFee = round(($strSaneAmount * 0.03)/1.03,2); // 3% fee is charged, round off to 2 digits
                $strSaneAmount = $strSaneAmount - $strFee;
            } else if ( $strTransactionType == 'Refunded') {
                $strSaneAmount = abs($strSaneAmount);
            }
        }
        $arrFields = array( 'RegistrationID','ActivityDate', 'ActivityType', 'Amount','ExtraFee','CheckOrConfirmationNo');
        $arrValues = array("'".$strSaneRegistrationID."'","'".strftime("%F %T")."'",$strTransactionCode,$strSaneAmount,$strFee,"'".$strSaneXactionID ."'");
        $objDatabase->runSQL( $objDatabase->addIfNotFound( 'tblMoneyActivities',$arrFields, $arrValues));
        //echo "Success!";
        return $objDatabase->addAnyway( 'tblMoneyActivities',$arrFields, $arrValues);
     }

    function getInferredDate($strYear,$strMonth,$strDay) {
        if (strlen($strYear)==0 && strlen($strMonth)==0 && strlen($strDay)==0) { // if everything is blank, assume todays date
            return "'".strftime("%F %T")."'";
        } else {
            $arrDays = array(31,28,31,30,31,30,31,31,30,31,30,31);
            $strMaybeMonth =(strlen($strMonth)==0 || $strMonth > 12)? 2:$strMonth;        
            $strMaybeYear = (strlen($strYear)==0)?date('Y'):$strYear;
            if (($strMaybeYear % 4) == 0)  $arrDays[1] = 29;
            // xform phoney month to Feb
            $strMaybeDay = (strlen($strDay)==0 || $strDay > $arrDays[$strMaybeMonth-1])? ((int)$arrDays[$strMaybeMonth-1]/2):$strDay;
            $strDate = "'".strftime("%F %T", strtotime("$strMaybeYear-$strMaybeMonth-$strMaybeDay 11:59 AM"))."'";
            return $strDate;
        }
    }
    
    function getReport($strYear,$intType=1) {
        $objDatabase = new ASTFdatabase();
        if ($intType == 1) {
        echo $objDatabase->runSQL("SELECT tblAddresses.ID AS ID,  
                       CONCAT(tblAddresses.State, ',', tblAddresses.City) AS City, COUNT(*) AS COUNT  
                        FROM tblRegistrations 
                             INNER JOIN
                             tblAddresses ON 
                              tblAddresses.ID=tblRegistrations.AddressID
                           WHERE SUBSTR(tblRegistrations.ID,1,4)='2011' GROUP BY City");
         }
        elseif ($intType == 2) {
        echo $objDatabase->runSQL("SELECT A.ActivityTime AS TimePoint,COUNT(*) AS COUNT   
                        FROM (SELECT DATE_FORMAT(ActivityDate, '%Y %b-%d %a') AS ActivityTime 
                             FROM tblMoneyActivities WHERE SUBSTR(RegistrationID,1,4)='2011'
                             ORDER BY ActivityDate) AS A GROUP BY TimePoint"); 

        }
     }
    
    function getEmptyCityStates(&$arrReturn) {
        $objDatabase = new ASTFdatabase();
        $arrReturn = $objDatabase->runSQL("SELECT Zipcode, City, State  FROM tblAddresses WHERE CHAR_LENGTH(City)=0 AND CHAR_LENGTH(State)=0 AND CHAR_LENGTH(ZipCode)>0",true);
    }

    function fillEmptyCityStates($arrInput) {
        $objDatabase = new ASTFdatabase();
        for ($intRow=0; $intRow<count($arrInput); $intRow++) {
            $strCity = $arrInput[$intRow]['City'];
            $strState = $arrInput[$intRow]['State'];
            $strZip = $arrInput[$intRow]['Zipcode'];
            $objDatabase->runSQL("UPDATE tblAddresses SET City='{$strCity}', State='{$strState}' WHERE ZipCode='{$strZip}'",true);
        }
    }

    function getTeachersClasses($dtmFestStartDate,&$arrTeachers,&$arrTimeSlots,&$arrClasses) {
        $objDatabase = new ASTFdatabase();
        $strMySqlTime = strftime("%F %T",$dtmFestStartDate);
        //$strSql = "SELECT DISTINCT Teachers FROM tblEvents WHERE Teachers LIKE '% y %' AND StartTime > '".strftime("%F %T",$dtmFestStartDate)."'";
        $arrTeachers = $objDatabase->runSQL("SELECT DISTINCT Teachers FROM tblEvents WHERE Teachers LIKE '% y %' AND Level <> 'P' 
                                                                                        AND StartTime > '".$strMySqlTime."' ORDER BY Teachers",true);
        $arrTimeSlots = $objDatabase->runSQL("SELECT DISTINCT StartTime FROM tblEvents WHERE Teachers LIKE '% y %' AND Level <> 'P' 
                                                                                        AND StartTime > '".$strMySqlTime."' ORDER BY StartTime",true);
	if (!$arrTimeSlots) return "";
        foreach ($arrTimeSlots as $strTimeSlot) {
            $strTimeSlot1 = strftime("%a %I:%M %p", strtotime($strTimeSlot));
            $arrClasses[$strTimeSlot1] = $objDatabase->runSQL("SELECT CONCAT('(', SUBSTRING(tblAddresses.Address,6,1), ')', tblEvents.Level,', ',tblEvents.Description) FROM tblEvents 
                                                                INNER JOIN tblAddresses ON tblAddresses.ID=tblEvents.Venue 
                                                                                        WHERE tblEvents.Teachers LIKE '% y %' AND Level <> 'P' 
                                                                                        AND tblEvents.StartTime = '".$strTimeSlot."' ORDER BY tblEvents.Teachers",true);
        }
        return $strTimeSlot1;
        }

        function getParticipantIdFromWikiId($intWikiId) {
        $objDatabase = new ASTFdatabase();
        $arrResults = $objDatabase->runSQL(
                                   ($objDatabase->getField('tblMapWikiIds','ParticipantID',
                                   array('ID'), array($intWikiId))),true);
        return $arrResults['ParticipantID'];
        }

        function getCityFromIpv4($intIpv4Address) {
        $objDatabase = new ASTFdatabase();
        return $objDatabase->runSQL("SELECT iso2 AS geobytesinternet, regioncode AS geobytescode, city AS geobytescity FROM tblIpAddresses WHERE IPv4={$intIpv4Address}", true);
        }

        function storeIpv4($arrValues) {
        $objDatabase = new ASTFdatabase();
	$strSql = "INSERT INTO tblIpAddresses (IPv4, iso2, regioncode, city) VALUES (".join(",",$arrValues);
	$strSql .= ") ON DUPLICATE KEY UPDATE iso2=VALUES(iso2), regioncode=VALUES(regioncode), city=VALUES(city), visits=visits+1"; 
        return $objDatabase->runSQL($strSql, true);
	}

    function addPrivate() {
	$objDatabase = new ASTFdatabase();
        $strSaneRegistrationID = mysql_real_escape_string($_POST['RegistrationID']);
        $strSaneActivityType = 16;
        $strSaneAmount = mysql_real_escape_string($_POST['Amount']);
        $strSaneXactionID = $_POST['teacher']."_".(mysql_real_escape_string($_POST['CheckOrConfirmationNo']));
        //if (strlen(trim($strSaneXactionID)) == 0) $strSaneXactionID = "''";
        $strFee = 0.00;
        $strDate = getInferredDate(isset($_POST['yearxact'])? $_POST['yearxact']:'',
                                    isset($_POST['monthxact'])? $_POST['monthxact']:'',
                                    isset($_POST['dayxact'])? $_POST['dayxact']:'') ;
        if (isset($_POST['DataEntryPersonID1'])) {
            $strDataEntryPersonId = $_POST['DataEntryPersonID1'];
            $objDatabase->setDataEntryPersonId($strDataEntryPersonId);
        }
        // for Paypal a fee must be calculated from the amount
        if (($strSaneActivityType == 3 || $strSaneActivityType == 9) && is_numeric($strSaneAmount)) {
            $strFee = round(($strSaneAmount * 0.03),2); // 3% fee is charged, round off to 2 digits
            //$strSaneAmount = $strSaneAmount - $strFee; 
        }
        $arrFields = array( 'RegistrationID','ActivityDate', 'ActivityType', 'Amount','ExtraFee','CheckOrConfirmationNo');
        $arrValues = array($strSaneRegistrationID,$strDate,$strSaneActivityType,$strSaneAmount,$strFee,"'".$strSaneXactionID."'" );
	$intRowsAffected = 0;
        $objDatabase->runSQL( $objDatabase->addIfNotFound( 'tblMoneyActivities',$arrFields, $arrValues), true, $intRowsAffected);
	return $intRowsAffected;
 print_r($_POST);
    }

    function getTeachersPrivates($dtmFestStartDate,&$arrTimeSlots,&$arrPrivates) {
        $objDatabase = new ASTFdatabase();
        $strMySqlTime = strftime("%F %T",$dtmFestStartDate);
        //$strSql = "SELECT DISTINCT Teachers FROM tblEvents WHERE Teachers LIKE '% y %' AND StartTime > '".strftime("%F %T",$dtmFestStartDate)."'";
        $arrTimeSlots = $objDatabase->runSQL("SELECT DISTINCT StartTime FROM tblEvents WHERE Level = 'P' 
                                                                                        AND StartTime > '".$strMySqlTime."' ORDER BY StartTime",true);
	if (!$arrTimeSlots) return "";
        foreach ($arrTimeSlots as $strTimeSlot) {
            $strTimeSlot1 = strftime("%a %I:%M %p", strtotime($strTimeSlot));
            $arrPrivates[$strTimeSlot1] = $objDatabase->runSQL("SELECT CONCAT(tblAddresses.Address, ', ',tblEvents.Level,', ',tblEvents.Description) FROM tblEvents 
                                                                INNER JOIN tblAddresses ON tblAddresses.ID=tblEvents.Venue 
                                                                                        WHERE Level = 'P' 
                                                                                        AND tblEvents.StartTime = '".$strTimeSlot."' ORDER BY tblEvents.Teachers",true);
        }
        return $strTimeSlot1;
	}
        
    function getPrivateRequests($strYear) {
        $objDatabase = new ASTFdatabase();
	return $objDatabase->runSQL("SELECT tblRegistrations.ID AS ID, 
						CONCAT(tblParticipants.firstName,' ',tblParticipants.lastName) AS Name, 
						tblEmails.Email AS Email,
						TXN.Phone AS Phone, 
						TXN.Teacher AS Teacher
					FROM (SELECT RegistrationID, 
						SUBSTR(CheckOrConfirmationNo, -10) AS Phone, 
						SUBSTR(CheckOrConfirmationNo,1, INSTR(CheckOrConfirmationNo,'_')-1 ) AS Teacher
					FROM tblMoneyActivities 
					WHERE ActivityType = 16 AND RegistrationID LIKE '{$strYear}%' 
					ORDER BY Teacher ) AS TXN
					INNER JOIN tblRegistrations ON tblRegistrations.ID = TXN.RegistrationID 
					INNER JOIN tblParticipants ON tblRegistrations.ParticipantID = tblParticipants.ID 
					LEFT JOIN tblEmails ON tblRegistrations.EmailID = tblEmails.ID");
	}

    ?>
