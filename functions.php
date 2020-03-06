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
    $_SESSION['loggedin']=true;
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
function appEnrollement($userid,$client_id,$account_name,$base_url,$cic_token){

  $uri = "/v1.0/authenticators/initiation?qrcodeInResponse=true";

  $callurl = $base_url . $uri;

  $call = \Httpful\Request::post($callurl)
  ->addHeaders(array(
      'Authorization' => 'Bearer '.$cic_token,
      'Content-Type'=>'application/json'
  ))
  ->body('{
  "owner": "'.$userid.'",
  "clientId": "'.$client_id.'",
  "accountName": "'.$account_name.'"
}')
  ->send();
  $response = json_decode($call, true);
  if(!$response["qrcode"])
    {
      return "fail";
    }
    else
    {
      $qr=$response["qrcode"];
      return $qr;
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
    if(!$call){
      return true;
    }
    else{
      return $call;
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
  if($response["messageId"]=="CSIAH0620I" || $response['messageId']=="CSIAH0621I")
  {
    return 1;
  }
  else {
    return 0;
  }
}
function getAuthenticatorEnrollment($userid,$base_url,$cic_token){
  $options = "?search=owner%20contains%20%22".$userid."%22%26state%3D%22ACTIVE%22";
  $uri = "/v1.0/authenticators".$options;
  $callurl = $base_url . $uri;

  $call = \Httpful\Request::get($callurl)
  ->addHeaders(array(
      'Authorization' => 'Bearer '.$cic_token,
      'Content-Type'=>'application/json'
  ))
  ->send();
  $response = json_decode($call, true);
  return $response;
}
function getAuthenticatorName($auth_id,$base_url,$cic_token){
  $options = "?search=id%3D%22".$auth_id."%22&filter=attributes";
  $uri = "/v1.0/authenticators".$options;
  $callurl = $base_url . $uri;

  $call = \Httpful\Request::get($callurl)
  ->addHeaders(array(
      'Authorization' => 'Bearer '.$cic_token,
      'Content-Type'=>'application/json'
  ))
  ->send();
  $response = json_decode($call, true);
  return $response['authenticators'][0]['attributes']['deviceName'];
}
function getTransactionLog($userid,$base_url,$cic_token){
  $authenticators = getAuthenticatorEnrollment($userid,$base_url,$cic_token);
  $rc = $authenticators['total'];
  $log = array();
  if($rc > 0){
    // continue on, has at least one device.
    $x = 0;
    while($x < $rc){
      $auth_id = $authenticators['authenticators'][$x]['id'];
      $uri = "/v1.0/authenticators/".$auth_id."/verifications?sort=-creationTime";
      $call = $base_url . $uri;
      $t = \Httpful\Request::get($call)
        ->addHeaders(array(
            'Authorization' => 'Bearer '.$cic_token,
            'Content-Type'=>'application/json'
        ))
      ->send();
      $t = json_decode($t, true);
      if($t['total'] > 0){
        // verificationt transactions exist
        $z = 0;
        while($z < $t['total']){
          $deviceName = getAuthenticatorName($auth_id, $base_url, $cic_token);
          $log_addendum = array(
            "deviceName" => $deviceName,
            "userid" => $t['verifications'][$z]['owner'],
            "deviceId" => $t['verifications'][$z]['authenticatorId'],
            "creationTime" => $t['verifications'][$z]['creationTime'],
            "authType" => $t['verifications'][$z]['authenticationMethods'][0]['subType'],
            "state" => $t['verifications'][$z]['state'],
            "pushStatus" => $t['verifications'][$z]['pushNotification']['sendState']
          );
          array_push($log, $log_addendum);
          $z++;
        }
      }
    $x++;
    }
  }
  return $log;
}
function getVerificationTransaction($tid, $auth_id,$base_url,$cic_token){
      $uri = "/v1.0/authenticators/".$auth_id."/verifications?search=id%3D%22".$tid."%22";
      $call = $base_url . $uri;
      $t = \Httpful\Request::get($call)
        ->addHeaders(array(
            'Authorization' => 'Bearer '.$cic_token,
            'Content-Type'=>'application/json'
        ))
      ->send();
      $t = json_decode($t, true);
      return $t['verifications'];
}
function appPushVerify($message, $title, $auth_id, $base_url, $cic_token){
  $uri = "/v1.0/authenticators/".$auth_id."/verifications";
  $options = "?search=owner%20contains%20%22".$userid."%22";
  $sig_uri = "/v1.0/authnmethods/signatures".$options;
  $callurl = $base_url . $uri;
  $getsigurl = $base_url . $sig_uri;
  $c1 = \Httpful\Request::get($getsigurl)
    ->addHeaders(array(
        'Authorization' => 'Bearer '.$cic_token,
        'Content-Type'=>'application/json'
    ))
  ->send();
  $c1result = json_decode($c1, true);
  // Logic
  // Loop over how many authenticators in array
  // If array[x] == $auth_id then get AuthenticatorID from array.
  $x = 0;
  $rc = $c1result['total'];
  while($x < $rc){
    $ret_auth_id = $c1result['signatures'][$x]['attributes']['authenticatorId'];
    if($ret_auth_id == $auth_id){
      $ret_sig_id = $c1result['signatures'][$x]['id'];
      $c2 = \Httpful\Request::post($callurl)
        ->addHeaders(array(
            'Authorization' => 'Bearer '.$cic_token,
            'Content-Type'=>'application/json'
        ))
        ->body('{
        "expiresIn": 120,
        "pushNotification": {
          "sound": "default",
          "message": "'.$message.'",
          "send": true,
          "title": "'.$title.'"
        },
        "authenticationMethods": [
          {
            "methodType": "signature",
            "id": "'.$ret_sig_id.'"
          }
        ],
        "logic": "OR",
        "transactionData": {
          "message": "'.$message.'",
          "originIpAddress": "127.0.0.1",
          "originUserAgent": "Mozilla Firefox 11"
        }
      }')
      ->send();
      $c2result = json_decode($c2, true);
      return $c2result['id'];
    }
    $x++;
  }
}
function appDelete($auth_id, $base_url, $cic_token){
  $uri = "/v1.0/authenticators/".$auth_id;
  $callurl = $base_url . $uri;
    $call = \Httpful\Request::delete($callurl)
    ->addHeaders(array(
        'Authorization' => 'Bearer '.$cic_token,
        'Content-Type'=>'application/json'
    ))
  ->send();
  $response = json_decode($call, true);
  if(!$response){
    return "success";
  }
  else {
    return "fail";
  }
}
?>
