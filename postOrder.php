<?php

require 'multToke.php';

function createOrder($consumerSecret, $link, $method,$token,$tokenSecret,$params,$merchId,$custId,$custAlias){
    $params['oauth_token']=$token;

     $queryParts=array(
        'merchantId'=>'-'.$merchId 
     );   
    $queryString=array();
 
    foreach ($queryParts as $key=>$value) {
        $queryString[] = urlencode_oauth($key).'='.urlencode_oauth($value);
    }
    $queryString = implode('&',$queryString); 

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

    $headers = array(
        'Authorization: OAuth '.$str,
    'Content-Type: application/json'
    );



    $info = array(

        "merchantId"=>$merchId,
        "type"=>"Sale",
        "quantity"=>1,
        "totalAmount"=>"3",
        "taxAmount"=>"0",
        "subTotalAmount"=>"3",
        "tipAmount"=>"0",
        "discountAmount"=>"0",
        "balance"=>"90",
  
        "purchases"=> array(array("productName"=>"test1",
                            "quantity"=>"1",
                            "price"=>"3",
                            "discountAmount"=>"0",
                            "subTotalAmount"=>"3",
                            "taxRate"=>"0",
                            "taxAmount"=>"0",
                            "totalAmount"=>"3",
                            )) ,
        "discounts"=>array(),
        "customer"=>array(
        "id"=>$custId,
        "alias"=>$custAlias
        )
    
    );


    $options = array(CURLOPT_HTTPHEADER => $headers, //use our authorization 
                           CURLOPT_URL => $link.'?'.$queryString,
                           CURLOPT_POST => true,
                           CURLOPT_POSTFIELDS => json_encode($info) ,//this is going to be a POST - required   
                           CURLOPT_RETURNTRANSFER => true, //return content as a string, don't echo out directly
                           CURLOPT_SSL_VERIFYPEER => false //don't verify SSL certificate, just do it
                    ); 
    $ch = curl_init(); //get a channel
    curl_setopt_array($ch, $options); //set options
    $response = curl_exec($ch); //make the call
    curl_close($ch); //hang up
    echo $response;
  
    
}
    //Fill in the following three variables with the
    //merchantId
    //customer id
    //customer alias(name)
    $merchId;
    $custId;
    $custAlias;
    $order=createOrder($consumerSecret, $links['createOrder'][0], $links['createOrder'][1],$token,$tokenSecret,$params,$merchId,$custId,$custAlias);
    $loc=$order['Location'];
    $pos =strrpos($loc, "/");
    $orderId = substr($loc,$pos+1); 
    echo "This is the id of your order. ".$orderId."<br />  You can submit this back to GET /order/{id} to see the information you just submitted."
         ?>
