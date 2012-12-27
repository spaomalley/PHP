<?php
require 'multToke.php';
define('CONSUMER_KEY', '');
$conSec=base64_encode(sha1('',true));

//URL encoding per RFC standard
function urlencode_oauth($str) {
  return
    str_replace('+',' ',str_replace('%7E','~',rawurlencode($str)));
}


$url='https://test.api.mxmerchant.com/v1/customer';
$params = array(
    
  'oauth_token'=>$t,
  'oauth_consumer_key'=>CONSUMER_KEY,
  'oauth_nonce'=>sha1(microtime()),
  'oauth_signature_method'=>'HMAC-SHA1',
  'oauth_timestamp'=> time(),  
  'oauth_version'=>'1.0'
);

// sort parameters according to ascending order of key
ksort($params);

// prepare URL-encoded query string
$q = array();
foreach ($params as $key=>$value) {
  $q[] = urlencode_oauth($key).'='.urlencode_oauth($value);
}
$q = implode('&',$q);+

/*
 * Get your 3 parts ready for generating your basestring
 */
$parts = array(
  'POST',
  urlencode_oauth($url),
  urlencode_oauth($q)
);
//Concatenate with &
$base_string = implode('&',$parts);
/*
 * Currently we have no tokens, so our key to our signature will just be our consumer secret concatenated with   &
 */
$key = urlencode_oauth($conSec) . '&'.  urlencode_oauth($ts);

/*
 * implement your key into the Sha1-HMAC hash of you basestring. Then base-64 encode this.
 * 
 */
$signature = base64_encode(hash_hmac('sha1',$base_string,$key,true));

//Append the signature to your array
$params['oauth_signature'] = $signature;
$str = array();
//Re-URL encode all your values including the signature this time
foreach ($params as $k=>$value) {
  $str[] = $k . '="'.urlencode_oauth($value).'"';
}

/*
 * separate these values with a comma followed by NO spaces
 * 
 */
$str = implode(',',$str);

$info = json_encode(array(

"alias"=>"Kumar",
"note"=>"Hello, my name is Kumar"
    
));

//Create Authorization Header
$headers = array(
  'Authorization: OAuth '.$str,
  'Content-Type: application/json',
  'Content-Length: '.strlen($info)
  //'Connection: close'
);



/*
 * Use cURL  to send request including header
 */
    $options = array(CURLOPT_HTTPHEADER => $headers, //use our authorization 
                           CURLOPT_URL => $url,//'?'."merchantId=-1",
                           //the URI we're sending the request to
                           CURLOPT_POST => true,
                           CURLOPT_POSTFIELDS => $info ,//this is going to be a POST - required   
                           //CURLOPT_RETURNTRANSFER => true, //return content as a string, don't echo out directly
                           CURLOPT_SSL_VERIFYPEER => false //don't verify SSL certificate, just do it
                    ); 
    $ch = curl_init(); //get a channel
    curl_setopt_array($ch, $options); //set options
    $response = curl_exec($ch); //make the call
    curl_close($ch); //hang up
    //echo $response;
    
    
    //The Location response header contains the ID of the created customer
  
   

?>
