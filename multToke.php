<?php

$ccKey='';
$ccSecret= base64_encode(sha1('',true));
$t;
$ts;

$links=array(
    'request'=> array('https://test.api.mxmerchant.com/v1/OAuth/1A/RequestToken',"POST"),
    'access'=> array('https://test.api.mxmerchant.com/v1/OAuth/1A/AccessToken', "POST"),
    'payment'=>array('https://test.api.mxmerchant.com/v1/payment', "POST"),
    'paymentOrder' =>array('https://test.api.mxmerchant.com/v1/order/.../payment', "POST"),
    'searchCust'=> array('https://test.api.mxmerchant.com/v1/customer',"GET")
    );

function urlencode_oauth($str) {
  return
    str_replace('+',' ',str_replace('%7E','~',rawurlencode($str)));
  }
  
  function startProcess($ccKey,$ccSecret,$url){
      
 $params = array(
  'oauth_consumer_key'=>$ccKey,
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
$q = implode('&',$q);
$parts = array(
  "POST",
  urlencode_oauth($url),
  urlencode_oauth($q)
);
$base_string = implode('&',$parts);

$key1 = urlencode_oauth($ccSecret) . '&';
$signature = base64_encode(hash_hmac('sha1',$base_string,$key1,true));

$params['oauth_signature'] = $signature;
$str = array();
foreach ($params as $key=>$value) {
  $str[] = $key . '="'.urlencode_oauth($value).'"';
}

$str = implode(',',$str);
$headers = array(
 //'POST /v1/OAuth/1A/RequestToken HTTP/1.1',
 //'Host: test.api.mxmerchant.com',
 'Authorization: OAuth '.$str,
 'Content-Type: application/json',
 'Content-Length: 0',
 'Connection: close'
);

    $options = array(CURLOPT_HTTPHEADER => $headers, //use our authorization 
                           //CURLOPT_HEADER => false, //don't retrieve the header back from Twitter
                           CURLOPT_URL => $url, //the URI we're sending the request to
                           CURLOPT_POST => true, //this is going to be a POST - required
                           //CURLOPT_POSTFIELDS => $params,    
                           CURLOPT_RETURNTRANSFER => true, //return content as a string, don't echo out directly
                           CURLOPT_SSL_VERIFYPEER => false); //don't verify SSL certificate, just do it

    $ch = curl_init(); //get a channel
    curl_setopt_array($ch, $options); //set options
    $response = curl_exec($ch); //make the call
    curl_close($ch); //hang up
    return $response;

  }
  
  

function getAccessToken($ccKey,$ccSecret,$maltToke,$maltTokeSec,$l){
    
    $params = array(
  'oauth_consumer_key'=>$ccKey,
  'oauth_nonce'=>sha1(microtime()),
  'oauth_signature_method'=>'HMAC-SHA1',
  'oauth_timestamp'=> time(),
  'oauth_version'=>'1.0'
);

$params['oauth_token']=$maltToke;
 
ksort($params);

// prepare URL-encoded query string
$q = array();
foreach ($params as $key=>$value) {
  $q[] = urlencode_oauth($key).'='.urlencode_oauth($value);
}
$q = implode('&',$q);
$parts = array(
  "POST",
  urlencode_oauth($l),
  urlencode_oauth($q)
);
$base_string = implode('&',$parts);

$key = urlencode_oauth($ccSecret).'&'.urlencode_oauth($maltTokeSec);
$signature = base64_encode(hash_hmac('sha1',$base_string,$key,true));

$params['oauth_signature'] = $signature;
$str = array();
foreach ($params as $k=>$value) {
  $str[] = $k . '="'.urlencode_oauth($value).'"';
}

$str = implode(',',$str);
$headers = array(
 'POST /v1/OAuth/1A/AccessToken HTTP/1.1',
 'Host: test.api.mxmerchant.com',
  'Authorization: OAuth '.$str,
  'Content-Type: application/json',
  'Content-Length: 0',
  'Connection: close'
);

    $options = array(CURLOPT_HTTPHEADER => $headers, //use our authorization 
                           //CURLOPT_HEADER => false, 
                           CURLOPT_URL => $l, //the URI we're sending the request to
                           CURLOPT_POST => true, //this is going to be a POST - required
                           //CURLOPT_POSTFIELDS => $params,    
                           CURLOPT_RETURNTRANSFER => true, //return content as a string, don't echo out directly
                           CURLOPT_SSL_VERIFYPEER => false); //don't verify SSL certificate, just do it
 
    $ch = curl_init(); //get a channel
    curl_setopt_array($ch, $options); //set options
    $response = curl_exec($ch); //make the call
    curl_close($ch); //hang up
    return $response;       
}

parse_str(startProcess($ccKey, $ccSecret,$links['request'][0],$links['request'][1]),$malt);
 parse_str(getAccessToken($ccKey, $ccSecret, $malt['oauth_token'],$malt['oauth_token_secret'],$links['access'][0],$links['access'][1]),$data);
 
 $t=$data['oauth_token'];
 $ts=$data['oauth_token_secret'];
 echo "This is your OAuth Access Token:".$t."<br />";
 echo "This is your OAuth Token Secret:".$ts;
?>
