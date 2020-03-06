<?php
function connectAPI(){
    include_once('./api_connect.php');
    $data =  array(
            'grant_type'          => "client_credentials",
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
            'scope'    => "openid"
        );
    $response = \Httpful\Request::post($url)
    ->body('grant_type=client_credentials&client_id='.$data['client_id'].'&client_secret='.$data['client_secret'])
    ->addHeaders(array(
            'Content-Type' => 'application/x-www-form-urlencoded'
    ))
    ->send();
    return $response;
}
function loginUser($username,$password,$base_url,$cic_token) {
  $uri = "/v2.0/Users/authentication";
  $credentials = array(
    uname => $username,
    pword => $password
  );
  //Set session logged in and userName
  $_SESSION['username']=$username;
  $_SESSION['loggedin']=true;
  #$credentials = array(
  #  uname => "a.jcase@gmail.com",
  #  pword => "Passw0rd"
  #);
  $callurl = $base_url . $uri;
  //echo $callurl;
  $call = \Httpful\Request::post($callurl)
  ->addHeaders(array(
      'Authorization' => 'Bearer '.$cic_token,
      'Content-Type' => 'application/json'      // in the form of an assoc array
  ))
  ->body('{
    "userName": "'.$credentials["uname"].'",
    "password": "'.$credentials["pword"].'",
    "schemas": [
      "urn:ietf:params:scim:schemas:ibm:core:2.0:AuthenticateUser"
    ]
  }')
  ->sendsJson()
  ->expectsJson()
  ->send();
  $response = json_decode($call, true);
  if($response[id]){
    $return = array(
      "id"=>$response["id"],
      "uname"=>$credentials["uname"]
    );
    return $return; // good login if this exists
  }else {
    return false;
  }
}
function totpentitlement($userid,$base_url,$cic_token){
  $uri = "/v1.0/authnmethods/totp?search=owner%20%3D%20%22".$userid."%22";
  $callurl = $base_url . $uri;

  $call = \Httpful\Request::get($callurl)
  ->addHeaders(array(
      'Authorization' => 'Bearer '.$cic_token
  ))
  ->expectsJson()
  ->send();
  $response = json_decode($call, true);
  $responseOwner = $response["totp"][0]["owner"];
  $responseEnabled = $response["totp"][0]["isEnabled"];
  $responseID = $response["totp"][0]["id"];
  $responseVerified = $response["totp"][0]["isValidated"];
  //print_r($response);
  if($responseOwner == $userid && $responseEnabled){
    $return = array(
      "id"=>$response["totp"][0]["id"],
      "owner"=>$response["totp"][0]["owner"],
      "enabled"=>$response["totp"][0]["isEnabled"],
      "verified"=>$response["totp"][0]["isValidated"]
    );
    return $return;
  }else {
    return false;
  }
}
function totpenrollment($userid,$username,$base_url,$cic_token){

  $uri = "/v1.0/authnmethods/totp/?qrcodeInResponse=true";

  $callurl = $base_url . $uri;

  $call = \Httpful\Request::post($callurl)
  ->addHeaders(array(
      'Authorization' => 'Bearer '.$cic_token,
      'Content-Type'=>'application/json'
  ))
  ->body('{
    "owner": "'.$userid.'",
    "isEnabled": "true",
    "ownerDisplayName": "'.$username.'"
  }')
  ->send();
  $response = json_decode($call, true);
  if($response["messageId"]=="CSIAH0610E")
  {
    return "TOTPexists";
  }
  elseif($response["isEnabled"]==1 && !$response["isValidated"])
  {
      $qr=$response["attributes"]["b64QRCode"];
      $_SESSION["totp_id"]=$response['id'];
      return $qr;
    }
  else
    {
      return "TOTPerror";
    }
  }
function totpdelete($totp_id,$base_url,$cic_token){

    $uri = "/v1.0/authnmethods/totp/".$totp_id;
    $callurl = $base_url . $uri;

    $call = \Httpful\Request::delete($callurl)
    ->addHeaders(array(
        'Authorization' => 'Bearer '.$cic_token,
        'Content-Type'=>'application/json'
    ))
    ->send();
    if(!$response){
      return true;
    }
    else{
      return false;
    }
  }
function totpverify($totp_token,$totp_id,$base_url,$cic_token){
  $uri = "/v1.0/authnmethods/totp/".$totp_id;
  $callurl = $base_url . $uri;

  $call = \Httpful\Request::post($callurl)
  ->addHeaders(array(
      'Authorization' => 'Bearer '.$cic_token,
      'Content-Type'=>'application/json'
  ))
  ->body('{
    "totp": "'.$totp_token.'"
  }')
  ->send();
  $response = json_decode($call, true);
  if($response["messageId"]=="CSIAH0620I")
  {
    return true;
  }
  else {
    return false;
  }
}
?>
