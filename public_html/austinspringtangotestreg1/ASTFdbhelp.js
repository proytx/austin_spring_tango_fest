/*@cc_on
    (function(f) {
        window.setTimeout = f(window.setTimeout);   // overwrites the global function!
        window.setInterval = f(window.setInterval); // overwrites the global function!
    })(function(f) {
        return function(c, t) {
            var a = [].slice.call(arguments, 2);    // gathers the extra args
            return f(function() {
                c.apply(this, a);                   // passes them to your function
            }, t);
        };
    });
@*/
var G_BLNAUTOSUGGESTNAMES = false;
// Need to remember the initial choice of items, i.e., classes and milongas, and transaction history when the data entry form pops up
var G_arrItems;
var G_arrXacts = [];
var G_dblDue = 0.00;
var G_dblTotal = 0.00;
function toggleOverlaidForm (objElement,intNumber) {
    window.clearTimeout(intTimer);
    //if (document.getElementById('floatform3').style.visibility = 'visible') return false; // if the 
    var strOption = '';
    var objFormDiv = null;
    var strYear = getFestDate('').getFullYear();
    var strUrlNoOrigin = (document.location.href).slice((document.location.origin).length + 1);
    if (strUrlNoOrigin.indexOf("ASTFdbReport.php") != -1) return false;
    if (strUrlNoOrigin.indexOf("index.php") != -1) {
        strYear = getUrlVar("yr");
        if (strUrlNoOrigin.indexOf("id=00d1dc87b40539415edd2fc7679b75cec75c4d57") != -1) {
            strOption = 'formParticipant';
            objFormDiv = G_DOM.getObject('floatform2');
        }
        else if (strUrlNoOrigin.indexOf("id=00d1dc87b40539415edd2fc7679b75cec75c4d58") != -1) {
            strOption = 'formTransaction';
            objFormDiv = G_DOM.getObject('floatform1');
        } 
    }
    
    // toggle the form OFF
    if (objFormDiv.style.visibility == 'visible') {
      objFormDiv.style.visibility = 'hidden';
    } else { // toggle the form ON
        objFormDiv.style.visibility = 'visible';
        objElement = objElement.parentNode; // whether the participant or transaction form, these originate from the table cell, not row, as the previous code did, for compatibility
        var strEditOption = getUrlVar('opt');
        
        if (strOption == 'formParticipant') {   // this is the larger form that replicates registration functionality
            resetForm('ASTF2');
            if (objElement.nodeName != 'THEAD') {

                if (strYear != 'all') {
                    if (strEditOption == "") {strUrlNoOrigin += '&opt=edit';} 
                    else {strUrlNoOrigin = strUrlNoOrigin.replace('opt='+strEditOption,'opt=edit');}
                }
                fillFormFromRow(objElement);
            } else { // end of filling up form when clicking on a row that's not the table header 
                            // This var  is declared in ASTFregisterhelp.js which is common between this database form and normal website registration form
                            //G_BLNAUTOSUGGESTNAMES = true;
                if (strEditOption == "") {strUrlNoOrigin += '&opt=add';} 
                else {strUrlNoOrigin = strUrlNoOrigin.replace('opt='+strEditOption,'opt=add');}
                    G_DOM.getObject('autofillonname').checked=true;
                        }
            document.ASTF2.action = strUrlNoOrigin;
         } else if (strOption == 'formTransaction') { 
            if (objElement.nodeName != 'THEAD') {
                var dtmToday = new Date();
                var objCells = G_DOM.getTagObjects(objElement,'td');
                var objCellName = objACell(objCells[1]);
                G_DOM.getObject('fullname').value = objCellName.getValue();
                var strRegistrationID = objCells[0].getAttribute("title"); 
                G_DOM.getObject('RegistrationID').value = strRegistrationID;
                var strYearRegn = strRegistrationID.slice(0,4);
                if (strEditOption == "") {strUrlNoOrigin += '&opt=add';} 
                else {strUrlNoOrigin = strUrlNoOrigin.replace('opt='+strEditOption,'opt=add');}
                strUrlNoOrigin = strUrlNoOrigin.replace('yr='+strYear, 'yr='+strYearRegn);
                //G_DOM.getObject('RegistrationID').value = G_DOM.getObject('yearxact').value + Array(6-strID.length).join('0') + strID;
                //G_DOM.getObject('yearxact').value =(new Date()).getFullYear();
                G_DOM.getObject('yearxact').selectedIndex = strYearRegn - G_DOM.getObject('yearxact').options[0].text;
                G_DOM.getObject('monthxact').value= ("00" + (dtmToday.getMonth() + 1)).slice(-2);
                G_DOM.getObject('dayxact').value= ("00" + dtmToday.getDate()).slice(-2);
                //G_DOM.getObject('yearxact').selectedIndex =(new Date()).getFullYear() - G_DOM.getObject('yearxact').options[0].text;
                G_DOM.getObject('teacher').selectedIndex = 0;
                G_DOM.getObject('CheckOrConfirmationNo').value = "";
                G_DOM.getObject('DataEntryPersonID1').value = getCookie('dataEntryPersonID');
                G_DOM.getObject('Amount').value = "";
                G_DOM.getObject('Amount').focus();
            }
            document.ASTF1.action = strUrlNoOrigin;
         }
    }
   return true;   
}

function resetForm(strFormName) {
    var objClasses = eval(document[strFormName]["classes[]"]);
    var objMilongas = eval(document[strFormName]["milongas[]"]);
    for (var i=0; i < objClasses.length;  i++) objClasses[i].checked = false;
    for (i=0; i < objMilongas.length;  i++) objMilongas[i].checked = false;
    G_DOM.getObject('ParticipantID').value = '';
    G_DOM.getObject('DataEntryPersonID').value = getCookie('dataEntryPersonID');
    G_DOM.getObject('firstName').value = ''; 
    G_DOM.getObject('lastName').value = ''; 
    G_DOM.getObject('midName').value ='';
    G_DOM.getObject('danceLvl1').checked=false; 
    G_DOM.getObject('danceLvl2').checked=false; 
    G_DOM.getObject('danceLvl3').checked=false; 
    G_DOM.getObject('lefol1').checked=false; 
    G_DOM.getObject('lefol2').checked=false; 
    G_DOM.getObject('address').value = '';
    G_DOM.getObject('city').value = ''; 
    G_DOM.getObject('state').value = ''; 
    G_DOM.getObject('zip').value ='';
    G_DOM.getObject('isStudent').checked = false;
    G_DOM.getObject('groupDiscount').checked = false;
    G_DOM.getObject('fullAward').checked = false;
    G_DOM.getObject('hostDiscount').checked = false;
    var dtmToday = new Date();
    G_DOM.getObject('yearregn').selectedIndex =dtmToday.getFullYear() - G_DOM.getObject('yearregn').options[0].text;
    G_DOM.getObject('monthregn').value= ("00" + (dtmToday.getMonth() + 1)).slice(-2);
    G_DOM.getObject('dayregn').value= ("00" + dtmToday.getDate()).slice(-2);

}

function fillFormFromRow( objElement) {
    var strFormName = 'tango1';
    var objCells = G_DOM.getTagObjects(objElement, 'td');

    G_DOM.getObject('ParticipantID').value = objCells[0].firstChild.nodeValue;
    var strYearRegn = (objCells[0].getAttribute("title")).slice(0,4);
    if (document.location.href.indexOf('index.php') != -1) {
        strFormName = 'ASTF2';
        G_DOM.getObject('yearregn').selectedIndex = strYearRegn - G_DOM.getObject('yearregn').options[0].text;
    }

    var objClasses = eval(document[strFormName]["classes[]"]);
    var objMilongas = eval(document[strFormName]["milongas[]"]);
    var strName= objCells[1].firstChild.nodeValue;
    var arrName = strName.split(",");   
    G_DOM.getObject('lastName').value = (arrName.length > 0)?arrName[0]:''; 
    var strName2 =(arrName.length > 1)?arrName[1]:''; 
    var arrName2 = strName2.split(" ");
    G_DOM.getObject('firstName').value =trim12((arrName2.length > 2)?(arrName2[0]+ ' ' + arrName2[1]):arrName2[0]); 
    G_DOM.getObject('midName').value =(arrName2.length > 2)?arrName2[2]:((arrName2.length == 1)?'':arrName2[1]);

    var objCellLevel = objACell(objCells[2]);
    G_DOM.getObject('danceLvl1').checked = objCellLevel.Equals('B');
    G_DOM.getObject('danceLvl2').checked = objCellLevel.Equals('I');
    G_DOM.getObject('danceLvl3').checked = objCellLevel.Equals('A');

    var objCellRole = objACell(objCells[3]);
    G_DOM.getObject('lefol1').checked = objCellRole.Equals('Lead');
    G_DOM.getObject('lefol2').checked = objCellRole.Equals('Follow');
    //strItems= objElement.getElementsByTagName('td')[4].firstChild.nodeValue;
    if ((document.location.href.indexOf('register.php') != -1) && (strYearRegn != getUrlVar('yr'))) {
        displayAmounts(0,0,0);
        return;
    }
    strItems= objCells[4].getAttribute("title");
    G_arrItems = strItems.split(',');

    for (i=0; i < objClasses.length;  i++) {
         objClasses[i].checked = (G_arrItems[i] == 'Y');
    } 
    for (i=0; i < objMilongas.length;  i++) {
         objMilongas[i].checked = (G_arrItems[6+i] == 'Y');
    }

    var objCellCity = objACell(objCells[5]);
    G_DOM.getObject('city').value = objCellCity.getValue();

    var objCellState = objACell(objCells[6]);
    G_DOM.getObject('state').value = objCellState.getValue();

    var objCellZip = objACell(objCells[7]);
    G_DOM.getObject('zip').value = objCellZip.getValue();

    var objCellDiscount = objACell(objCells[8]);
    G_DOM.getObject('isStudent').checked = objCellDiscount.Equals('1');
    if (document.location.href.indexOf('index.php') != -1) {
        G_DOM.getObject('groupDiscount').checked = objCellDiscount.Equals('2');
        G_DOM.getObject('fullAward').checked = objCellDiscount.Equals('4');
        G_DOM.getObject('hostDiscount').checked = objCellDiscount.Equals('8');
    }

    // Code to parse History column entries copied from noteIntent (pops up charts) below and adapted
    var objCellHistory = objACell(objCells[9]);
    dblPaidTotal = 0;
    G_arrXacts = [];
    var arrLines = objCellHistory.getValues();
    // Initialize a date before this festival was born WARNING!!!WARNING, Chrome devtools says invalid date proto but works
    // NOT -Initialize date to beginning of festival year, this creates issues for people paying in Nov-Dec of previous year
    // should look dblo this later, the previous option would have been ideal
    var dtmFestDate = getFestDate(strYearRegn);
    var dtmEarlyBird = getEarlyBird(dtmFestDate);
    var dtmLastXact = new Date(strYearRegn,0,1);
    var dtmLastSignup = new Date(strYearRegn,0,1);
    // Calculate the lower and higher limits that participant should have paid, based on the item selections
    // Gets complicated when some items are pre-early-bird, others post, e.g., added during the festival
    var dblDueHi = getPrice(0.00, dtmFestDate);
    var dblDueLo = getPrice(0.00, dtmEarlyBird);
    for (var j=0,arrTmp,dblAmt,strType,strLine; j<arrLines.length; j++) {
        strLine = arrLines[j];
        arrTmp = strLine.split('$');
        dblAmt = parseFloat( (arrTmp[1].slice(0,arrTmp[1].indexOf(' '))) );
        strType = arrTmp[1].substring(arrTmp[1].indexOf(' ')+1);
        dtmXact =  new Date((trim12(arrTmp[0])).replace(/-/g,'/'));
        //if (dblWeek<0) alert(strAmt + ':' + strType  + ':' + arrTmp[0]);
        if ( (strType.indexOf('refund confirmed') != -1) || 
            (strType.indexOf('refund cleared') != -1) ||
            (strType.indexOf('Cash refund') != -1) ) {
            dblPaidTotal -= dblAmt;                             
            if (dtmXact > dtmLastXact) dtmLastXact = dtmXact;
            G_arrXacts.push({'dayXact' : dtmXact, 'amtXact': -dblAmt, 'paid' : "P"});
        } else if ( (strType.indexOf('paid') != -1) || 
            (strType.indexOf('cleared') != -1) || 
            (strType.indexOf('received') != -1) || 
            (strType.indexOf('confirmed') != -1) ) {
            // In 2014, austin tango society abolished the Paypal tax, 
            // so this adjustment is needed to show that the person actually paid the full amount owed
            if ( (2013 < parseInt( strYearRegn)) && (strType.indexOf('Paypal') != -1)) dblAmt = dblAmt * 1.03;
            dblPaidTotal += dblAmt;
            if (dtmXact > dtmLastXact) dtmLastXact = dtmXact;
            G_arrXacts.push({'dayXact' : dtmXact, 'amtXact': dblAmt, 'paid' : "P"});
        } else {
            if ( (2013 < parseInt( strYearRegn)) && (strType.indexOf('Paypal web') != -1)) dblAmt = dblAmt * 1.03;
            // Starting festival day, anything received at the door, even cheque, is considered paid
            if ( (dtmXact >= dtmFestDate) && (strType.indexOf('@door') != -1) ) {
                if (dtmXact > dtmLastXact)  dtmLastXact = dtmXact;
                G_arrXacts.push({'dayXact' : dtmXact, 'amtXact': dblAmt, 'paid' : "P"});
            } else {
                G_arrXacts.push({'dayXact' : dtmXact, 'amtXact': -dblAmt});
            }
            if (dtmXact > dtmLastSignup) dtmLastSignup = dtmXact;
        }
    }   // end of for loop through individual lines of transaction
    //G_arrXacts.sort( function(el1, el2) { return (el2.amtXact - el1.amtXact); } );
    G_arrXacts.sort( function(el1, el2) { return (el1.dayXact - el2.dayXact); } );
    var arrRealXacts = G_arrXacts.filter(function(el) { return el.paid; });
    var dblPaid = 0.00;
    G_dblDue = 0.00;
    G_dblTotal = 0.00;
    if (arrRealXacts.length == 0) {  // no payments made yet
        var arrEarlyXacts = G_arrXacts.filter(function(el) { return (el.dayXact < dtmEarlyBird); });
        G_dblTotal = (arrEarlyXacts.length == 0 ) ? (dblDueHi) : (dblDueLo);
        // TODO handle thte case where items were booked at different rates
    } else { // in case one or more payments already been made
        dblPaid = arrRealXacts.reduce(function(lastValue, currEl){return (lastValue + currEl.amtXact);} , 0.00);
        var arrEarlyXacts = G_arrXacts.filter(function(el) { return (el.dayXact < dtmEarlyBird); });
        G_dblTotal = (arrEarlyXacts.length == 0 ) ? (dblDueHi) : (dblDueLo);
        // The complicated case of some items at early-bird others not - just trust that the paid amount IS the TOTA:
        if ((dblPaid < dblDueHi) && (dblPaid > dblDueLo) ) G_dblTotal = dblPaid;
    }
    G_dblDue = G_dblTotal - dblPaid;
    // Don't overwrite the month day values if it is already festival time for this registrant - easier for data entry
    if ( (document.location.href.indexOf('index.php') != -1) && !((strYearRegn == dtmFestDate.getFullYear()) && (new Date() >= dtmFestDate)) ) {
        G_DOM.getObject('monthregn').value= ("00" + (dtmLastXact.getMonth() + 1)).slice(-2);
        G_DOM.getObject('dayregn').value= ("00" + dtmLastXact.getDate()).slice(-2);
    }
    
    displayAmounts( G_dblTotal, G_dblDue, dblPaid );


}

// added the code for 3 second click
var intTimer = null;
var objClickedElement = null;
function noteIntention(objElement) {
    //objClickedElement = objElement;
    intTimer = window.setTimeout(
        function(objElement)    {
            //var strYear = getUrlVar("yr"); // Now that the year changes on the client side with yearcycler
            var strYear = G_STRYEAR;
            var strWhichColumn = objElement.firstChild.nodeValue;
            if ('CityStateLevelLeadFollowSignedUpForHistory'.indexOf(strWhichColumn) != -1) {   
                var strText,strText1,strRegistrationID;
                var objColumn, objColumn1, objColumn2, objIDColumn;
                var arrData = {}; // shortcut for new object
                var arrData1 = {};
                var intMaxValue = 0;
                var arrChoices, arrTemp;
                var intNoFullPackages = 0;
                var intNoMilongaPackages = 0;
                var intColumnIndex = objElement.cellIndex;
                var objTable = document.getElementsByTagName("table")[0];
                if (strWhichColumn == 'SignedUpFor') { // initialization for sign up sheet
                    var strTemp = 'Class Fri 8-9:30,Class Sat 12-1:30,Class Sat 2-3:30,Class Sun 1-2:30,Class Sun 3-4:30,'
                        + 'Class Sun 5-6:30,Milonga Fri,Practica/Milonga 50/50,Milonga Sat,Milonga Sun';
                    arrTemp = strTemp.split(",");
                    for (var k=0; k < arrTemp.length; k++) {arrData[arrTemp[k]] = 0; arrData1[arrTemp[k]] = {};}
                    for (var i = 1, objRow; objRow = objTable.rows[i]; i++) { // cycle through all rows but header
                                                objIDColumn = objRow.cells[0];
                                                strRegistrationID = objIDColumn.getAttribute("title");
                                                if (strRegistrationID.substring(0,4) != strYear) continue;
                        objColumn = objRow.cells[intColumnIndex];
                        objColumn1 = objRow.cells[intColumnIndex-1];
                        objColumn2 = objRow.cells[intColumnIndex-2];
                        //strText = objColumn.firstChild? objColumn.firstChild.nodeValue:'';    
                        strText = objColumn.firstChild? objColumn.getAttribute('title'):''; 
                        strText1 = (objColumn1.firstChild? (objColumn1.firstChild.nodeValue=='Lead'?'\u2642':objColumn1.firstChild.nodeValue=='Follow'?'\u2640':'\u2642\u2640'):'_')
                                     + (objColumn2.firstChild? objColumn2.firstChild.nodeValue:'?') ;
                        if (strText == 'Y,Y,Y,Y,Y,Y,Y,Y,Y,Y') 
                            { intNoFullPackages += 1;}
                        else if (strText.match(/,Y,Y,Y,Y$/))
                            { intNoMilongaPackages += 1;}
                        arrChoices = strText.split(","); 
                        for (var ii=0; ii < arrChoices.length; ii++) {
                            arrData[arrTemp[ii]] += (arrChoices[ii]=='Y')?1:0;
                            if (arrData1[arrTemp[ii]][strText1] != undefined) {
                                arrData1[arrTemp[ii]][strText1] += (arrChoices[ii]=='Y')?1:0;
                            } else { if (arrChoices[ii]=='Y') arrData1[arrTemp[ii]][strText1] = 1;}                         
                        }
                        for (var jj=0; jj < arrTemp.length; jj++) if (arrData[arrTemp[jj]] > intMaxValue) intMaxValue =arrData[arrTemp[jj]];                    
                    }
                }   else {
                    // index starts at 1 to avoid header
                    var intPaidTotal = 0;
                    var intSignedupTotal = 0;
                    var intWeekMax = -99;
                    var intWeekMin = 99;
                    for (var i = 1, objRow; objRow = objTable.rows[i]; i++) {
                                                objIDColumn = objRow.cells[0];
                                                strRegistrationID = objIDColumn.getAttribute("title");
                                                if (strRegistrationID.substring(0,4) != strYear) continue;
                        objColumn = objRow.cells[intColumnIndex];
                        if (strWhichColumn == 'History') {
                            var arrDupLines=[];
                            var dtmFestDate = getFestDate(strYear);
                            for (var j=0,intWeek,arrTmp,intAmt,strType,strLine,strText1; j<objColumn.childNodes.length; j++) {
                                if (objColumn.childNodes[j].nodeType == 3) {
                                    strLine = objColumn.childNodes[j].nodeValue;
                                    if (arrDupLines[strLine] == undefined) {    // if this line is a first
                                        arrDupLines[strLine] = 1;
                                        arrTmp = strLine.split('$');
                                        intAmt = (arrTmp[1].slice(0,arrTmp[1].indexOf(' '))) * 100; // the cents count!
                                        strType = arrTmp[1].substring(arrTmp[1].indexOf(' ')+1);
                                        intWeek = Math.ceil((dtmFestDate - Date.parse(arrTmp[0].replace(/-/g,'/')))/(1000*60*60*24*7));
                                        if (intWeek < intWeekMin) intWeekMin = intWeek;
                                        if (intWeek > intWeekMax) intWeekMax = intWeek;
                                        //if (intWeek<0) alert(strAmt + ':' + strType  + ':' + arrTmp[0]);
                                        strText = ((intWeek < 10 && intWeek >=0)?'Week_0-0':'Week_0-') + intWeek;
                                        if ( (strType.indexOf('refund confirmed') != -1) || 
                                            (strType.indexOf('refund cleared') != -1) ||
                                            (strType.indexOf('Cash refund') != -1) ) {
                                            strText1 = strText + 'refund';
                                            intPaidTotal -= intAmt;                         
                                            if (arrData[strText1] != undefined) {
                                                arrData[strText1][0] -=  intAmt;
                                            } else { arrData[strText1] = [-intAmt, 0];}
                                        } else if ( (strType.indexOf('paid') != -1) || 
                                            (strType.indexOf('cleared') != -1) || 
                                            (strType.indexOf('received') != -1) || 
                                            (strType.indexOf('confirmed') != -1) ) {
                                            strText1 = strText + 'paid';
                                            intPaidTotal += intAmt;
                                            if (arrData[strText1] != undefined) {
                                                arrData[strText1][0] +=  intAmt;
                                            } else { arrData[strText1] = [intAmt, 0];}
                                        } else if (strType.indexOf('signup') != -1) {
                                            strText1 = strText + 'clickthrus';
                                            intSignedupTotal += intAmt;
                                            if (arrData[strText1] != undefined) {
                                                arrData[strText1][0] +=  intAmt;
                                            } else { arrData[strText1] = [intAmt, 0];}
                                        }

                                        //if (intWeek == 4) alert(strLine + '-' + arrData[strText1]);
                                    }   // end check duplicate line
                                }
                            }   // end of for loop through individual lines of transaction
                        } else { // not the Hostory column                      
                            strText = objColumn.firstChild? objColumn.firstChild.nodeValue:'';
                            if (arrData[strText] != undefined) {
                                arrData[strText] += 1;
                                if (arrData[strText] > intMaxValue) intMaxValue = arrData[strText];
                            } else { arrData[strText] = 1;}
                        } // end if else for History and other columns
                    } // end for loop going through all rows
                    if (strWhichColumn == 'History') {
                        intMaxValue = (intPaidTotal > intSignedupTotal) ? intPaidTotal : intSignedupTotal;
                        //arrData['TOTALClickThrus'] = [intSignedupTotal, intSignedupTotal];
                        //arrData['TOTALpaid'] = [intPaidTotal, intPaidTotal];
                        for (var k= intWeekMax, intLastSum1 = 0, intLastSum2=0; k > (intWeekMin - 1); --k) {
                            var strKey = 'Week_0-' + ("00" + k).slice(-2) + 'paid';
                            if (arrData[strKey] != undefined) {
                                arrData[strKey][1] = arrData[strKey][0]  + intLastSum1;
                                intLastSum1 = arrData[strKey][1];
                            }
                            strKey = 'Week_0-' + ("00" + k).slice(-2) + 'refund';
                            if (arrData[strKey] != undefined) {
                                arrData[strKey][1] = arrData[strKey][0]  + intLastSum1;
                                intLastSum1 = arrData[strKey][1];
                            }
                            strKey = 'Week_0-' + ("00" + k).slice(-2) + 'clickthrus';
                            if (arrData[strKey] != undefined) {
                                arrData[strKey][1] = arrData[strKey][0]  + intLastSum2;
                                intLastSum2 = arrData[strKey][1];
                            }
                        }
                    } // end of final if for postprocessing totals after going through all rows
                }
                //var intMultiplyFactor = Math.floor (((screen.width < 600)? screen.width : 600)/intMaxValue);
                var dblMultiplyFactor = ((screen.width < 600)? screen.width : 600)/intMaxValue;
                G_DOM.getObject('floatform3').style.visibility = 'visible';
                var arrKeys = [];
                var objForm= document.ASTF3;
                if (objForm.hasChildNodes()) {
                    while (objForm.childNodes.length >= 1) {
                        objForm.removeChild(objForm.firstChild);
                    }
                }
                for (var objKey in arrData) arrKeys.push(objKey);
                if (strWhichColumn == 'History' ) arrKeys.sort();
                var objDiv = document.createElement("div");
                objDiv.style.backgroundColor = "silver";
                var strOnTop = "";
                if (strWhichColumn == 'SignedUpFor' ) {
                    strOnTop = " different entries(" + intNoFullPackages + " full packages " + intNoMilongaPackages + " milonga packages)";
                } else {
                    strOnTop = " different entries";                    
                }
                var objTextnode = document.createTextNode(arrKeys.length + strOnTop);
                objDiv.appendChild(objTextnode);
                objForm.appendChild(objDiv);
                for (var j=0; j<arrKeys.length; j++) { // loop through associative array to build histogram
                    objDiv = document.createElement("div");
                    //objDiv.setAttribute("id", arrKeys[j] + arrData[arrKeys[j]]);
                    objDiv.style.backgroundColor = "red";
                    if (strWhichColumn == 'History' && arrKeys[j].indexOf('paid')!=-1) objDiv.style.backgroundColor = "green";
                    if (strWhichColumn == 'History' && arrKeys[j].indexOf('refund')!=-1) objDiv.style.backgroundColor = "purple";
                    objDiv.style.margin = "2px";                    
                    objDiv.style.fontSize = "0.7em";
                    objDiv.style.color = "white";
                    //objDiv.style.width = intMultiplyFactor * arrData[arrKeys[j]];
                    objDiv.style.width = (strWhichColumn == 'History')? Math.round(dblMultiplyFactor * arrData[arrKeys[j]][1]) : 
                                                Math.round(dblMultiplyFactor * arrData[arrKeys[j]]);
                    objDiv.style.overflow = "visible";
                    objDiv.style.whiteSpace = "nowrap";
                    objTextnode = document.createTextNode(arrKeys[j] + "=" + ( (strWhichColumn == 'History')? 
                                ( (arrData[arrKeys[j]][0]/100).toFixed(2) + ' Tot: ' + (arrData[arrKeys[j]][1]/100).toFixed(2) ) :
                                arrData[arrKeys[j]] ) );
                    objDiv.appendChild(objTextnode);
                    objForm.appendChild(objDiv);
                    if (strWhichColumn == 'SignedUpFor' ) {
                        var objDiv1, objDiv2;
                        var intLeadTotal = 0, intFollowTotal = 0, intSpecialTotal =0;
                        var arrKeys1 =[];
                        for (var objKey1 in (arrData1[arrKeys[j]])) arrKeys1.push(objKey1);
                        objDiv1 = objDiv.cloneNode(false  ); // clone just the node, not its children
                        objDiv1.style.backgroundColor = "black";
                        objDiv1.style.color = "black";
                        //objDiv1.style.display = 'inline-block';
                        arrKeys1 = arrKeys1.sort();
                        arrBlues = ['steelblue', '#7EC0EE', '#00BFFF', '#4F94CD', 'steelblue', '#7EC0EE', '#00BFFF', '#4F94CD', 'steelblue', '#7EC0EE', '#00BFFF', '#4F94CD'];
                        arrPinks = ['hotpink', '#FF6EB4', '#EE1289', 'lightpink', 'hotpink', '#FF6EB4', '#EE1289', 'lightpink', 'hotpink', '#FF6EB4', '#EE1289', 'lightpink'];
                        arrGrays = ['gray', '#8E8E8E', '#AAAAAA', 'darkgray', 'gray', '#8E8E8E', '#AAAAAA', 'darkgray', 'gray', '#8E8E8E', '#AAAAAA', 'darkgray'];
                        for (var k=0; k<arrKeys1.length; k++) { // for loop for leadfollow/level  category partitioning
                            // if not declared as lead or follow (or both) don't include them in the graphs
                            if ((arrKeys1[k].indexOf('\u2642') == -1) && (arrKeys1[k].indexOf('\u2640') == -1)) continue;
                            if ((arrKeys1[k].indexOf('\u2642') != -1) && (arrKeys1[k].indexOf('\u2640') != -1)) continue;
                            objDiv2 = document.createElement("div");
                            //objDiv2.style.display = 'inline-block'; moved this to ASTFdb.css
                            objDiv2.style.textAlign = 'center';
                            objDiv2.style.overflow = "visible";
                            objDiv2.style.width = Math.round(dblMultiplyFactor * arrData1[arrKeys[j]][arrKeys1[k]]);
                            if (arrKeys1[k].indexOf('\u2642') != -1) {
                                objDiv2.style.backgroundColor = arrBlues[k];
                                intLeadTotal += arrData1[arrKeys[j]][arrKeys1[k]];
                            } else if (arrKeys1[k].indexOf('\u2640') != -1) {
                                objDiv2.style.backgroundColor = arrPinks[k];                            
                                intFollowTotal += arrData1[arrKeys[j]][arrKeys1[k]];
                            } else {
                                objDiv2.style.backgroundColor = arrGrays[k];                            
                                intSpecialTotal += arrData1[arrKeys[j]][arrKeys1[k]];                           
                            }
                            
                            objTextnode = document.createTextNode((arrKeys1[k]).charAt(1) + "=" + arrData1[arrKeys[j]][arrKeys1[k]]);
                            objDiv2.appendChild(objTextnode);
                            objDiv1.appendChild(objDiv2);
                        }   // end for loop through the combo leadfollow/level category

                        var objDiv3 = objDiv1.cloneNode(false);
                        var objDiv4 = objDiv2.cloneNode(false);
                        var objDiv5 = objDiv2.cloneNode(false);
                        objTextnode = document.createTextNode("\u2642=" + intLeadTotal);
                        objDiv4.style.backgroundColor = 'steelblue';
                        objDiv4.style.width = Math.round(dblMultiplyFactor * intLeadTotal);
                        objDiv4.appendChild(objTextnode);
                        objTextnode = document.createTextNode("\u2640=" + intFollowTotal);
                        objDiv5.style.backgroundColor = 'hotpink';
                        objDiv5.style.width = Math.round(dblMultiplyFactor * intFollowTotal);
                        objDiv5.appendChild(objTextnode);
                        objDiv3.appendChild(objDiv5);   // insert follow totals
                        objDiv3.appendChild(objDiv4);   // insert lead totals
                        objForm.appendChild(objDiv3);
                        objForm.appendChild(objDiv1);
                        var objDiv6 = objDiv1.cloneNode(false);
                        objTextnode = document.createTextNode("Divider");
                        objDiv6.appendChild(objTextnode);
                        objForm.appendChild(objDiv6);
                    }
                } // end for loop for histogram
            } // end of if strWhichColumn
        },3000, objElement);
    return false;
}

function endIntention(objElement) {
    //if (objElement == objClickedElement) {    // the timer is not cancelled do it
        clearTimeout(intTimer);
        //objTimer = null;
    //}
    //objClickedElement = null;
    return false;
}

function setRegistrationID() {
    var strID = G_DOM.getObject('RegistrationID').value.slice(5);
    G_DOM.getObject('RegistrationID').value = G_DOM.getObject('yearxact').value + Array(6-strID.length).join('0') + strID;
}

function doCheckin(objElement) {

    if (objElement.nodeName != 'THEAD') {
        var dtmNow = new Date();
        var objNewNode = document.createTextNode(dtmNow.toISOString().replace("T"," ").slice(0,-5));
        if (objElement.firstChild) {
            objElement.replaceChild(objNewNode, objElement.firstChild);
        } else {
            objElement.appendChild(objNewNode);
        }

    } else {
            alert('Bulk checkin not yet implemented');
    }

}

function showYear(objElement, intDirection) {
    var objYearNode;
    if (intDirection < 0) {
        objYearNode = objElement.nextSibling;   
    } else {
        objYearNode = objElement.previousSibling;   
    }

    var strValue;
    if (objYearNode.nodeName == '#text') {
        strValue = objYearNode.nodeValue;
        if (strValue == 'all' ) {
            objYearNode.nodeValue = '2010';
        } else {
            objYearNode.nodeValue = parseInt(objYearNode.nodeValue) + intDirection;
        }
        if (parseInt(objYearNode.nodeValue) < 2010) objYearNode.nodeValue = 'all';

    }
    G_STRYEAR = objYearNode.nodeValue;
    deleteCookie('listParticipantIDs');
    initSetup();
}

function showMenuOptions(objElement) {
    var objDiv = G_DOM.getObject("menudownload");
    var objHref = objDiv.getElementsByTagName("a")[0];
    objHref.href = 'data:text/csv;base64, ' + Base64.encode(getCsvStringFromTable());
    objDiv.style.display = "block";
    return false; // return false to prevent normal context menu popping up next to our custom menu
}

function hideMenuOptions(objElement) {
    G_DOM.getObject("menudownload").style.display = "none";
    return true;
}

function getCsvStringFromTable() {
    var arrLines = [];
    // we go through the table 
    var objTable = document.getElementsByTagName("table")[0];
    for (var i = 0, objRow; objRow = objTable.rows[i]; i++) { // cycle through all rows
        if (objRow.className != "hiddenrow") {
            var arrCells = [];
            for (var j = 0, objCell; objCell = objRow.cells[j]; j++) { // cycle through all cells in a row
                if ( j == 4 && objCell.nodeName.toUpperCase() == 'TD' ) {
                    var strChild = objCell.getAttribute("title");
                    arrCells.push('"' + strChild.replace(/,/g, '",') + '"' );
                }
                else if (objCell.firstChild) {
                    var strChild = objCell.firstChild.nodeValue.replace(/(\r\n|\n|\r)/gm," ");
                    strChild = strChild.replace(/\s+/g," ");
                    arrCells.push ( '"' + strChild + '"' );
                } else {
                    arrCells.push ( '""' );
                }
            }
            arrLines.push (  arrCells.join(',') );
        }
    } // for loop ends here
    return arrLines.join('\r\n');
}

function showPrivateOptions() {
    if((document.location.host).indexOf("test") == 0) {
        var dtmFestDate = getFestDate('');
        G_DOM.getObject('monthxact').value= ("00" + (dtmFestDate.getMonth() + 1)).slice(-2);
        G_DOM.getObject('dayxact').value= ("00" + dtmFestDate.getDate()).slice(-2);
        G_DOM.getObject('Amount').value = "0.00";
        var objConfirmationNo = G_DOM.getObject('CheckOrConfirmationNo');
        objConfirmationNo.value = "Phone No. - 10 DIGITS ONLY";
        objConfirmationNo.select();
        objConfirmationNo.focus();
    } else {
        alert("Feature works only in test site");
        return false;
    }
}


// This function moved from ASTFregisterhelp  - was unnecessarily complicated for that page
function getPrice( dblTotalIn, dtmNow ) {
// var dtmNow, dblTotal;
// The dblTotalIn is normally 0 but nonzero number may be supplied to calculate incremental cost when the total consists of 
// items purchased before and after early bird.
 switch (arguments.length) {
    case 1: dtmNow = validateDateEntry();
    case 2: break;
    default: throw new Error('illegal argument count')
 }
 
/* 
*/
// var dtmNow = validateDateEntry();
 if (dtmNow === false) return;
 var dtmEarlyBird; 
 var strYear = dtmNow.getFullYear();
 var dtmFestDate = getFestDate(strYear);
 //dtmEarlyBird.setDate(dtmEarlyBird.getDate() - 19);
 dtmEarlyBird = getEarlyBird(dtmFestDate);

 //var dtmNow = new Date(strYear, parseInt(strMonth) -1 , strDay);

 var intTier = 0, intTierDataEntry = 0;
 if (dtmNow > dtmEarlyBird) intTier = 1;
 if ((new Date()) > dtmEarlyBird) intTierDataEntry = 1;
 var objClasses = null;
 var objMilongas = null;
 var strFormName;
 if (document.location.href.indexOf('index.php') != -1) strFormName = 'ASTF2';
 else strFormName = 'tango1';
 objClasses = eval(document[strFormName]["classes[]"]);
 objMilongas = eval(document[strFormName]["milongas[]"]);

 var dblTotal = 0.00;
 var dblTotalDiff = 0.00;
 // 2014 new milonga was added, so to be backward compatible with old forms without this item, we drop one of the prices
  var arrPriceYearTier = G_arrPrice[strYear][intTier];
  var arrPriceYearTierDataEntry = G_arrPrice[strYear][intTierDataEntry];
  if (arrPriceYearTier.length - objMilongas.length - objClasses.length == 1) arrPriceYearTier.splice(7,1);
 // HACK ALERT!!!!
  for (i=0; i < objMilongas.length;  i++) {
    dblTotal += (objMilongas[i].checked ? arrPriceYearTier[i + 6]:0.00);
  }
  if (dblTotalIn > 0) {
      for (i=0; i < objMilongas.length;  i++) {
    dblTotalDiff += (((G_arrItems[i+6] == 'N') && objMilongas[i].checked ) ? arrPriceYearTierDataEntry[i+6]:0.00);
      }
  } 
  // if package price is exceeded, drop to the package price
  if (dblTotal > G_arrPkgMilongas[strYear][intTier]) dblTotal = G_arrPkgMilongas[strYear][intTier];
  for (var i=0; i < objClasses.length;  i++) {
    dblTotal += (objClasses[i].checked ? arrPriceYearTier[i]:0.00);
  }

  if (dblTotalIn > 0) {
      for (var i=0; i < objClasses.length;  i++) {
    dblTotalDiff += (((G_arrItems[i] == 'N') && objClasses[i].checked ) ? arrPriceYearTierDataEntry[i]:0.00);
      }
  } 
  // if package price is exceeded, drop to the package price
  if (dblTotal > G_arrPkgFull[strYear][intTier]) dblTotal = G_arrPkgFull[strYear][intTier];
  // the form ids are different in the registration page and data entry page
  if (document.location.href.indexOf('index.php') != -1) {
    dblTotal = (document.ASTF2["isStudent"].checked?0.7:(document.ASTF2["groupDiscount"].checked?0.9:1.0)) * dblTotal;
    dblTotal = (document.ASTF2["hostDiscount"].checked?(0.5):(1.0))*(dblTotal);
    dblTotal = document.ASTF2["fullAward"].checked?(0.0):(dblTotal);
    dblTotalDiff = (document.ASTF2["isStudent"].checked?0.7:(document.ASTF2["groupDiscount"].checked?0.9:1.0)) * dblTotalDiff;
    dblTotalDiff = (document.ASTF2["hostDiscount"].checked?(0.5):(1.0))*(dblTotalDiff);
    dblTotalDiff = document.ASTF2["fullAward"].checked?(0.0):(dblTotalDiff);
  } else {
  }
  // During the festival adding classes will cause the calculation to pick a higher number
  if ((dblTotalIn > 0) && ((dblTotalIn + dblTotalDiff) > dblTotal)) dblTotal = dblTotalIn + dblTotalDiff;
  return (dblTotal - dblTotalIn);

//                          (G_DOM.getObject('payment2').checked ? (' + FEE: $' + (intTotal*0.03).toFixed(2) ) :'');
}

// Autofill only a function needed for database interface, so moved here from ASTFregisterhelp.js
function autoFill(node, blnForce) {
    var nodeId = node.getAttribute("id");
    if (G_DOM.getObject('autofillonname').checked && G_BLNAUTOSUGGESTNAMES && 
              (nodeId == "lastName" || nodeId == "firstName") && node.value && (node.value.length > 2 || blnForce) ) {
        //&& (node.value.length % 3 == 0)) {
        var strText = node.value;
    var blnMatchFound = false;
        var intText = strText.toUpperCase().charCodeAt(0) - 65;
    var strName = '';
    var strNamePrevious = ' ';
    for (var i = (G_OBJROWINDICES[nodeId][intText]).length, objRow, objNameColumn  ; i > 0; --i) { 
            objRow = objTable.rows[G_OBJROWINDICES[nodeId][intText][i-1]];
            objNameColumn = objRow.cells[1];
            if (objNameColumn.firstChild) {
                strName = objNameColumn.firstChild.nodeValue;
                var arrName = strName.split(",");   
                var strName2 =(arrName.length > 1)?trim12(arrName[1]):''; 
        var strToMatch;
        if (nodeId == 'lastName') strToMatch = strName;
        else strToMatch = strName2;
                if ((strNamePrevious != strName) && (strToMatch.toUpperCase().slice(0, strText.length) == strText.toUpperCase())) {
            if ( (i > 1 )) { // are we on a 2nd match but not the last?
            // the pop-up needs complete information in case of phones, there is no real estate to see the textboxes when this & keyboard are up
            strNamePrevious = strName;
            if (true == confirm("Found '" + strName + "' for " + nodeId + "s starting with '" + strText + 
                        "'.\nSelect 'OK' to accept this choice." +
                        "\n'Cancel' to skip to next choice." +
                        "\n OR, if no more found, bail out, and type a couple of more letters - " +
                        "in which case, backspace to delete suggested ending first.") ) {
                blnMatchFound = true;
                
                fillFormFromRow(objRow);
                var intStartOffset = strText.length;
                var intEndOffset = strName.length;
                if (node.createTextRange) {
                var selRange = node.createTextRange();
                selRange.collapse = true;
                selRange.moveStart('character', intStartOffset);
                selRange.moveEnd('character', intEndOffset);
                selRange.select();
                } else if (node.setSelectionRange) {
                node.setSelectionRange(intStartOffset, intEndOffset);
                } else if (node.setSelectionStart) {
                node.setSelectionStart = intStartOffset;
                node.setSelectionEnd = intEndOffset;
                }
                break;
            }
            } // end of display confirm 
                } // end match for name 
            } // end if check on name column firstchild
        } // end for loop
    if (blnMatchFound && typeof checkinhelp != 'undefined') { checkinhelp.toggleForm(1); return false;}
    } // end if blnAutoSuggestNames
    if (!G_BLNAUTOSUGGESTNAMES) G_BLNAUTOSUGGESTNAMES=true;
    return true;
 
}

function validateDateEntry() {
var varReturn = false;
var strYear, strMonth, strDay;
 
 var objYear = G_DOM.getObject('yearregn');
 strYear = objYear.options[objYear.selectedIndex].text;
 
 var strMonth = G_DOM.getObject('monthregn').value;
 if ((strMonth.length > 0) && (parseInt(strMonth) > 12
    ||  parseInt(strMonth) < 1)) {
 alert("Month can only be a number between 1 and 12");
 return varReturn;
 }

var intLeapYear = 0;
if (strYear) intLeapYear = ((strYear % 4 == 0)  ^ (strYear % 100 == 0) ^ (strYear % 400 == 0 )) ? (1): (0);
 var strDay = G_DOM.getObject('dayregn').value;
 var intMaxDays = parseInt(G_arrDaysInMonth[intLeapYear][parseInt(strMonth)-1]);
 if ((strMonth.length > 0) && (strDay.length > 0) && 
       ((parseInt(strDay) > intMaxDays) ||  parseInt(strDay) < 1)) {
        alert("Day can only be a number between 1 and " + intMaxDays);
        return varReturn;
 }

 //varReturn = new Date(strYear, parseInt(strMonth) -1 , strDay,0,0,0);
 //return varReturn;
 return (new Date(strYear, parseInt(strMonth) -1 , strDay));
 //return {'strYear': strYear, 'strMonth': strMonth, 'strDay': strDay};

}

function displayAmounts (dblTotal, dblDue,  dblPaid) {
    G_DOM.getObject('Amt').firstChild.data = "TOTAL: $" + dblTotal.toFixed(2) ;
    //var strData = (G_DOM.getObject('AmtPaid')).firstChild.data;
    //strData = strData.slice(strData.indexOf('$') + 1);
    if (document.location.href.indexOf('index.php') != -1) {
        G_DOM.getObject('AmtDue').value = dblDue.toFixed(2) ;
        G_DOM.getObject('AmtPaid').firstChild.data = 'PAID: $' + dblPaid.toFixed(2);
    }
}   

function getTotal() {
    var dblDiffTotal = getPrice(G_dblTotal);
    displayAmounts(G_dblTotal + dblDiffTotal, G_dblDue + dblDiffTotal);
}

function isValidChkTxnNo() {
var blnValid = false;
// The 6th radio button is paypal confirm 12th is paypal refund confirm, both manually entered. Only check Txn# for these
if (!(document.ASTF1["ActivityType"][5].checked) && !(document.ASTF1["ActivityType"][11].checked)) return true;
objElement = G_DOM.getObject("CheckOrConfirmationNo");
if ( isNaN(objElement.value)) blnValid = true;
if (!blnValid) alert("You probably entered a registration ID in the Chq/Txn# field.\n"
            +"Paypal transaction ID is alphanumeric and l-o-n-g.\n"
            +"Avoid typos. Copy-paste the code from Paypal website \n"
            +"into the box instead of typing in.");
return blnValid;
}

// This is kind of like autoFill, but different in that only when typed in full, and Enter pressed, row matches will be searched
// This will be used for email and phone number, in an attempt to make these fields confidential
function matchAndFill(node) {
    var nodeId = node.getAttribute("id");
    var strToMatch = XXH(node.value);
    var arrToSearch = G_OBJROWINDICES[nodeId];
    var intStartIndex = arrToSearch.length;

    var intIndex = arrToSearch.lastIndexOf(strToMatch, intStartIndex);
    while (intIndex != -1) {
    objRow = objTable.rows[intIndex + 1];
    objNameColumn = objRow.cells[1];
        if (objNameColumn.firstChild) {
            strName = objNameColumn.firstChild.nodeValue;
        if (true == confirm("Found '" + strName +  
            "'.\nSelect 'OK' to keep this choice." +
            "\n'Cancel' to see next choice (or search ends if no more matches).") ) {
        fillFormFromRow(objRow);
        if (typeof checkinhelp != 'undefined') { checkinhelp.toggleForm(1);}
        return false;
        }
    }
    intStartIndex = intIndex - 1;
        intIndex = arrToSearch.lastIndexOf(strToMatch, intStartIndex);

    } // end while looking through array backwards
    return false;
}
