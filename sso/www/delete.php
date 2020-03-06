<?
require './functions.php';
include('./httpful.phar');
$totp_id = "9302ba60-07e0-4b4f-a4cc-3abda3038fe0";
$token = connectAPI();
$decode = json_decode($token, true);
$base_url = "https://ajcasecidz.ice.ibmcloud.com";
$uri = "/v1.0/authnmethods/totp/".$totp_id;
print($totp_id.$base_url.$decode);

$callurl = "https://ajcasecidz.ice.ibmcloud.com/v1.0/authnmethods/totp/".$totp_id;
$call = \Httpful\Request::delete($callurl)
->addHeaders(array(
  'Authorization' => 'Bearer '.$cic_token,
  'Content-Type'=>'application/json'
))
->send();
echo $call;
?>
