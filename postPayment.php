<?php
require 'multToke.php';

function makePayment($consumerSecret, $link, $method,$token,$tokenSecret,$params,$merchId){

 $queryParts=array(
      'merchantId'=>'-'.$merchId 
 );   
 $queryString=array();
foreach ($queryParts as $key=>$value) {
  $queryString[] = urlencode_oauth($key).'='.urlencode_oauth($value);
}
$queryString = implode('&',$queryString); 

$params['oauth_token']=$token;

$newArray=  array_merge($queryParts,$params);
 
ksort($newArray);
 
$q = array();
foreach ($newArray as $key=>$value) {
  $q[] = urlencode_oauth($key).'='.urlencode_oauth($value);
}
$q = implode('&',$q);

$parts = array(
        $method,
        urlencode_oauth($link),
        urlencode_oauth($q)
        );

$base_string = implode('&',$parts);

$key = urlencode_oauth($consumerSecret) . '&'.  urlencode_oauth($tokenSecret);

$signature = base64_encode(hash_hmac('sha1',$base_string,$key,true));

$newArray['oauth_signature'] = $signature;
$str = array();

foreach ($newArray as $k=>$value) {
  $str[] = $k . '="'.urlencode_oauth($value).'"';
}

$str = implode(',',$str);

///////Create random card number

$i=0;
$cardDigits=0;
$number=  strval(rand(3, 6));

if(strval($number)=="3") { $cardDigits=15; } else {$cardDigits=16;}

for ($i; $i <= $cardDigits-2; $i++) {
    $number.= strval(rand(0, 9));
}

$info = array(
"payment"=>"card",
"paymentCard"=>array(
     "accountNumber"=>$number,
     "expirationMonth"=>"07",
     "expirationYear"=>strval(rand(2013, 2020)),
     "cvv"=>"123"
 ),
"taxAmount"=>"5",
"totalAmount"=>"0.01"    
);

$headers = array(
  'Authorization: OAuth '.$str,
  'Content-Type: application/json',
  //'Content-Length: '.strlen($info),
  //'Connection: close',
  'Accept: application/json'
);


    $options = array(CURLOPT_HTTPHEADER => $headers, //use our authorization 
                           CURLOPT_HEADER => true,
                           CURLOPT_URL => $link."?".$queryString,
                           CURLOPT_POST => true,
                           CURLOPT_POSTFIELDS => json_encode($info) ,//this is going to be a POST - required   
                           CURLOPT_RETURNTRANSFER => true, //return content as a string, don't echo out directly
                           CURLOPT_SSL_VERIFYPEER => false //don't verify SSL certificate, just do it
                    ); 
    $ch = curl_init(); //get a channel
    curl_setopt_array($ch, $options); //set options
    $response = curl_exec($ch); //make the call
    curl_close($ch); //hang up
    //echo $response;
    
    $head = array();

    $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

    foreach (explode("\r\n", $header_text) as $i => $line)
        if ($i === 0)
            $head['http_code'] = $line;
        else
        {
            list ($key, $value) = explode(': ', $line);

            $head[$key] = $value;
        }

  return $head;
    
 }
        //Enter your merchantId in the $merchId variable. It is located inside MX Merchant after you log in at the top of the page in a box.  (ie,  Test Merchant  2)
         $merchId;
         $good= makePayment($consumerSecret,$links['payment'][0],$links['payment'][1],$token,$tokenSecret,$params,$merchId);
         $loc=$good['Location'];
         $pos =strrpos($loc, "/");
         $paymentId = substr($loc,$pos+1); 
         echo "This is the id of your payment. ".$paymentId."<br />  You can submit this back to GET /payment{id} to see the information you just submitted."
         
?>
