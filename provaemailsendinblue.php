<?php

$now=date("Y-m-d H:i:s");
/*
$comando=<<<EOD
curl --request POST \
  --url https://api.sendinblue.com/v3/smtp/email \
  --header 'accept: application/json' \
  --header 'api-key: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' \
  --header 'content-type: application/json' \
  --data '{
   "sender":{
      "name":"[CLEAN MANAGEMENT]",
      "email":"info@clean-mangement.it"
   },
   "to":[
      {
         "email":"fabio.franci@gmail.com",
         "name":"Fabio Franci"
      }
   ],
   "subject":"Hello world $now",
   "htmlContent":"<html><head></head><body><p>Hello,</p>This is my first transactional email sent from Sendinblue.</p></body></html>"
}'
EOD;

$result=system($comando);
*/

$url = 'https://api.sendinblue.com/v3/smtp/email';
$data['sender']['name']="[CLEAN MANAGEMENT]";
$data['sender']['email']="info@clean-mangement.it";
$data['to'][0]['name']="Fabio Franci";
$data['to'][0]['email']="fabio.franci@gmail.com";
$data['subject']="Hello world $now";
$data['htmlContent']="<html><head></head><body><p>Hello,</p>This is my first transactional email sent from Sendinblue.</p></body></html>";

$postdata = json_encode($data);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('api-key: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','content-type: application/json','accept: application/json',));
$result = curl_exec($ch);
curl_close($ch);
print_r ($result);

?>
