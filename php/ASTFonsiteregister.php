<?php
//==============================================================================================================================
function http_send_query_via_curl( $newpost, $strRemoteFormAction ) {
	$chnCurl = curl_init();
	curl_setopt($chnCurl, CURLOPT_URL, $strRemoteFormAction);
	curl_setopt($chnCurl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($chnCurl, CURLOPT_POST, true);
	curl_setopt($chnCurl, CURLOPT_POSTFIELDS, $newpost);

	// waiting until file will be locked for writing (3000 milliseconds as timeout)
	if ($fp = fopen($_SERVER['DOCUMENT_ROOT'].'/tmp.lock', 'a')) {
	  $startTime = microtime();
	  do { 
	    $canWrite = flock($fp, LOCK_EX); 
	    // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load 
	    if(!$canWrite) usleep(round(rand(0, 100)*1000)); 
	  } while ((!$canWrite)and((microtime()-$startTime) < 8000)); 
	}	

	$strResponse = curl_exec($chnCurl);
	$strInfo = curl_getinfo($chnCurl);

	fclose($fp);
	//unlink($_SERVER['DOCUMENT_ROOT'].'/tmp.lock');

	curl_close($chnCurl);
	return array('Response' => $strResponse,
			'Info'  => $strInfo);
}

function http_build_query_for_curl( $arrays, &$new = array(), $prefix = null ) {

    if ( is_object( $arrays ) ) {
        $arrays = get_object_vars( $arrays );
    }

    foreach ( $arrays AS $key => $value ) {
        $k = isset( $prefix ) ? $prefix . '[' . $key . ']' : $key;
        if ( is_array( $value ) OR is_object( $value )  ) {
            http_build_query_for_curl( $value, $new, $k );
        } else {
            $new[$k] = $value;
        }
    }
}
//===============================================================================================================================:u
 ?>

<?php
if (isset($_GET['type']) && ($_GET['type'] == 'repeat')) {
    echo '<body onload=initSetup()>';
    include 'dbtable.inc.php';
}
else {
    echo '<body>';
} 
 ?>
