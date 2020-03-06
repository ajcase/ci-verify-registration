<!DOCTYPE html>
<!-- Copyright (C) 2015 IBM Corporation -->
<!-- Copyright (C) 2000 Tivoli Systems, Inc. -->
<!-- Copyright (C) 1999 IBM Corporation -->
<!-- Copyright (C) 1998 Dascom, Inc. -->
<!-- All Rights Reserved. -->
<html style="height: 100%;">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="EN" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IBM CIV - Self Service Registration</title>
    <link rel="stylesheet" href="/w3ds-3.7.2.css" type="text/css">
    <!-- if data tables are required -->
    <link rel="stylesheet" href="/w3ds-data-tables.css" type="text/css">
    <style type="text/css">
        .ibm-logo{
            float:left;
            height:24px;
            display: block;
            top:42px;
        }
        #clock{
            font-size:36px;
        }

      </style>
    <!-- if code syntax highlighting is required -->
    <link rel="stylesheet" href="/w3ds-prism.css" rel="text/css" />
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript">
    function onLoadPage() {
        setFocus();
    }
    function setFocus() {
        document.getElementById("username").focus();
    }
    </script>
    <script>
    $(document).ready(function(){
        $("#step1button").click(function(){
            $("#step1").hide();//body
            $("#step1h").hide();//body
            $("#step2").show();//header
            $("#step2h").show();//header
        });
    });
    </script>

    <!-- ClickJack projection from:
     https://www.owasp.org/index.php/Clickjacking_Defense_Cheat_Sheet
    -->

    <style id="antiClickjack">body{display:none !important;}</style>

    <script type="text/javascript">
    if (self === top) {
        var antiClickjack = document.getElementById("antiClickjack");
        antiClickjack.parentNode.removeChild(antiClickjack);
    } else {
        top.location = self.location;
    }
    </script>
  </head>
  <body class="ds-has-sticky-footer" onload="onLoadPage()">

    <div class="ds-grid">
      <div class="ds-row">
        <div class="ds-col-12">
          <div id="header" class="ds-row ds-bg-neutral-8">
            <div class="ds-col-lg-2 ds-col-md-4 ds-col-sm-4 ds-col-xs-7 ds-padding-top-1 ds-padding-bottom-1">
              <img src="/images/logo.png" style="width:100%"/>
            </div>
            <div class="ds-col-lg-8 ds-col-md-4 ds-col-sm-4 ds-col-xs-1 ds-padding-top-1 ds-padding-bottom-1">
                &nbsp;
            </div>
            <div class="ds-col-lg-2 ds-col-md-4 ds-col-sm-4 ds-col-xs-4 ds-padding-top-1 ds-padding-bottom-1 ds-padding-right-1 ds-align-text-right ds-text-neutral-1">
                <?php echo $sso_firstname;?> <?php echo $sso_lastname;?><br> <a class="ds-text-contextual-blue-4" href="index.php/logout">Logout</a>
            </div>
          </div>
          <div class="ds-row">
              <div class="ds-col-12 ds-padding-left-2" aria-label="breadcrumb" role="navigation">
                  <ul class="ds-padding-0">
                      <li class="ds-hide-xs ds-display-sm-inline-block"><a href="/" class="ds-text-neutral-6 ds-text-small">Device Registration</a></li>
                      <li class="ds-hide-xs ds-display-sm-inline-block ds-text-neutral-6 ds-text-small" role="text" aria-label="next">></li>
                      <li class="ds-hide-xs ds-display-sm-inline-block"><a href="/" class="ds-text-neutral-6 ds-text-small">My Devices</a></li>
                      <li class="ds-hide-xs ds-display-sm-inline-block ds-text-neutral-6 ds-text-small" role="text" aria-label="next">></li>
                      <li class="ds-display-inline-block"><a href="#" class="ds-text-neutral-cool-7 ds-text-small">Create Enrollment</a></li>
                  </ul>
              </div>
          </div>
          <div class="ds-row ds-padding-top-1">
            <div class="ds-col-xs-12 ds-col-sm-12 ds-col-md-10 ds-col-lg-8 ds-col-md-8 ds-offset-md-1 ds-offset-lg-2 ds-padding-bottom-3">
              <div class="ds-panel ds-panel-raised ds-panel-rounded ds-panel-floating">
                  <div id="step1h" class="ds-panel-header">
                      <h2 class="ds-margin-top-0 ds-margin-bottom-0">Download IBM Verify</h2>
                  </div>
                  <div id="step2h" class="ds-panel-header" style="display:none;">
                      <h2 class="ds-margin-top-0 ds-margin-bottom-0">Scan QR Code</h2>
                  </div>
                  <div class="ds-panel-container ds-bg-neutral-2 ds-slide-up ds-animation-delay-4">
                  <?php if(!$error){?>
                    <div id="step1">
                      <img src="/images/ibmverify.png" style="width:200px" class="ds-float-right">
                      <ol class="ds-list-ordered">
                        <li>Launch the App Store® (iOS) or Google Play™ Store (Android™) app</li>
                        <li>Search for "IBM Verify"</li>
                        <li>Tap "Get" and "Install" to download the app</li>
                      </ol>
                      <a id="step1button" href="#" style="width:60%;" class="ds-button ds-width-auto ds-margin-top-1">Next Step: Connect your device</a>
                    </div>
                    <div id="step2" style="display:none;">
                        <p>Next, we need to connect the app to your account so it can do its magic. On your mobile device:</p>
                        <ol class="ds-list-ordered">
                          <li>Launch IBM Verify</li>
                          <li>Tap "Connect an Account"</li>
                          <li>Scan the QR Code below using your device's camera</li>
                        </ol>
                        <div class="ds-row ds-padding-left-1 ds-padding-right-1">
                          <div class="ds-col-4">
                              <div class="ds-panel">
                                  <div class="ds-panel-container">
                                      <img src="data:image/png;base64, <? echo $qr;?>" style="display: block;margin:auto;width:100%" alt="QR" />
                                  </div>
                              </div>
                          </div>
                        </div>
                      <a id="step2button" href="./verify" style="width:50%;" class="ds-button ds-margin-top-1">Next Step: Verify your device</a>
                    </div>
                  <? }else{
                    ?>
                      <div id="complete"><div class="ds-col-12 ds-alert ds-error ds-margin-top-0 ds-margin-bottom-1">
                     <p>There was an error: <? echo $errormsg; ?></p></div><br><a href="index.php"><button class="ds-button ds-secondary">Go Back</button></a>
                   </div>
                    <?}?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php require('./templates/footer.php');?>
  </body>
</html>
