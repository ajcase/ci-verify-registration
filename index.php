<?php
/*
 * Copyright IBM Corp. 2016
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

 /**
  * This PHP file uses the Slim Framework to construct a REST API.
  * See Cloudant.php for the database functionality
  */
session_start();
error_reporting(E_WARNING);
require 'vendor/autoload.php';
require './config.php';
require './functions.php';
require_once('./sso/lib/_autoload.php');
include('./httpful.phar');

$cic_token = "not set";

$as = new \SimpleSAML\Auth\Simple('default-sp');
$as->requireAuth(array(
    'ReturnTo' => '/'
));
$attributes = $as->getAttributes();

$app = new \Slim\Slim();
$dotenv = new Dotenv\Dotenv(__DIR__);
try {
  $dotenv->load();
} catch (Exception $e) {
    error_log("No .env file found");
 }
$app->get('/', function () {
    global $app;
    global $attributes;
    global $base_url;
    if(isset($attributes['mail'][0])){
      $_SESSION['loggedin'] = true;
      $_SESSION["username"] = $attributes['mail'][0];
      $_SESSION["user_id"] = $attributes['uid'][0];
    }
    $token = connectAPI();
    $decode = json_decode($token, true);
    $cic_token = $decode["access_token"];
    //print_r($attributes);
    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']){
      $userid = $_SESSION["user_id"];
      $response = getAuthenticatorEnrollment($userid,$base_url,$cic_token);
      $log = getTransactionLog($userid,$base_url,$cic_token);
      $rc = $response['total'];
      $hasDevice = ($rc>0)?true:false;
      $checkStatus = totpentitlement($userid,$base_url,$cic_token);
      if($checkStatus['enabled']==1){
        $_SESSION["totp_id"] = $checkStatus["id"];
        $_SESSION['totp_verified'] = $checkStatus["verified"];
        $_SESSION['totp_enabled'] = $checkStatus["enabled"];
      }
      $app->render("enrollments.php", array(
          'launchpadURL'=>$base_url,
          'sso_firstname'=>$attributes['firstname'][0],
          'sso_lastname'=>$attributes['lastname'][0],
          'username'=> $_SESSION["username"],
          'userid'=> $_SESSION["user_id"],
          'response' => $response,
          'rc'=>$rc,
          'log'=>$log,
          'enabled'=>$hasDevice,
          'verified'=>$_SESSION['totp_verified']
          )
        );
    }

});
$app->get('/enrollments', function () {
    global $app;
    global $attributes;
    global $base_url;
    if(isset($attributes['mail'][0])){
      $_SESSION['loggedin'] = true;
      $_SESSION["username"] = $attributes['mail'][0];
      $_SESSION["user_id"] = $attributes['uid'][0];
    }
    $token = connectAPI();
    $decode = json_decode($token, true);
    $cic_token = $decode["access_token"];
    //print_r($attributes);
    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']){
      $userid = $_SESSION["user_id"];
      $response = getAuthenticatorEnrollment($userid,$base_url,$cic_token);
      $rc = $response['total'];
      $hasDevice = ($rc>0)?true:false;
      $checkStatus = totpentitlement($userid,$base_url,$cic_token);
      if($checkStatus['enabled']==1){
        $_SESSION["totp_id"] = $checkStatus["id"];
        $_SESSION['totp_verified'] = $checkStatus["verified"];
        $_SESSION['totp_enabled'] = $checkStatus["enabled"];
      }
      $app->render("enrollments.php", array(
          'launchpadURL'=>$base_url,
          'sso_firstname'=>$attributes['firstname'][0],
          'sso_lastname'=>$attributes['lastname'][0],
          'username'=> $_SESSION["username"],
          'userid'=> $_SESSION["user_id"],
          'response' => $response,
          'rc'=>$rc,
          'enabled'=>$hasDevice,
          'verified'=>$_SESSION['totp_verified']
          )
        );
    }

});
$app->get('/logout', function() {
  global $app;
  global $as;
  global $base_url;
  $as->logout($base_url);
});
$app->post('/testPush', function () {
    global $app;
    global $attributes;
    global $base_url;
    global $verifyTitle;
    global $verifyMessage;

    if(isset($attributes['mail'][0])){
      $_SESSION['loggedin'] = true;
      $_SESSION["username"] = $attributes['mail'][0];
      $_SESSION["user_id"] = $attributes['uid'][0];
    }
    if($_SESSION['loggedin'] && ($app->request->get('userid') == $_SESSION["user_id"])){
      $token = connectAPI();
      $decode = json_decode($token, true);
      $cic_token = $decode["access_token"];
      $testPush = appPushVerify($verifyMessage, $verifyTitle, $app->request->get('auth_id'), $base_url, $cic_token);

      if($testPush){
        echo $testPush;
      }
      else{
        echo "Something went wrong";
      }
    }
    else{
      echo "Unauthenticated Session";
    }
});
$app->get('/verifyPush', function () {
    global $app;
    global $attributes;
    global $base_url;
    global $verifyTitle;
    global $verifyMessage;

    if(isset($attributes['mail'][0])){
      $_SESSION['loggedin'] = true;
      $_SESSION["username"] = $attributes['mail'][0];
      $_SESSION["user_id"] = $attributes['uid'][0];
    }
    if($_SESSION['loggedin']){
      $token = connectAPI();
      $decode = json_decode($token, true);
      $cic_token = $decode["access_token"];
      $vPush = getVerificationTransaction($app->request->get('tid'), $app->request->get('auth_id'), $base_url, $cic_token);
      if($vPush[0]['state']=="VERIFY_SUCCESS"){
        echo "1";
      }
      elseif($vPush[0]['state']=="USER_DENIED") {
        echo "2";
      }
      else{
        echo $vPush[0]['state'];
      }
    }
    else{
      echo "Unauthenticated Session";
    }
});
$app->post('/deleteApp', function () {
    global $app;
    global $attributes;
    global $base_url;
    global $verifyTitle;
    global $verifyMessage;
    global $sig_id;

    if(isset($attributes['mail'][0])){
      $_SESSION['loggedin'] = true;
      $_SESSION["username"] = $attributes['mail'][0];
      $_SESSION["user_id"] = $attributes['uid'][0];
    }

    if($_SESSION['loggedin'] && ($app->request->get('userid') == $_SESSION["user_id"])){
      $token = connectAPI();
      $decode = json_decode($token, true);
      $cic_token = $decode["access_token"];
      $testPush = appDelete($app->request->get('auth_id'), $base_url, $cic_token);
      echo $testPush;
    }
    else{
      echo "Unauthenticated Session";
    }
});
$app->post('/registrations', function () {
    global $app;
    global $base_url;
    $token = connectAPI();
    $decode = json_decode($token, true);
    $cic_token = $decode["access_token"];
    $_SESSION["username"] = $app->request->post('username');
    $credentials = array(
      uname => $app->request->post('username'),
      pword => $app->request->post('password')
    );
    //validate $credentials
    $loginUser = loginUser($credentials['uname'],$credentials['pword'],$base_url,$cic_token); // returns array if successful, false if not
    if(!$loginUser){
      $status = "failed";
      $app->render("login.php", array('status' => $status));
    }
    else {
      $userid = $loginUser["id"];
      $_SESSION['userid'] = $loginUser["id"];
      $checkStatus = totpentitlement($userid,$base_url,$cic_token);
      //print_r($checkStatus);
      if($checkStatus['enabled']==1){
        $_SESSION["totp_id"] = $checkStatus["id"];
        $_SESSION['totp_verified']=$checkStatus["verified"];
        $_SESSION['totp_enabled']=$checkStatus["enabled"];
        $app->render("process.php", array('launchpadURL'=>$base_url,'sso_firstname'=>$attributes['firstname'][0],'sso_lastname'=>$attributes['lastname'][0],'username'=> $_SESSION["username"],'id' => $checkStatus["id"],'owner' => $checkStatus["owner"],'verified' => $checkStatus["verified"],'enabled' => $checkStatus["enabled"]));
      }
      else{
        $app->render("process.php", array('enabled'=>false));
      }
    }
});
$app->get('/registrations', function () {
    global $app;
    global $base_url;
    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']){

      $app->render("process.php", array('launchpadURL'=>$base_url,'sso_firstname'=>$attributes['firstname'][0],'sso_lastname'=>$attributes['lastname'][0],'username'=> $_SESSION["username"],'id' => $_SESSION["totp_id"],'owner' => $_SESSION["user_id"],'verified' => $_SESSION['totp_verified'],'enabled'=> $_SESSION['totp_enabled']));
    }
    else{
      //not logged in
      $app->render("login.php");
    }
    //print_r($_SESSION);
});

$app->get('/create', function () {
    global $app;
    global $base_url;
    global $account_name;
    global $attributes;
    global $authclient_id;
    $token = connectAPI();
    $decode = json_decode($token, true);
    $cic_token = $decode["access_token"];
    $credentials = array(
      uname => $_SESSION["username"],
      id => $_SESSION["user_id"]
    );
    $create = appEnrollement($credentials['id'],$authclient_id,$credentials['uname'],$base_url,$cic_token);
    if($create == "fail"){
      $errormsg = "Error creating Verify registration";
        $app->render("create.php", array(
          'launchpadURL'=>$base_url,
          'sso_firstname'=>$attributes['firstname'][0],
          'sso_lastname'=>$attributes['lastname'][0],
          'error'=>true,
          'errormsg'=>$errormsg,
          'qr'=>false
        ));
      }
    else { // all good in the hood
      $error = false;
      $qr = $create; // sets the QR variable if $create is not false
      $app->render("create.php", array(
        'launchpadURL'=>$base_url,
        'sso_firstname'=>$attributes['firstname'][0],
        'sso_lastname'=>$attributes['lastname'][0],
        'qr'=>$qr,
        'error'=>false,
        'errormsg'=>false));
    }
});
$app->get('/verify', function () {
    global $app;
    global $attributes;
    global $base_url;
    if(isset($_SESSION['loggedin']) && isset($_SESSION['user_id']) && $_SESSION['loggedin']){
      $app->render("verify.php",array(
        'launchpadURL'=>$base_url,
        'sso_firstname'=>$attributes['firstname'][0],
        'sso_lastname'=>$attributes['lastname'][0],
        'error'=>false,
        'called'=>false
        )
      );
    }
});
$app->post('/verify', function () {
    global $app;
    global $base_url;
    global $attributes;
    $token = connectAPI();
    $decode = json_decode($token, true);
    $cic_token = $decode["access_token"];
    $userid = $_SESSION["user_id"];
    $checkStatus = totpentitlement($userid,$base_url,$cic_token);
    if($checkStatus['enabled']==1){

        $totp_token = $app->request->post('totp1').$app->request->post('totp2').$app->request->post('totp3').$app->request->post('totp4').$app->request->post('totp5').$app->request->post('totp6');
        $totp_id = $checkStatus["id"];
        $verify = totpverify($totp_token, $totp_id, $base_url, $cic_token);
        //print_r($verify);
        if($verify){ // all good in the hood
            $error = false;
            $app->render("verify.php", array(
              'launchpadURL'=>$base_url,
              'sso_firstname'=>$attributes['firstname'][0],
              'sso_lastname'=>$attributes['lastname'][0],
              'error'=>false,
              'called'=>true
            ));
          } else { // no good
            $error = true;
            $app->render("verify.php", array(
              'launchpadURL'=>$base_url,
              'sso_firstname'=>$attributes['firstname'][0],
              'sso_lastname'=>$attributes['lastname'][0],
              'error'=>true,
              'called'=>true));
          }
    }
});
$app->post('/delete', function () {
    global $app;
    global $base_url;
    //print_r($_SESSION);
    $token = connectAPI();
    $decode = json_decode($token, true);
    $cic_token = $decode["access_token"];
    $totp_id=$_SESSION['totp_id'];
    $delete = totpdelete($totp_id,$base_url,$cic_token);
    //print("msg:". $delete);
    if($delete == 1){
      $app->render("process.php", array(
        'launchpadURL'=>$base_url,
        'sso_firstname'=>$attributes['firstname'][0],
        'sso_lastname'=>$attributes['lastname'][0],
        'enabled'=>false,
        'deleted'=>"complete"
      ));
      unset($_SESSION['totp_id']);
      unset($_SESSION['totp_verified']);
      unset($_SESSION['totp_enabled']);
    }
    else{
      //not logged in
      $app->render("process.php", array(
        'launchpadURL'=>$base_url,
        'sso_firstname'=>$attributes['firstname'][0],
        'sso_lastname'=>$attributes['lastname'][0],
        'enabled'=>false,
        'deleted'=>"error"));
    }
});
$app->get('/sessionkill', function () {
    global $app;
    global $as;
    global $base_url;
    session_destroy();
    $as->logout('/');
});

$app->run();
