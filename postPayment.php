<?php

require 'multToke.php';
$orderID=''; //<<  the Order ID

 $params = array(
  'oauth_consumer_key'=>$ccKey,
  'oauth_nonce'=>sha1(microtime()),
  'oauth_signature_method'=>'HMAC-SHA1',
  'oauth_timestamp'=> time(),
  'oauth_version'=>'1.0'
); 

$newL=  str_replace("...", $orderID, $links['paymentOrder'][0]);
 

$params['oauth_token']=$t;
$params['merchantId']="";  //<<the merchant Id prepended by  "-"


ksort($params);

$q = array();
foreach ($params as $key=>$value) {
  $q[] = urlencode_oauth($key).'='.urlencode_oauth($value);
}
$q = implode('&',$q);


$parts = array(
        $links['paymentOrder'][1],
        urlencode_oauth($newL),
        urlencode_oauth($q)
        );



$base_string = implode('&',$parts);

$key = urlencode_oauth($ccSecret) . '&'.  urlencode_oauth($ts);

$signature = base64_encode(hash_hmac('sha1',$base_string,$key,true));

$params['oauth_signature'] = $signature;
$str = array();

foreach ($params as $k=>$value) {
  $str[] = $k . '="'.urlencode_oauth($value).'"';
}

$str = implode(',',$str);

$i=0;
$cardDigits=0;
$number=  strval(rand(3, 6));

if(strval($number)=="3") { $cardDigits=15; } else {$cardDigits=16;}


for ($i; $i <= $cardDigits-2; $i++) {
    $number.= strval(rand(0, 9));
}

$info = json_encode(array(

"payment"=>"card",
"paymentCard"=>array(
     "accountNumber"=>$number,
     "expirationMonth"=>"07",
     "expirationYear"=>strval(rand(2013, 2020)),
     "cvv"=>"123"
 ),
"taxAmount"=>"5",
"totalAmount"=>"0.01"
    
));

$headers = array(
  'Authorization: OAuth '.$str,
  'Content-Type: application/json',
  'Content-Length: '.strlen($info),
  'Connection: close',
  'Accept: application/json'
);



    $options = array(CURLOPT_HTTPHEADER => $headers, //use our authorization 
                           //CURLOPT_HEADER => true,
                           CURLOPT_URL => $newL."?"."",//<< the merchant id
                           //the URI we're sending the request to
                           CURLOPT_POST => true,
                           CURLOPT_POSTFIELDS => $info ,//this is going to be a POST - required   
                           CURLOPT_RETURNTRANSFER => true, //return content as a string, don't echo out directly
                           CURLOPT_SSL_VERIFYPEER => false //don't verify SSL certificate, just do it
                    ); 
    $ch = curl_init(); //get a channel
    curl_setopt_array($ch, $options); //set options
    $response = curl_exec($ch); //make the call
    curl_close($ch); //hang up
    $good= json_decode($response,true);
    print_r($good);
  
 
?>
