<?php

require 'multTokeSend.php';

function createCustomer($consumerSecret,$link, $method,$token,$tokenSecret,$params){
    
    $params['oauth_token']=$token;

    ksort($params);

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

    $key = urlencode_oauth($consumerSecret) . '&'.  urlencode_oauth($tokenSecret);


    $signature = base64_encode(hash_hmac('sha1',$base_string,$key,true));

    $params['oauth_signature'] = $signature;
    $str = array();
    
    foreach ($params as $k=>$value) {
        $str[] = $k . '="'.urlencode_oauth($value).'"';
    }

    $str = implode(',',$str);

    $info = array(

       "alias"=>"Craig",
      "number"=>"1",
        "email"=>"Sean.OMalley@pps.io",
        "isLoyaltyEnrolled"=>true,
        "addresses"=>array(array(
  		"addressLine1"=>"123 4th St",
			"city"=>"Alpharetta",
			"state"=>"",
			"zip"=>"30303",
			"type"=>"Billing"
			)),
        "receiveEmailPromotions"=>true,
        "receiveCellPhonePromotions"=>false
       );

//Create Authorization Header
   $headers = array(
     'Authorization: OAuth '.$str,
    'Content-Type: application/json',
   // 'Content-Length: '.strlen($info),
    //'Connection: close'
    );


    $options = array(CURLOPT_HTTPHEADER => $headers, //use our authorization 
                           CURLOPT_HEADER=>true,
                           CURLOPT_URL => $link,
                           CURLOPT_POST => true,
                           CURLOPT_POSTFIELDS => json_encode($info) ,//this is going to be a POST - required   
                           CURLOPT_RETURNTRANSFER => true, //return content as a string, don't echo out directly
                           CURLOPT_SSL_VERIFYPEER => false //don't verify SSL certificate, just do it
                    ); 
    $ch = curl_init(); //get a channel
    curl_setopt_array($ch, $options); //set options
    $response = curl_exec($ch); //make the call
    curl_close($ch); //hang up
    
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
    $cust=createCustomer($consumerSecret,$links['createCust'][0], $links['createCust'][1],$token,$tokenSecret,$params);
    $loc=$cust['Location'];
    $pos =strrpos($loc, "/");
    $custId = substr($loc,$pos+1); 
    echo "This is the id of the customer you just created. ".$custId."<br />  You can submit this back to GET /customer/{id} to see the information you just submitted."
         
?>
