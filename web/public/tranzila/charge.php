<?php
/**
 * Charge the credti card using Tranzila Token.
 * @param sum - the sum to charge in NIS
 * @param expdate - epxmonth and expyear of the card as returned by Tranzila, format "my" e.g. 0117
 * @param token - the token
 */

$data = array(
	'supplier' => 'amsntest',
	'sum' => $sum,
	'currency' => 1, //NIS
	'expdate' => $expdate,
	'TranzilaPW' =>  'mRyqlc',
	'TranzilaTK' => $token,
	'tranmode' => 'A',
);
$resp = $this->_curl($data);

$respCode = $resp['Response'];
if($respCode != '000') {
	//ERROR
}

function _curl($data){
	$headers = array(
		"Cache-Control: no-cache",
		"Pragma: no-cache",
	);
	$poststring = array();
	foreach($data as $k => $v)
		$poststring[] = $k . '=' . $v;
	$poststring = implode('&', $poststring);

	$cr = curl_init();
	curl_setopt($cr, CURLOPT_URL,  'https://secure5.tranzila.com/cgi-bin/tranzila31tk.cgi');
	curl_setopt($cr, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($cr, CURLOPT_TIMEOUT,        10);
	curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cr, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($cr, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($cr, CURLOPT_POST, true);
	curl_setopt($cr, CURLOPT_POSTFIELDS, $poststring);
	curl_setopt($cr, CURLOPT_HTTPHEADER, $headers);

	$result = curl_exec($cr);
	$error = curl_error($cr);
	curl_close($cr);

	if(!empty($error))
		die($error);

	$resp = array();
	parse_str($result, $resp);
	return $resp;
}	