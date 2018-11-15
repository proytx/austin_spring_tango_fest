var G_STRYEAR = "";
var strDataEntryPersonID;
var G_OBJROWINDICES = { 'lastName' : [], 'firstName' : [], 'email' : [], 'phonenumber' : [] };
var strCodedTable;
var objTable;
function initSetup()
{
	// Done with the database entry cookie, now for the cookie to check if new additions were made
	if (G_STRYEAR == "") {
        	G_STRYEAR = getUrlVar("yr");
		// This roundabout way was needed because Chrome did not show the text inside div as children[1] element
		var objElem = G_DOM.getObject("yearcycler");
		if (objElem) {
			objElem = objElem.firstChild;
			if (objElem.nodeName != "span") objElem = objElem.nextSibling;
			objElem.nextSibling.nodeValue = G_STRYEAR;
		}
	}
        var objSelectTeacher = G_DOM.getObject('teacher');
	if (objSelectTeacher && objSelectTeacher.length == 1) {
		var arrTeachers = (getCookie("Teachers")).split(",");
		for (var i=0; i < arrTeachers.length; ++i) {
			var arrTmp = arrTeachers[i].split("_");
			for (var j=0, objOption; j < arrTmp.length; ++j) {
				objOption = new Option(arrTmp[j],arrTmp[j]);
				objSelectTeacher.appendChild(objOption);
			}
			var objOption = new Option(arrTeachers[i],arrTeachers[i]);
			objSelectTeacher.appendChild(objOption);
		}

	}
        var strOption = getUrlVar("opt");
	var arrParticipantIDs = [];
	var strTemp, strName, intFirstLetter, arrName;
        var objColumn, objNameColumn;
	var strParticipantIDs=getCookie("listParticipantIDs");
	if (strParticipantIDs) {
            arrParticipantIDs = strParticipantIDs.split("+");
            if (arrParticipantIDs[0].length != 5) {
                deleteCookie("listParticipantIDs");
                arrParticipantIDs = [];
                strParticipantIDs = null;
            }
        }
	// we go through the table no matter what - storing all IDs takes less space
	objTable = document.getElementsByTagName('table')[0];
	if (!objTable) {  // decided to move table away from direct view 
		objTable = getRegistrationData(document, 'encdata', 'table');
	}
	G_OBJROWINDICES['email'] = (getRegistrationData(document, 'Email', '')).split('\n');
	G_OBJROWINDICES['phonenumber'] = (getRegistrationData(document, 'Phonenumber', '')).split('\n');
	for (var i = 1, objRow, strRegistrationID, ii = 0, strClassName = "evenrow"; objRow = objTable.rows[i]; i++) { // cycle through all rows but header
		objIdColumn = objRow.cells[0];
                objNameColumn = objRow.cells[1];
                if (objNameColumn.firstChild) {
                    strName = objNameColumn.firstChild.nodeValue;
                    intFirstLetter = strName.toUpperCase().charCodeAt(0) - 65;
                    if ((intFirstLetter >= 0) && (intFirstLetter <=25)){
                        if (typeof G_OBJROWINDICES['lastName'][intFirstLetter] === 'undefined') {
				G_OBJROWINDICES['lastName'][intFirstLetter] = Array();
			}
			(G_OBJROWINDICES['lastName'][intFirstLetter]).push(i);
                    } // end of if firstletter between 0 and 25 check
		    arrName = strName.split(",");
                    strName =(arrName.length > 1)?trim12(arrName[1]):''; 
                    intFirstLetter = strName.toUpperCase().charCodeAt(0) - 65;
                    if ((intFirstLetter >= 0) && (intFirstLetter <=25)){
                        if (typeof G_OBJROWINDICES['firstName'][intFirstLetter] === 'undefined') {
				G_OBJROWINDICES['firstName'][intFirstLetter] = Array();
			}
			(G_OBJROWINDICES['firstName'][intFirstLetter]).push(i);
                    } // end of if firstletter of firstname between 0 and 25 check
                } // end if objName.firstChild test
                    
                strRegistrationID = objIdColumn.getAttribute("title");
                if (G_STRYEAR != "all" && strRegistrationID.substring(0,4) != G_STRYEAR) {
                    objRow.className = "hiddenrow";
                } else {
                    objRow.className = "";
                    ++ii;
                    if (ii % 2 == 0) { objRow.className = "evenrow";}
                    else { objRow.className = "oddrow";}
		    // moved this code to be specific to the displayed year data only - data for all years exceeds cookie size 4093 bytes
		    if (objIdColumn.firstChild) {
			strValue = objIdColumn.firstChild.nodeValue;
			if (strParticipantIDs) {
                                if (strValue != arrParticipantIDs[ii-1]) { //if cookie was read in insert in array
				    arrParticipantIDs.splice(ii-1,0,strValue);
				    //objColumn.style.backgroundColor = "yellow";
				    objIdColumn.style.fontWeight = "bold";
				    objIdColumn.style.color = "blue";
                                } else {
                                    //objColumn.style.backgroundColor = "white";
                                    objIdColumn.style.fontWeight = "normal";
                                    objIdColumn.style.color = "black";
                                }
			} else { // no cookie, then append
				arrParticipantIDs[ii-1] = strValue;
				//objColumn.style.backgroundColor = "white";
			}
		    }
                }
	} // for loop ends here
	setCookie("listParticipantIDs",arrParticipantIDs.join("+"),50);
        var strPostResult = getCookie("lastPostResult");
        if (strOption.length > 0 && strPostResult.length > 0) {
            G_DOM.getObject('floatform3').style.visibility = 'visible';
            var objForm= document.ASTF3;
            if (objForm.hasChildNodes()) {
                while (objForm.childNodes.length >= 1) { objForm.removeChild(objForm.firstChild); }
            }
            objDiv = document.createElement("div");
            objDiv.style.fontSize = "2em";
            objDiv.style.color = "white";
            if (strPostResult.indexOf('Success') == 0) {
                objDiv.style.backgroundColor = "green";
            } else if (strPostResult.indexOf('Warning') == 0) {
                objDiv.style.backgroundColor = "yellow";
            } else {
                objDiv.style.backgroundColor = "red";
            }
            objDiv.appendChild(document.createTextNode(strPostResult));
            objForm.appendChild(objDiv);
            setTimeout (
                    function() {
                        G_DOM.getObject('floatform3').style.visibility = 'hidden';
			deleteCookie('lastPostResult');
                    }, 2500);
        }
}

// function that gets a cookie name
function getCookie(strName)
{
var i,x,y,arrCookies=document.cookie.split(";");
for (i=0;i<arrCookies.length;i++)
  {
  x=arrCookies[i].substr(0,arrCookies[i].indexOf("="));
  y=arrCookies[i].substr(arrCookies[i].indexOf("=")+1);
  x=x.replace(/^\s+|\s+$/g,"");
  if (x==strName)
	{
	return unescape(y);
	}
  }
return "";
}

// function that gets a cookie name
function setCookie(strName,strValueIn,intNumberDaysExpiry)
{
var dtmDate=new Date();
dtmDate.setDate(dtmDate.getDate() + intNumberDaysExpiry);
var strValue=escape(strValueIn) + ((intNumberDaysExpiry==null) ? "" : "; expires="+dtmDate.toUTCString());
document.cookie=strName + "=" + strValue;
}

// function that gets a cookie name
function deleteCookie(strName)
{
document.cookie=strName + "=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
}
