<?php
        error_reporting(E_ALL & ~E_NOTICE);
        ini_set('log_errors','1');
        ini_set('display_errors','0');
	//include(dirname(dirname(dirname(__FILE__)))."/php/ASTFDatesOptions.php");
class LZW
{
    private $strOut="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    private $bitCount = 0;
    //private $func = function($i){return $this->strOut[$i];};
    function compress($unc) {
        $i;$c;$wc;
        $w = "";
        $dictionary = array();
        $result = array();
        $dictSize = 256;
	$bitoffset = 0;
	$startpos = array();
        for ($i = 0; $i < 256; $i += 1) {
            $dictionary[chr($i)] = $i;
        }
	//print strlen($unc);
        for ($i = 0; $i < strlen($unc); $i++) {
            $c = $unc[$i];
            $wc = $w.$c;
            if (array_key_exists($wc, $dictionary)) {
                $w = $wc;
            } else {
                $numbits = ceil(log($dictSize+1,2.0));
		$val = $dictionary[$w];
		$posInsert = (int)($bitoffset / 6);
		$shiftLeft = ($bitoffset % 6);
		$shiftRight = 6 - $shiftLeft;
        	//for ($j = 0; $j < $numbits; $j++) {
		    //if ($j == 0) { // Code to count starting bit positions
			//$k = $bitoffset % 6;
			//if (array_key_exists($shiftLeft, $startpos)) $startpos[$shiftLeft] += 1;	
			//else $startpos[$shiftLeft] = 1;
		    //}
		    //$lsbval = ($val >> $j) & 1;
		    //$result[$bitoffset >> 3] |= $lsbval << ($bitoffset & 7 );
		    //$result[(int)($bitoffset / 7)] |= $lsbval << ($bitoffset % 7 ); // UTF-8 compatible with 7-bit ASCII
		    //$result[(int)($bitoffset / 6)] |= $lsbval << ($bitoffset % 6 ); // 6 bits for Base64
		    //$bitoffset++;
		//}
		$result[$posInsert] |= ((63 >> $shiftLeft) & $val) << $shiftLeft;
		// How many more $result positions to fill
		$bitsleft = $numbits - $shiftRight;
		$val = $val >> $shiftRight;
		$ii = 0;
		while ($bitsleft > 0) {
		    $ii++;
		    $result[$posInsert + $ii] = $val & 63;
		    $val = $val >> 6;
		    $bitsleft = $bitsleft - 6;
		}
		$bitoffset += $numbits;
                //array_push($result,$dictionary[$w]);
                $dictionary[$wc] = $dictSize++;
                $w = (string)$c;
            }
        }
        if ($w !== "") {
	    $numbits = ceil(log($dictSize+1,2.0));
	    $val = $dictionary[$w];
	    $posInsert = (int)($bitoffset / 6);
	    $shiftLeft = ($bitoffset % 6);
	    $shiftRight = 6 - $shiftLeft;
	    //if (array_key_exists($shiftLeft, $startpos)) $startpos[$shiftLeft] += 1;	
	    //else $startpos[$shiftLeft] = 1;
	    $result[$posInsert] |= ((63 >> $shiftLeft) & $val) << $shiftLeft;
	    $bitsleft = $numbits - $shiftRight;
		$val = $val >> $shiftRight;
		$ii = 0;
		while ($bitsleft > 0) {
		    $ii++;
		    $result[$posInsert + $ii] = $val & 63;
		    $val = $val >> 6;
		    $bitsleft = $bitsleft - 6;
		}
	    $bitoffset += $numbits;
            //array_push($result,$dictionary[$w]);
        }
        //array_shift($result);
	//print count($result);
	//print $bitoffset;
	//print_r ($startpos);
        //return implode(",",$result);
	$this->bitCount = $bitoffset;
        return implode(array_map(
				function($i){return $this->strOut[$i];},$result));
    }

    function getBitCount() {
	return $this->bitCount;
    }
 
    function decompress($com) {
        $com = explode(",",$com);
        $i;$w;$k;$result;
        $dictionary = array();
        $entry = "";
        $dictSize = 256;
        for ($i = 0; $i < 256; $i++) {
            $dictionary[$i] = chr($i);
        }
        $w = chr($com[0]);
        $result = $w;
        for ($i = 1; $i < count($com);$i++) {
            $k = $com[$i];
            if ($dictionary[$k]) {
                $entry = $dictionary[$k];
            } else {
                if ($k === $dictSize) {
                    $entry = $w.$w[0];
                } else {
                    return null;
                }
            }
            $result .= $entry;
            $dictionary[$dictSize++] = $w + $entry[0];
            $w = $entry;
        }
        return $result;
    }
}

//$str = file_get_contents("/home/austioi2/tmp/cache/17aaa669e24983f8dc18955e3da73e5a.tbl1");
//$lzw = new LZW();
//$com = $lzw->compress($str);
//file_put_contents("/home/austioi2/tmp/cache/17aaa669e24983f8dc18955e3da73e5a.lzw",$lzw->compress($str));
?>
