<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="html"/>
<xsl:strip-space elements="*" />
<xsl:template match="Name">
	<td><xsl:value-of select="."/></td>
</xsl:template>
<xsl:template match="Level | LeadFollow | City | State | Zipcode | DiscountCode | Email | Phone | Teacher">
	<td><xsl:value-of select="."/></td>
</xsl:template>
<xsl:template match="ID">
	<td>
            <xsl:attribute name="title">
		<xsl:value-of select="."/>
            </xsl:attribute>
	<xsl:value-of select="substring(.,5,5)"/></td>
</xsl:template>
<xsl:template match="Checkin">
	<td onclick="return doCheckin(this);">
		<xsl:value-of select="." />
	</td>
</xsl:template>
<xsl:template match="History">
	<td onclick="return toggleOverlaidForm(this,2);">
		<xsl:value-of select="." disable-output-escaping="yes"/>
	</td>
</xsl:template>
<xsl:template match="SignedUpFor">
        <td>
            <xsl:attribute name="title">
		<xsl:value-of select="concat(substring(.,1,13),substring(.,18,2),substring(.,14,4))"/>
            </xsl:attribute>
            <xsl:choose>
                <xsl:when test=".='Y,Y,Y,Y,Y,Y,Y,Y,Y,Y'">
                    <xsl:text>FPKG</xsl:text>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:choose>
                        <xsl:when test="substring(.,13,7)='Y,Y,Y,Y'">
                            <xsl:text>MPKG</xsl:text>
                        </xsl:when>
                        <xsl:otherwise>
                            m
                            <xsl:call-template name="for-loop">
                            	<xsl:with-param name="intCurrent" select="1"/>
                            	<xsl:with-param name="intFinal" select="7"/>
                            	<xsl:with-param name="intIncrement" select="2"/>
                            	<xsl:with-param name="strInstring" select="concat(substring(.,13,1),substring(.,18,2),substring(.,14,4))"/>
                            	<xsl:with-param name="strNewstring" select="concat('F s s S','')"/>
                            </xsl:call-template>
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:choose>
                        <xsl:when test="substring(.,1,11)='N,N,N,N,N,N'">
                        </xsl:when>
                        <xsl:otherwise>
                            <br/>c
                            <xsl:call-template name="for-loop">
                            	<xsl:with-param name="intCurrent" select="1"/>
                            	<xsl:with-param name="intFinal" select="11"/>
                            	<xsl:with-param name="intIncrement" select="2"/>
                            	<xsl:with-param name="strInstring" select="substring(.,1,11)"/>
                            	<xsl:with-param name="strNewstring" select="concat('F s s S S S','')"/>
                            </xsl:call-template>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:otherwise>
            </xsl:choose>
	</td>
</xsl:template>
<xsl:template name="for-loop">
	<xsl:param name="intCurrent"/>
	<xsl:param name="intFinal"/>
	<xsl:param name="intIncrement"/>
	<xsl:param name="strInstring"/>
	<xsl:param name="strNewstring"/>
	<xsl:variable name="blnLoopContinue">
		<xsl:if test="$intCurrent &lt;= $intFinal">
			<xsl:text>true</xsl:text>
		</xsl:if>
	</xsl:variable>
	<xsl:if test="$blnLoopContinue = 'true'">
		<xsl:choose>
			<xsl:when test="substring($strInstring,$intCurrent,1) = 'Y'">
				<span><xsl:value-of select="substring($strNewstring,$intCurrent,1)"/></span>
			</xsl:when>
			<xsl:otherwise>
				<span><xsl:text>&#160;</xsl:text></span>
			</xsl:otherwise>
        	</xsl:choose>
		<xsl:call-template name="for-loop">
			<xsl:with-param name="intCurrent" select="$intCurrent + $intIncrement"/>
			<xsl:with-param name="intFinal" select="$intFinal"/>
			<xsl:with-param name="intIncrement" select="$intIncrement"/>
			<xsl:with-param name="strInstring" select="$strInstring"/>
			<xsl:with-param name="strNewstring" select="$strNewstring"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>
<xsl:template match="/">
<html>
 <head>
   <title>Participants</title>
   <link media="screen" type="text/css" href="css/ASTFdb.css" rel="stylesheet" /> 
   <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
   <link media="handheld, only screen and (max-width: 480px), only screen and (max-device-width: 480px)" href="css/ASTFdb_mob.css" type="text/css" rel="stylesheet" />
   <script type="text/javascript" src="ASTFtools.mini.js"></script>
   <script type="text/javascript" src="ASTFcookies.js"></script>
   <script type="text/javascript" src="ASTFdbhelp.js"></script>
   <script type="text/javascript" src="ASTFregisterhelp.js"></script>
   <xsl:comment><![CDATA[[if IE]>
     <link rel="stylesheet" title="ASTF style" type="text/css" href="css/ASTFdb_ie.css"/>
     <script type="text/javascript" >
       window.onload=function(){
       var tables = document.getElementsByTagName("table");
       for ( var t = 0; t < tables.length; t++ ) {
         var rows = tables[t].getElementsByTagName("tr");
         for ( var i = 1; i < rows.length; i += 2 )
           if ( !/(^|\s)odd(\s|$)/.test( rows[i].className ) )
              rows[i].className += " odd";
        } }  // end for loop
     </script>
   <![endif]]]>
   </xsl:comment>
 </head>
 <body onload="initSetup()">
 <div class="hiddenmenu" id="menudownload" onclick="hideMenuOptions(this);">
 <a onclick="hideMenuOptions(this);">DownloadCSV</a>
 </div>
  <div id="yearcycler" class="arrowcontainer"><span class="arrowleft" onclick="showYear(this,-1);" oncontextmenu="return showMenuOptions(this);"></span>2015<span class="arrowright" onclick="showYear(this,1);"></span></div>
  <div id="results">
  <table>
    <thead>
      <tr onclick="return toggleOverlaidForm(this,2);" >
      <xsl:for-each select="//row[1]/*">
        <th onMouseDown="return noteIntention(this);" >
          <xsl:value-of select="name()"/>
        </th>
      </xsl:for-each>
      </tr>   
   </thead>
    <tbody>
      <xsl:for-each select="//row">
          <tr>
              <xsl:apply-templates/>
        </tr>
      </xsl:for-each>
    </tbody>
  </table>
 </div>
 <div id="floatform1" >
   <form action="index.php?id=00d1dc87b40539415edd2fc7679b75cec75c4d58" method="post" name="ASTF1" id="ASTF1">
   <input type="hidden" name="RegistrationID" id="RegistrationID" value="" />
   <input type="hidden" name="DataEntryPersonID1" id="DataEntryPersonID1" value="" />
   <label for="fullname">Name:</label> <input type="text" name="fullname" id="fullname" /><br/>
      <input type="radio" name="ActivityType" value="4"/> <span> Cash paid </span><br/>
      <input type="radio" name="ActivityType" value="5"/> <span> Cheque at the door received</span><br/>
      <input type="radio" name="ActivityType" value="6"/> <span> Paypal at the door </span><br/>
      <input type="radio" name="ActivityType" value="7"/> <span> Cheque in the mail received </span><br/>
      <input type="radio" name="ActivityType" value="8"/> <span> Cheque cleared </span><br/>
      <input type="radio" name="ActivityType" value="9"/> <span> Paypal confirmed (copy-paste Txn #)</span><br/>
      <input type="radio" name="ActivityType" value="10"/> <span> Cancelled </span><br/>
      <input type="radio" name="ActivityType" value="11"/> <span> Cash refund </span><br/>
      <input type="radio" name="ActivityType" value="12"/> <span> Cheque refund mailed </span><br/>
      <input type="radio" name="ActivityType" value="13"/> <span> Paypal refund started </span><br/>
      <input type="radio" name="ActivityType" value="14"/> <span> Cheque refund cleared </span><br/>
      <input type="radio" name="ActivityType" value="15"/> <span> Paypal refund confirmed (copy-paste Txn #)</span><br/>
      <span>OR</span><br/>
      <select id="teacher" name="teacher" onChange="return showPrivateOptions()"> <option value="">SELECT PRIVATE TEACHER</option> </select><br/> 
    <label for="Amount">$Amount :</label> <input type="text" name="Amount" id="Amount" onkeypress="return isFloatNumberKey(event)" /><br/>
    <label for="CheckOrConfirmationNo">Chq/Txn# :</label> <input type="text" name="CheckOrConfirmationNo" id="CheckOrConfirmationNo" size="40" onkeypress="return isNumberKey(event)" /><br/>
	 <select id="yearxact" name="yearxact" >
		<option value="2010">2010</option>
		<option value="2011">2011</option>
		<option value="2012">2012</option>
		<option value="2013">2013</option>
		<option value="2014">2014</option>
		<option value="2015">2015</option>
	</select> 
     <label for="month">Type Month No.</label> <input type="text" name="monthxact" id="monthxact" value="" onkeypress="return isNumberKey(event)"/>
     <label for="day">Type Day</label> <input type="text" name="dayxact" id="dayxact" value="" onkeypress="return isNumberKey(event)"/><br/>
   <label for="submit">CLICK TO RECORD PAYMENT OR REQUEST PRIVATE:</label>
	<input name="record" type="submit" value="Record" id="record" onclick="return isValidChkTxnNo();" /><br/><br/>
   </form>
 </div>
 <div id="floatform2" >
   <form action="index.php?id=00d1dc87b40539415edd2fc7679b75cec75c4d57" method="post" name="ASTF2" id="ASTF2">
    <input type="hidden" name="ParticipantID" id="ParticipantID" value="" />
    <input type="hidden" name="DataEntryPersonID" id="DataEntryPersonID" value="" />
    <label for="autofillonname">Auto fill on name</label>
    <input type="checkbox" name="autofillonname" value="autofill" id="autofillonname" onchange="blnAutoSuggestNames=this.checked; return true;" /><br/>
     <hr />
    <label for="firstName">First Name:</label> <input type="text" name="firstName" id="firstName" onblur="setSaneInput(this);" value="" /><br/>
    <label for="midName">Middle Initial:</label> <input name="midName" type="text" id="midName" size="3" onblur="setSaneInput(this);" value="" /><br/>
    <label for="lastName">Last Name:</label> <input type="text" name="lastName" id="lastName" onblur="setSaneInput(this);" value="" /><br/>
     <hr />
    <label for="phonenumber"><img src="android.png"/><img src="iphone.png"/><img src="winphone.png"/>  No.:</label> <input type="text" name="phonenumber" id="phonenumber" onblur="setSaneInput(this);" value="" /><br/>
    <label for="email">Email:</label> <input type="text" name="email" size="30" id="email" onblur="setSaneInput(this);" value="" /><br/>
     <hr />
    <label for="lefol1">Lead:</label>
    <input name="lefol" type="radio" value="lead" id="lefol1" />
    <label for="lefol2">Follow:</label>
    <input name="lefol" type="radio" value="follow" id="lefol2" /><br/>
    <label for="danceLvl1">Beginner:</label>
    <input name="danceLvl" type="radio" value="beginner" id="danceLvl1"/>
    <label for="danceLvl2">Intermediate:</label>
    <input name="danceLvl" type="radio" value="intermediate" id="danceLvl2" />
    <label for="danceLvl3">Advanced:</label>
    <input name="danceLvl" type="radio" value="advanced" id="danceLvl3"/>
    <hr/>
    <label for="address">Address:</label> <textarea name="address" cols="40" rows="3" onblur="setSaneInput(this);" id="address" ></textarea><br/>
    <label for="city">City:</label> <input type="text" name="city" id="city" onblur="setSaneInput(this);" value="" /><br/>
     <label for="state">State:</label> <input type="text" name="state" id="state" onblur="setSaneInput(this);" value="" /><br/>
     <label for="zip">Zip Code:</label> <input type="text" name="zip" id="zip" onblur="setSaneInput(this);" value="" />
     <hr />
     <label ><a onclick="fh.p1on(); return getTotal();">Full Package</a></label><label><a onclick="fh.p2on(); return getTotal();">Milonga Package</a></label><br/>
     <hr />
     <label for="class1">Class 8:00-9:30 PM:</label> <input type="checkbox" name="classes[]" value="Fri 8:00-9:30 PM" id="class1" onclick="return getTotal()"/>
     <label for="milonga1">Friday Milonga </label>
     <input type="checkbox" name="milongas[]" value="Friday" id="milonga1" onclick="return getTotal()" /><br/>
     <hr />
     <label for="class2">Class 11:00-12:30 PM:</label> <input type="checkbox" name="classes[]" value="Sat 11:00-12:30 PM" id="class2" onclick="return getTotal()" /><br/>
     <label for="class3">Class 1:00-2:30 PM:</label> <input type="checkbox" name="classes[]" value="Sat 1:00-2:30 PM" id="class3" onclick="return getTotal()" />
     <label for="milonga4">Saturday 50/50 Milonga </label>
     <input type="checkbox" name="milongas[]" value="Saturday5050" id="milonga4" onclick="return getTotal()" />
     <label for="milonga2">Saturday Milonga </label>
     <input type="checkbox" name="milongas[]" value="Saturday" id="milonga2" onclick="return getTotal()" /><br/>
     <hr />
     <label for="class4">Class 1:00-2:30 PM:</label> <input type="checkbox" name="classes[]" value="Sun 1:00-2:30 PM" id="class4" onclick="return getTotal()" /><br/>
     <label for="class5">Class 3:00-4:30 PM:</label> <input type="checkbox" name="classes[]" value="Sun 3:00-4:30 PM" id="class5" onclick="return getTotal()" /><br/>
     <label for="class6">Class 5:00-6:30 PM:</label> <input type="checkbox" name="classes[]" value="Sun 5:00-6:30 PM" id="class6" onclick="return getTotal()" />
    <label for="milonga2">Sunday Asado Milonga </label>
     <input type="checkbox" name="milongas[]" value="Sunday" id="milonga3" onclick="return getTotal()" />
     <hr/>
     <label for="isStudent">Full-time student</label> 
     <input type="checkbox" name="isStudent" value="true" id="isStudent" onclick="return getTotal()" />
     <label for="groupDiscount">Group discount</label> 
     <input type="checkbox" name="groupDiscount" value="true" id="groupDiscount" onclick="return getTotal()" />
     <label for="fullAward">Full award</label> 
     <input type="checkbox" name="fullAward" value="true" id="fullAward" onclick="return getTotal()" />
     <label for="hostDiscount">Host Discount</label> 
     <input type="checkbox" name="hostDiscount" value="true" id="hostDiscount" onclick="return getTotal()" />
     <hr/>
	 <select id="yearregn" name="yearregn">
		<option value="2010">2010</option>
		<option value="2011">2011</option>
		<option value="2012">2012</option>
		<option value="2013">2013</option>
		<option value="2014">2014</option>
		<option value="2015">2015</option>
	</select> 
     <label for="monthregn">Type Month No.</label> <input type="text" name="monthregn" id="monthregn" value="" onkeypress="return isNumberKey(event)"/>
     <label for="dayregn">Type Day</label> <input type="text" name="dayregn" id="dayregn" value="" onkeypress="return isNumberKey(event)"/><br/>
     <hr />
      <input type="radio" name="payment" value="check" id="payment1" onclick="return getTotal();"/> <span> Cheque signup </span><br/>
      <input type="radio" name="payment" value="card" id="payment2" onclick="return getTotal();"/> <span> Paypal signup </span><br/>
      <input type="radio" name="payment" value="cashatdoor" id="payment3" onclick="return getTotal();"/> <span> Cash paid </span><br/>
      <input type="radio" name="payment" value="chequeatdoor" id="payment4" onclick="return getTotal();"/> <span> Cheque at the door </span><br/>
      <input type="radio" name="payment" value="paypalatdoor" id="payment5" onclick="return getTotal();"/> <span> Paypal at the door </span><br/>
      <input type="radio" name="payment" value="cashrefunddoor" id="payment6" onclick="return getTotal();"/> <span> Cash refund </span><br/>
      <input type="radio" name="payment" value="paypalrefundstart" id="payment7" onclick="return getTotal();"/> <span> Paypal refund started </span><br/>
    <label id="Amt">TOTAL:</label><br/>
    <label id="AmtPaid">PAID:</label><br/>
    <label for="AmtDue">DUE: ------------------->$</label> <input type="text" name="AmtDue" id="AmtDue" value="" />
    <label for="ChequeOrConfirmationNo">Chq/Txn# :</label> <input type="text" name="ChequeOrConfirmationNo" id="ChequeOrConfirmationNo" /><br/>
     <input name="register2" type="submit" value="Register" id="register2" onclick="return (isEmptyCheck('firstName,lastName');"/><br/><br/>

  </form>

 </div>
  <div id="floatform3" onclick="this.style.visibility='hidden';">
   <form action="" method="post" name="ASTF3" id="ASTF3">
		<span> HOLA </span>
   </form>
  </div>
<iframe id="regdata" height="0" width="0" src="loadregdata.php"></iframe>
</body></html>
</xsl:template>
</xsl:stylesheet>
