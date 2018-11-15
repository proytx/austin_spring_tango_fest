/**
*
*  Base64 encode / decode
*  http://www.webtoolkit.info/
*
**/
 
var Base64 = {
 
    // private property
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
 
    // public method for encoding
    encode : function (input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;
 
        input = Base64._utf8_encode(input);
 
        while (i < input.length) {
 
            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);
 
            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;
 
            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }
 
            output = output +
            this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
            this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
 
        }
 
        return output;
    },
 
    // public method for decoding
    decode : function (input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;
 
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
 
        while (i < input.length) {
 
            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));
 
            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;
 
            output = output + String.fromCharCode(chr1);
 
            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }
 
        }
 
        output = Base64._utf8_decode(output);
 
        return output;
 
    },
 
    // private method for UTF-8 encoding
    _utf8_encode : function (string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";
 
        for (var n = 0; n < string.length; n++) {
 
            var c = string.charCodeAt(n);
 
            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }
 
        }
 
        return utftext;
    },
 
    // private method for UTF-8 decoding
    _utf8_decode : function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;
 
        while ( i < utftext.length ) {
 
            c = utftext.charCodeAt(i);
 
            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
 
        }
 
        return string;
    }
 
};

/*
 * After looking into many javascript implementations, found
 * this to the most easily unserstandable readable code, although
 * pieroxy.net/blog/pages/lz-string seems th be more active. 
 * Modified the output / input streams to try out efficient alternatives
 * -- Partha
 */
/*
  lzwjs.js - Javascript implementation of LZW compress and decompress algorithm
  Copyright (C) 2009 Mark Lomas

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Used to write values represented by a user specified number of bits into 
// a 'bytestream' array.
// renamed from OutStream
function OutStreamBitpacked()
{
    this.bytestream = new Array();
    this.offset = 0;
    
    this.WriteBit = function(val)
    {
       	//this.bytestream[this.offset>>>3] |= val << (this.offset & 7);
       	//this.bytestream[(this.offset/7)>>0] |= val << (this.offset % 7); // UTF-8 backward compatible with 7-bit ASCII
       	this.bytestream[(this.offset/6)>>0] |= val << (this.offset % 6); // UTF-8 backward compatible with 7-bit ASCII
        this.offset++;
    };

    this.Write = function(val, numBits)
    {
        // Write LSB -> MSB
        for(var i = 0; i < numBits; ++i)
            this.WriteBit((val >>> i) & 1);
    };

    this.asString = function()
    {
	//return this.bytestream.map(function(a){ return String.fromCharCode(parseInt(a,16)) }).join('');
	//return this.bytestream.map(function(a){ return String.fromCharCode(a) }).join('');
	return this.bytestream.map(
			function(a){ 
				return "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".charAt(a);
			}
		).join('');
    }


}

// Used to read values represented by a user specified number of bits from 
// a 'bytestream' array.'
// renamed from InStream
function InStreamBitpacked(bytestream, bitcount)
{
	this.bytestream = bytestream;
	this.bitcount = bitcount;
	this.offset = 0;

	this.ReadBit = function()
	{
	    //var tmp = this.bytestream[this.offset>>>3] >> (this.offset & 7);
	    //var tmp = this.bytestream[(this.offset/7)>>0] >> (this.offset % 7); // use 7-bit, UTF-8 backward compatible with ASCII
	    var tmp = this.bytestream[(this.offset/6)>>0] >> (this.offset % 6); // use 7-bit, UTF-8 backward compatible with ASCII
	    this.offset++;
	    return tmp&1;
	};

	this.Read = function(numBits)
	{
	    if((this.offset + numBits) > this.bitcount)
	        return null;

	    // Read LSB -> MSB
	    var val = 0;
	    for(var i = 0; i < numBits; ++i)
	        val |= this.ReadBit() << i;

	    return val;
	}
}


function LZWCompressor(outstream)
{
        this.output = outstream;

	// Hashtable dictionary used by compressor
	this.CompressDictionary = function() 
	{
	    this.hashtable = new Object();
	    this.nextcode = 0;

	    // Populate table with all possible character codes.
	    for(var i = 0; i < 256; ++i)
	    {
	        var str = String.fromCharCode(i);
	        this.hashtable[str] = this.nextcode++;
	    };    


	    this.Exists = function(str)
	    {
	        return (this.hashtable.hasOwnProperty(str));
	    };

	    this.Insert = function(str)
	    {
	        var numBits = this.ValSizeInBits();
	        this.hashtable[str] = this.nextcode++;
	        return numBits;
	    };

	    this.Lookup = function(str)
	    {
	        return (this.hashtable[str]);
	    };

	    this.ValSizeInBits = function()
	    {
	        // How many bits are we currently using to represent values?
	        var log2 = Math.log(this.nextcode + 1)/Math.LN2;
	        return Math.ceil(log2);
	    }
	};


	// LZW compression algorithm. See http://en.wikipedia.org/wiki/LZW
	this.compress = function(str)
	{
	   var length = str.length;
	   if(length == 0)
	       return output.bytestream;

	   var dict = new this.CompressDictionary();
	   var numBits = dict.ValSizeInBits();
	   var w = "";
	   for(var i = 0; i < length; ++i)
	   {
	       var c = str.charAt(i);
	       if(dict.Exists(w + c))
	       {
	           w = w + c;
	       }
	       else
	       {
	           numBits = dict.Insert(w + c);
	           this.output.Write(dict.Lookup(w), numBits); // Looks-up null on first interation.
	           w = c;
	       }
	   }
	   this.output.Write(dict.Lookup(w), numBits);
	};

} // end of LZWCompressor

function LZWDecompressor(instream)
{
	this.input = instream;

	this.DecompressDictionary = function()
	{
	    this.revhashtable = new Array();
	    this.nextcode = 0;

	    // Populate table with all possible character codes.
	    for(var i = 0; i < 256; ++i)
	    {
	        this.revhashtable[this.nextcode++] = String.fromCharCode(i);
  	    };

	    this.numBits = 9;

	    this.Size = function()
	    {
	        return (this.nextcode);
	    };

	    this.Insert = function(str)
	    {
	        this.revhashtable[this.nextcode++] = str;

	        // How many bits are we currently using to represent values?
		// Look ahead one value because the decompressor lags one iteration
		// behind the compressor.
	        var log2 = Math.log(this.nextcode + 2)/Math.LN2;
	        this.numBits = Math.ceil(log2);
	        return this.numBits;
	    };

	    this.LookupIndex = function(idx)
	    {
		return this.revhashtable[idx];
	    };

	    this.ValSizeInBits = function()
	    {
	        return this.numBits;
	    }
	};

	// LZW decompression algorithm. See http://en.wikipedia.org/wiki/LZW
	// Correctly handles the 'anomolous' case of 
	// character/string/character/string/character (with the same character 
	// for each character and string for each string).
	this.decompress = function(data, bitcount)
	{
	   if(bitcount == 0)
	       return "";

	   var dict = new this.DecompressDictionary();
	   var numBits = dict.ValSizeInBits();

	   var k = this.input.Read(numBits);
	   var output = String.fromCharCode(k);
	   var w = output;
	   var entry = "";

	   while ((k = this.input.Read(numBits)) != null)
	   {
	      if (k < dict.Size()) // is it in the dictionary?
	          entry = dict.LookupIndex(k); // Get corresponding string.
	      else 
	          entry = w + w.charAt(0);
	
	      output += entry;
	      numBits = dict.Insert(w + entry.charAt(0));
	      w = entry;
	   }
	
	   return output;
	};

} // end of LZWDecompressor

function getRegistrationData(objDocument, strElement, strTag) {
	var objIframe = objDocument.getElementById('regdata');
	if (objIframe) {
		var objIframeDocument = objIframe.contentDocument ? objIframe.contentDocument : objIframe.contentWindow.document;
		var objDiv = objIframeDocument.getElementById(strElement);
		var strToDecode = objDiv.innerHTML;
		var intTableBitCount = objDiv.getAttribute('bitoffset');
		//var strDecoded = objCode.innerHTML;
		var arrToDecode = new Array(strToDecode.length);
		for (var ii=0; ii < strToDecode.length; ++ii) { 
			arrToDecode[ii] = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".indexOf(strToDecode[ii]);
		}
		var objInStream = new InStreamBitpacked(arrToDecode,intTableBitCount);
		var objDecoder = new LZWDecompressor(objInStream);
		var strHtml = objDecoder.decompress(arrToDecode,intTableBitCount);
		var objDiv = objIframeDocument.createElement('div');
		objDiv.innerHTML = strHtml;
		if (strTag) 
			return objDiv.getElementsByTagName(strTag)[0];
		else
			return strHtml;
	}
	return null;
}

function storeTable(objDocument) {
	var objIframe = objDocument.getElementById('regdata');
	if (objIframe) {
	//objTable.parentNode.replaceChild(objIframe.contentDocument.createElement('span'),objTable);
		var objIframeDocument = objIframe.contentDocument ? objIframe.contentDocument : objIframe.contentWindow.document;
		var objCompressedDiv = objIframeDocument.createElement('div');
		var objOutStream = new OutStreamBitpacked();
		var objCompressor = new LZWCompressor(objOutStream);
		(new LZWCompressor(objOutStream)).compress(objTable.outerHTML);
		objCompressor.compress(objTable.outerHTML);
		strCodedTable = objOutStream.asString();
		var objTextNode = objIframeDocument.createTextNode(strCodedTable);
		objCompressedDiv.appendChild(objTextNode);
		objIframeDocument.body.appendChild(objCompressedDiv);	
	}
}
var PRIME32_1 = parseInt( '2654435761' );
var PRIME32_2 = parseInt( '2246822519' );
var PRIME32_3 = parseInt( '3266489917' );
var PRIME32_4 = parseInt(  '668265263' );
var PRIME32_5 = parseInt(  '374761393' );


//Started off with Pierre Curto's javascript implementation, but then backed off
//with a simpler version copied from Stuart Herbert's PHP version. After 3 days
//of not matching javascript and C hashes, went back to copy the Multiply Uint32
//but had to adapt it to my own
function XXH (input, seed ) {
	var p = 0;
	var len = input.length;
	var bEnd = p + len;
	var h32;
	seed = seed || 0;

	if (len == 0) return '';
	if (len >= 16)   
	{
		var limit = bEnd - 16;
		var v1 = limitToUint32(seed + PRIME32_1 + PRIME32_2);
		var v2 = limitToUint32(seed + PRIME32_2);
		var v3 = limitToUint32(seed);
		var v4 = limitToUint32(seed - PRIME32_1);
		do
		{
			v1 = limitToUint32( v1 + multiplyUint32(PRIME32_2 , (
				(input.charCodeAt(p+1) << 8) | input.charCodeAt(p) | 
				(input.charCodeAt(p+3) << 24) | (input.charCodeAt(p+2) << 16)
			)));
			v1 = rotateLeftUint32 (v1, 13);
			v1 = multiplyUint32( v1 , PRIME32_1 );
			p += 4;
			v2 = limitToUint32( v2 + multiplyUint32(PRIME32_2 , (
				(input.charCodeAt(p+1) << 8) | input.charCodeAt(p) |
				(input.charCodeAt(p+3) << 24) | (input.charCodeAt(p+2) << 16)
			)));
			v2 = rotateLeftUint32 (v2, 13);
			v2 = multiplyUint32( v2 , PRIME32_1 );
			p += 4;
			v3 = limitToUint32( v3 + multiplyUint32(PRIME32_2 , (
				(input.charCodeAt(p+1) << 8) | input.charCodeAt(p) |
				(input.charCodeAt(p+3) << 24) | (input.charCodeAt(p+2) << 16)
			)));
			v3 = rotateLeftUint32 (v3, 13);
			v3 = multiplyUint32( v3 , PRIME32_1 );
			p += 4;
			v4 = limitToUint32( v4 + multiplyUint32(PRIME32_2 , (
				(input.charCodeAt(p+1) << 8) | input.charCodeAt(p) |
				(input.charCodeAt(p+3) << 24) | (input.charCodeAt(p+2) << 16)
			)));
			v4 = rotateLeftUint32 (v4, 13);
			v4 = multiplyUint32( v4 , PRIME32_1 );
			p += 4;
		} while (p <= limit);
		h32 = limitToUint32(rotateLeftUint32 (v1, 1) + rotateLeftUint32 (v2, 7) + rotateLeftUint32 (v3, 12) + rotateLeftUint32 (v4, 18));
	} 
	else
	{
		h32  = limitToUint32(seed + PRIME32_5 );
	} 
	h32 = limitToUint32(h32 + len);
	while (p <= bEnd - 4)
	{
		h32 = limitToUint32( h32 + multiplyUint32(PRIME32_3 , (
					(input.charCodeAt(p+1) << 8) | input.charCodeAt(p) | 
					(input.charCodeAt(p+3) << 24) | (input.charCodeAt(p+2) << 16)
				)));
		h32 = multiplyUint32(rotateLeftUint32 (h32, 17) , PRIME32_4);
		p += 4;
	}

	while (p < bEnd)
	{
		h32 = limitToUint32( h32 + multiplyUint32(PRIME32_5 , input.charCodeAt(p++)));
		h32 = multiplyUint32(rotateLeftUint32 (h32, 11) , PRIME32_1);
	}


	h32 = shiftRightXor(h32,15);
	h32 = multiplyUint32( h32 , PRIME32_2);

	h32 = shiftRightXor(h32,13);
	h32 = multiplyUint32( h32 , PRIME32_3);

	h32 = shiftRightXor(h32,16);

	return h32.toString();

}

function limitToUint32 (jsInt) {
	// Didn't think the negative sign was needed, but tests proved otherwise
	if (jsInt < 0) return -(~jsInt + 1);
	var strNumber = jsInt.toString(2);
	if (strNumber.length < 32) strNumber = Array(33-strNumber.length).join('0') + strNumber;
	return parseInt(strNumber.slice(-32), 2);
}

function rotateLeftUint32 (jsInt, npos) {
	// String base rotate left to dwell in a world where there is no negativity in integers 
	// Could it be faster than bit shift?
	var strNumber = jsInt.toString(2);
	if (strNumber.length < 32) strNumber = Array(33-strNumber.length).join('0') + strNumber;
	if (strNumber.length > 32) strNumber = strNumber.slice(-32);
	return parseInt(strNumber.slice(npos) + strNumber.slice(0,npos), 2);
}

function shiftRightXor (jsInt, npos) {
	// String base shift right and xor to avoid javascript interpreting MSB as sign bit
	// Call it code obfuscation
	if (npos <= 0) return 0;
	var strNumber = jsInt.toString(2);
	if (strNumber.length < 32) strNumber = Array(33-strNumber.length).join('0') + strNumber;
	if (strNumber.length > 32) strNumber = strNumber.slice(-32);
	var strNumberRightShifted = strNumber.slice(0,32-npos);
	if (strNumber.charAt(0) == '1') {
		strPartXor = (parseInt(strNumber.slice(1),2) ^ parseInt(strNumberRightShifted,2)).toString(2);
		return parseInt('1' + Array(32-strPartXor.length).join('0') + strPartXor, 2);
		
	} else {
		return (parseInt(strNumberRightShifted,2) ^ jsInt) ;
	}
}

function multiplyUint32 (jsInt1, jsInt2) {
	// Freakin' javascript round-off error for large integer multiplication !!!
	// Note when a 32bit int is split into 2 16-bit hi and lo, the multiply can be expressed
	// as (2^16 * hi1 + lo1) ( 2^16 * hi2 + lo2) = (2^32*hi1*hi2) + 2^16 * (hi1 * lo2 + h12 * lo1)
	// + (lo2 * lo1). Note, the first term can be safely dropped, since the Uint32 result will
	// ignore it
	var lo1 = (jsInt1 & 0xFFFF) >>> 0;
	var hi1 = Math.floor(jsInt1 / 0x10000);
	var lo2 = (jsInt2 & 0xFFFF) >>> 0;
	var hi2 = Math.floor(jsInt2 / 0x10000);
	var reshi, reslo;
	reslo = lo2 * lo1;
	reshi = reslo >>> 16;
	reshi += hi2 * lo1;
	reshi &= 0xFFFF;
	reshi += lo2 * hi1;
	reshi &= 0xFFFF;
	return parseInt( ('0000'+reshi.toString(16)).slice(-4) + ('0000'+(reslo & 0xFFFF).toString(16)).slice(-4), 16);
	
}

