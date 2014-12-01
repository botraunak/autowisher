<?php 
error_reporting(0);
require 'sdk/src/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '', // CHANGE
  'secret' => '', // CHANGE
));

function getPostData($url, $data) {
	// $url, $data array
	$postdata = http_build_query(
	    $data
	);
	$opts = array('http' =>
	    array(
	        'method'  => 'POST',
	        'header'  => "Content-type: application/x-www-form-urlencoded",
	        'content' => $postdata,
	        'user_agent' => "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36"
	    )
	);
	$context  = stream_context_create($opts);
	$result = file_get_contents($url, true, $context);
	return $result;
}

// See if there is a user from a cookie
$user = $facebook->getUser();

if ($user) {
  $access_token = $facebook->getAccessToken();
}

	
?>