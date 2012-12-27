<?php
require 'multToke.php';

function urlencode_oauth($str) {
  return
    str_replace('+',' ',str_replace('%7E','~',rawurlencode($str)));
}


$url='https://test.api.mxmerchant.com/v1/order';
$params = array(
    'merchantId'=>'',  // <<<<<<<The Merchant Id
  'oauth_token'=>$t,
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
  'POST',
  urlencode_oauth($url),
  urlencode_oauth($q)
);
//Concatenate with &
$base_string = implode('&',$parts);


$key = urlencode_oauth($ccSecret) . '&'.  urlencode_oauth($ts);


$signature = base64_encode(hash_hmac('sha1',$base_string,$key,true));

//Append the signature to your array
$params['oauth_signature'] = $signature;
$str = array();
//Re-URL encode all your values including the signature this time
foreach ($params as $k=>$value) {
  $str[] = $k . '="'.urlencode_oauth($value).'"';
}


$str = implode(',',$str);

//Create Authorization Header
$headers = array(
  'Authorization: OAuth '.$str,
  'Content-Type: application/json'
  //'Content-Length: 0',
  //'Connection: close'
);



$info = json_encode(array(

"merchantId"=>'',//<<<<<The Merchant Id
"type"=>"Sale",
"quantity"=>1,
"totalAmount"=>"3",
"taxAmount"=>"0",
"subTotalAmount"=>"3",
"tipAmount"=>"0",
 "discountAmount"=>"0",
"balance"=>"90",
  
"purchases"=> array("productName"=>"test1",
                    "quantity"=>1,
                    "price"=>"3",
                    "discountAmount"=>"0",
                    "subTotalAmount"=>"3",
                    "taxRate"=>"0",
                    "taxAmount"=>"0",
                    "totalAmount"=>"3",
                    ) ,
"discounts"=>array(),
"customer"=>array(
"id"=>"",//<<<<<<the Id of the customer you are attaching the order to.  ie:31fbm8l9c-e006-4145-8fd4-97679ee5fc08
"alias"=>"Craig"
)
    
));


    $options = array(CURLOPT_HTTPHEADER => $headers, //use our authorization 
                           CURLOPT_URL => $url.'?'."",// <<<<<<<The Merchant ID
                           CURLOPT_POST => true,
                           CURLOPT_POSTFIELDS => $info ,//this is going to be a POST - required   
                           CURLOPT_RETURNTRANSFER => true, //return content as a string, don't echo out directly
                           CURLOPT_SSL_VERIFYPEER => false //don't verify SSL certificate, just do it
                    ); 
    $ch = curl_init(); //get a channel
    curl_setopt_array($ch, $options); //set options
    $response = curl_exec($ch); //make the call
    curl_close($ch); //hang up
    echo $response;
  
    //The Location response header contains the  ID of the created Order/invoice
    //This can be seen in firebug/Fiddler
   

?>
