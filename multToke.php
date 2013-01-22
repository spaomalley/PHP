<?php
//Enter your MX Merchant username here between the apostrophes
$consumerKey='';
//Enter your MX Merchant password here between the apostrophes
$consumerSecret= base64_encode(sha1('',true));


$links=array(
    'request'=> array('https://test.api.mxmerchant.com/v1/OAuth/1A/RequestToken',"POST"),
    'access'=> array('https://test.api.mxmerchant.com/v1/OAuth/1A/AccessToken', "POST"),
    'payment'=>array('https://test.api.mxmerchant.com/v1/payment', "POST"),
    'getPayment'=>array('https://test.api.mxmerchant.com/v1/payment/...', "GET"),
    'putPayment'=>array('https://test.api.mxmerchant.com/v1/payment/...', "PUT"),
    'getAllPayment'=>array('https://test.api.mxmerchant.com/v1/payment', "GET"),
    'paymentOrder' =>array('https://test.api.mxmerchant.com/v1/order/.../payment', "POST"),
    'searchCust'=> array('https://test.api.mxmerchant.com/v1/customer',"GET"),
    'createOrder'=> array('https://test.api.mxmerchant.com/v1/order',"POST"),
    'createCust'=> array('https://test.api.mxmerchant.com/v1/customer',"POST")
    );

$params = array(
  'oauth_consumer_key'=>$consumerKey,
  'oauth_nonce'=>sha1(microtime()),
  'oauth_signature_method'=>'HMAC-SHA1',
  'oauth_timestamp'=> time(),
  'oauth_version'=>'1.0'
);

function urlencode_oauth($str) {
  return
    str_replace('+',' ',str_replace('%7E','~',rawurlencode($str)));
  }
  
function startProcess($consumerKey,$consumerSecret,$link,$method,$params){
      
ksort($params);

// prepare URL-encoded query string
$q = array();
foreach ($params as $key=>$value) {
  $q[] = urlencode_oauth($key).'='.urlencode_oauth($value);
}

$q = implode('&',$q);
$parts = array(
  $method,
  urlencode_oauth($link),
  urlencode_oauth($q)
);
$base_string = implode('&',$parts);

$key = urlencode_oauth($consumerSecret) . '&';
$signature = base64_encode(hash_hmac('sha1',$base_string,$key,true));

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
                           CURLOPT_URL => $link, //the URI we're sending the request to
                           CURLOPT_POST => true, //this is going to be a POST - required
                           CURLOPT_RETURNTRANSFER => true, //return content as a string, don't echo out directly
                           CURLOPT_SSL_VERIFYPEER => false); //don't verify SSL certificate, just do it

    $ch = curl_init(); //get a channel
    curl_setopt_array($ch, $options); //set options
    $response = curl_exec($ch); //make the call
    curl_close($ch); //hang up
    return $response;

  }
  
function getAccessToken($consumerKey,$consumerSecret,$token,$tokenSecret,$link,$method,$params){
    
 $params['oauth_token']=$token;
 
ksort($params);

// prepare URL-encoded query string
$q = array();
foreach ($params as $key=>$value) {
  $q[] = urlencode_oauth($key).'='.urlencode_oauth($value);
}
$q = implode('&',$q);
$parts = array(
  $method,
  urlencode_oauth($link),
  urlencode_oauth($q)
);
$base_string = implode('&',$parts);

$key = urlencode_oauth($consumerSecret).'&'.urlencode_oauth($tokenSecret);
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
                           CURLOPT_URL => $link, //the URI we're sending the request to
                           CURLOPT_POST => true, //this is going to be a POST - required
                           CURLOPT_RETURNTRANSFER => true, //return content as a string, don't echo out directly
                           CURLOPT_SSL_VERIFYPEER => false); //don't verify SSL certificate, just do it
 
    $ch = curl_init(); //get a channel
    curl_setopt_array($ch, $options); //set options
    $response = curl_exec($ch); //make the call
    curl_close($ch); //hang up
    return $response;       
}

parse_str(startProcess($consumerKey, $consumerSecret,$links['request'][0],$links['request'][1],$params),$initCall);
parse_str(getAccessToken($consumerKey, $consumerSecret, $initCall['oauth_token'],$initCall['oauth_token_secret'],$links['access'][0],$links['access'][1],$params),$data);
 
 $token=$data['oauth_token'];
 $tokenSecret=$data['oauth_token_secret'];
 
 
 //echo "This is your oauth_token ".$token;
 //echo "This is your oauth_token_secret".$tokenSecret;
 
 
?>
