 var G_STRGROUPDISCOUNTCODE = "BOLEO";
 var G_arrPrice = {
            "2010":[
                    [25,25,25,25,25,25,20,0,25,20],
                    [30,30,30,30,30,30,22,0,27,22]
                ],
            "2011":[
                    [25,25,25,25,25,25,20,0,25,20],
                    [30,30,30,30,30,30,22,0,27,22]
                ],
            "2012":[
                    [25,25,25,25,25,25,22,0,25,22],
                    [30,30,30,30,30,30,25,0,27,25]
                ],
            "2013":[
                    [25,25,25,25,25,25,22,0,25,22],
                    [30,30,30,30,30,30,25,0,27,25]
                ],
            "2014":[
                    [30,30,30,30,30,30,25,25,35,40],
                    [35,35,35,35,35,35,25,25,35,40]
                ],
            "2015":[
                    [30,30,30,30,30,30,25,25,35,40],
                    [35,35,35,35,35,35,25,25,35,40]
                ]
        };
 var G_arrPkgMilongas = {
            "2010": [57, 65] ,
            "2011": [57, 65] ,
            "2012": [57, 65] ,
            "2013": [57, 65] ,
            "2014": [105, 125] ,
            "2015": [105, 125] 
            };
 var G_arrPkgFull = {
            "2010": [165, 190] ,
            "2011": [165, 190] ,
            "2012": [165, 190] ,
            "2013": [165, 190] ,
            "2014": [235, 275] ,
            "2015": [235, 275] 
            };
 var G_arrDaysInMonth = [
                    [31,28,31,30,31,30,31,31,30,31,30,31],
                    [31,29,31,30,31,30,31,31,30,31,30,31]
                ];

var G_DOM = { 
        'getObject' : function (strId) { return document.getElementById(strId); },
        'getTagObjects' : function(objElement, strId) { return objElement.getElementsByTagName(strId); },
    };

var objACell = function(objDOMcell) {
    var that = {};  //empty object fresh instance each time?
    that.getValue = function() {
        return ( (objDOMcell.firstChild) ? (objDOMcell.firstChild.nodeValue) : '' );
    };
    that.getValues = function() {
        var arrReturn = [];
        for (var i=0; i < objDOMcell.childNodes.length; ++i) {
            if (objDOMcell.childNodes[i].nodeType == 3) {
                arrReturn.push(objDOMcell.childNodes[i].nodeValue);
            }
        }
        return arrReturn;
    };
    that.Equals = function(strText) {
        return (objDOMcell.firstChild !== null) && (objDOMcell.firstChild.nodeValue == strText);
    };
    return that;
}

function interceptKeys(evt) {
  
  var evt = (evt)? evt: ((window.event)? window.event:null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ( evt.keyCode == 13 ) { 
    setSaneInput(node);
    var objNext = node;
        do  {
        if (!objNext.nextSibling ) {
        objNext = objNext.parentNode.nextSibling;
        while (objNext && objNext.nodeName != "FIELDSET" ) objNext = objNext.nextSibling; 
        if (objNext) objNext = objNext.firstChild; 
        }
        if (objNext) objNext = objNext.nextSibling;
        else break;
    } while (objNext.nodeName != "INPUT" || (typeof objNext.type != 'undefined' && !objNext.type.match(/text|radio|checkbox/)) );
        if (objNext) {
    objNext.focus();
    objNext.select();
    }
    return false;
  } else if (evt.keyCode == 8) {
      G_BLNAUTOSUGGESTNAMES=false;
      return true;
  } else if ((evt.keyCode >= 65 && evt.keyCode <= 90) ||  // letters 
        (evt.keyCode >= 48 && evt.keyCode <= 57) || // number keys 
        (evt.keyCode >= 96 && evt.keyCode <= 105) || // number pad numbers
        evt.keyCode == 189) { // Dash key
      if (typeof autoFill == 'function') {
    intTimer = window.setTimeout(
        function(node)  {
            autoFill(node, false);
        },100, node);
      }
     return true;
  } else if (evt.keyCode == 39) {
    G_BLNAUTOSUGGESTNAMES=true;
      if (typeof autoFill == 'function') {
    intTimer = window.setTimeout(
        function(node)  {
            autoFill(node, true);
        },100, node);
      }
     return true;
  } 
}

document.onkeydown = interceptKeys

function isNumberKey(evt) {
         var charCode = (evt.which) ? evt.which : event.keyCode;
         if (charCode==8) return true; //allow backspace
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

         return true;  
}
function isFloatNumberKey(evt) {
         var charCode = (evt.which) ? evt.which : event.keyCode;
         if ( (charCode==8) || (charCode==46) ) return true; //allow backspace and decimal point
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

         return true;  
}

// This function added as a step to separate total calculation on the registration web page, it is much simpler than on the DB interface -- Partha 20140312
function setTotal() {
 var dtmEarlyBird = getFestDate('');
 var strYear = dtmEarlyBird.getFullYear();
 dtmEarlyBird.setDate(dtmEarlyBird.getDate() - 19);
 var dtmNow = new Date();

 var intTier = 0;
 if (dtmNow > dtmEarlyBird) intTier = 1;
 var objClasses = eval(document.tango1["classes[]"]);
 var objMilongas = eval(document.tango1["milongas[]"]);
 var intTotal = 0.00;
// 2014 new milonga was added, so to be backward compatible with old forms without this item, we drop one of the prices
  var arrPriceYearTier = G_arrPrice[strYear][intTier];
  if (arrPriceYearTier.length - objMilongas.length - objClasses.length == 1) arrPriceYearTier.splice(7,1);
// HACK ALERT!!!!
  for (i=0; i < objMilongas.length;  i++) {
    intTotal += (objMilongas[i].checked ? arrPriceYearTier[i + 6]:0.00);
  }
  // if package price is exceeded, drop to the package price
  if (intTotal > G_arrPkgMilongas[strYear][intTier]) intTotal = G_arrPkgMilongas[strYear][intTier];
  for (var i=0; i < objClasses.length;  i++) {
    intTotal += (objClasses[i].checked ? arrPriceYearTier[i]:0.00);
  }
  // if package price is exceeded, drop to the package price
  if (intTotal > G_arrPkgFull[strYear][intTier]) intTotal = G_arrPkgFull[strYear][intTier];
  // the form ids are different in the registration page and data entry page
  intTotal = (document.tango1["isStudent"].checked?0.7:(((document.tango1["groupDiscount"].value).toUpperCase() == G_STRGROUPDISCOUNTCODE)?0.9:1.0)) * intTotal;
  var objElem = G_DOM.getObject('Amt');
  objElem.firstChild.data = "TOTAL: $" + intTotal.toFixed(2) ;

}

function isWebURL(strElements) { // ought to keep the less determined spammers out
var blnNoUrls = true;
var varRegEx = new RegExp();
varRegEx.compile("^((ht|f)tp(s?)\:\/\/|~/|/)?[a-z0-9-_]+\\.[a-z0-9-_%&\?\/.=]+$");
if (strElements == 'anytext') { // check if all text fields need to be verified
    arrElements = document.getElementsByTagName("textarea"); // address is more likely to have url
    for (var intElem = 0; intElem < arrElements.length; intElem++) {
        if (varRegEx.test(arrElements[intElem].value.toLowerCase()) )
             {blnNoUrls = false; break; }
    }
    arrElements = document.getElementsByTagName("input");
    for (intElem = 0; intElem < arrElements.length; intElem++) {
        if (!blnNoUrls) break;
        if (arrElements[intElem].type == 'text' && varRegEx.test(arrElements[intElem].value.toLowerCase()) )
             {blnNoUrls = false; break; }
    }
 }  else { // just check selected elements
     arrElements = strElements.split(",");
     for (var i=0; i < arrElements.length; i++) {
        if (varRegEx.test(G_DOM.getObject(arrElements[i]).value)) 
                 {blnNoUrls = false; break;}
     }
 }
 return blnNoUrls;
}


function isEmptyCheck(strElements) {
 var strMsg = "";
 if (strElements == 'anytext') { // check if all text fields need to be verified
    arrElements = document.getElementsByTagName("input");
    for (var intElem = 0; intElem < arrElements.length; intElem++) {
        if (arrElements[intElem].type == 'text' && arrElements[intElem].value.length == 0)
            strMsg += arrElements[intElem].id + " cannot be blank \n";
    }
    arrElements = document.getElementsByTagName("textarea");
    for (intElem = 0; intElem < arrElements.length; intElem++) {
        if (arrElements[intElem].value.length == 0)
            strMsg += arrElements[intElem].id + " cannot be blank \n";
    }
 }  else { // just check selected elements
     arrElements = strElements.split(",");
     for (var i=0; i < arrElements.length; i++) {
        if (G_DOM.getObject(arrElements[i]).value.length == 0) 
                 strMsg += arrElements[i] + " cannot be blank \n";
     }
}
if (strMsg.length > 0) {alert(strMsg); return false;} else {return true;}
}

function isValidEmail() {
var blnValid = false;
var varRegEx = new RegExp();
varRegEx.compile("^[a-z0-9._%+-]+@(?:[a-z0-9-]+\.)+[a-z]{2,4}$");
objElement = G_DOM.getObject("email");
if ( varRegEx.test(objElement.value)) blnValid = true;
if (!blnValid) alert('Correct email format please');
return blnValid;
}

function isSelectedMinOne() {
var blnSelected = false;
arrElements = document.getElementsByTagName("input");
for (var intElem = 0; intElem < arrElements.length; intElem++) {
    if ( arrElements[intElem].type == 'checkbox' && arrElements[intElem].id != 'isStudent' && arrElements[intElem].checked )
        { blnSelected = true; break;}
}
if (!blnSelected) alert('Nothing selected');
return blnSelected;
}

function getFestDate(strYear) {
var varThisYear;
if ((strYear == '') || (strYear == 'all') || isNaN(strYear)) {
    var dtmNow = new Date();
    varThisYear = dtmNow.getFullYear();
} else {
    varThisYear = strYear;
}
//if (varThisYear == 2013) return ( new Date(2013,2,22));

var dtmFest = new Date(varThisYear,3,0,0,0,0); // 1st Apr this year
var j = 0;  // added to avoid infinite loops
do { // then back up
    dtmFest.setDate (dtmFest.getDate() - 1);
    j = j + 1;
        if (j > 7) break;
    } while (dtmFest.getDay() != 5)  // until we hit a Friday
if (j > 7) return null;
if (strYear != '') return dtmFest;  // when year is explicitly stated we don't go any further
var dtmFestEnd = new Date(dtmFest.getTime());
dtmFestEnd.setDate(dtmFestEnd.getDate() + 3);
if (dtmNow < dtmFestEnd) return dtmFest;
// the fest we are looking for is next years
dtmFest = new Date(varThisYear+1,3,0,0,0,0); // 1st Apr next year
//alert ('1st Apr next year is ' + dtmFest);
do {
    dtmFest.setDate (dtmFest.getDate() - 1);
    } while (dtmFest.getDay() != 5)
return dtmFest;
}

function setSaneInput(objNode) {

var strNodeId = objNode.getAttribute("id");
var strValue = trim12(objNode.value);
if ( /(first|mid|last)Name/.test(strNodeId) ) {
    // some of those spanish portuguese italian origin names don't want to capitalize the leading di, de, do

    // Not a word, not a space? Lose it.
    strValue = strValue.replace(/[^A-Za-z -]/g,'');
    var arrSplit = new Array();
    if ( /d[aeiou][s]*[A-Za-z\s]+/.test(strValue) ) {
        arrSplit = strValue.split(" ");
        strValue = arrSplit[arrSplit.length - 1];
    } 
    
    if ( /[Mm]c[A-Za-z]+/.test(strValue) ) {
        strValue = 'Mc' + strValue.substr(2,1).toUpperCase() + strValue.substring(3).toLowerCase();
    } else {
        strValue = strValue.toLowerCase().replace(/\b[a-z]/g, 
                                        function(letter) {
                                            return letter.toUpperCase();
                                        });
    }
    if (arrSplit.length > 0) {
        arrSplit[arrSplit.length - 1] = strValue;
        strValue = arrSplit.join(" ");
    } 
    
    if (strNodeId == 'midName') strValue = strValue.substring(0,1);
    objNode.value = strValue;

}
else if (strNodeId == 'email') {
    objNode.value = trim12(objNode.value.toLowerCase());
    if (typeof matchAndFill == 'function') {
        return  matchAndFill(objNode);
    }
}
else if (strNodeId == 'phonenumber') {
    strValue = strValue.replace(/\D/g,'');
    if (strValue.length != 10 ) {
        alert('10 digits please');
        objNode.value = '';
    } else {
        objNode.value = strValue;
    }
} 
else if (strNodeId == 'city') {
    strValue = trim12(objNode.value.toUpperCase());
    objNode.value = strValue.replace(/[\d,\(\)\[\]{}]/g,'');
}
else if (strNodeId == 'state') {
    strValue = trim12(objNode.value.toUpperCase());
    strValue = strValue.replace(/[\d,\(\)\-\[\]{}]/g,'');
    objNode.value = strValue.slice(0,2);
}
else if (strNodeId == 'zip') {
    strValue = trim12(objNode.value.toUpperCase());  //Canada zip has letters
    strValue = strValue.slice(0,6); // some anal US folks enter the - and 4 digits in the end, strip it, that allows Canada zip codes too
    objNode.value = strValue.replace(/\-/g,'');
}
else {
    objNode.value = trim12(objNode.value.toUpperCase());
}

}

// copied from blog.stevenlevithan.com/archives/faster-trim-javascript
function trim12 (str) {
    var str = str.replace(/^\s\s*/, ''),
        ws = /\s/,
        i = str.length;
    while (ws.test(str.charAt(--i)));
    return str.slice(0, i + 1);
}

function getUrlVar (strVar) {
    var strValue = "";
    var strUrl = document.location.href;
    var intIndex = strUrl.indexOf(strVar + "=");
    if (intIndex != -1) {
        strValue = strUrl.slice(intIndex + strVar.length + 1);
        var intEndIndex = strValue.indexOf("&");
        if (intEndIndex != -1) strValue = strValue.substring(0,intEndIndex);
    }
    return strValue;
}

function isPaymentSelected() {

    var blnSelected = G_DOM.getObject('payment1').checked || G_DOM.getObject('payment2').checked;
    if (!blnSelected) alert('Select a mode of payment');
    return blnSelected;
}

function getEarlyBird(dtmFestDate) {
    var dtmEarlyBird = new Date(dtmFestDate.getTime());
    dtmEarlyBird.setDate(dtmEarlyBird.getDate() - 19);
    return dtmEarlyBird;
}
