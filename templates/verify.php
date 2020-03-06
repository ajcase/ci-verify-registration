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
                      <li class="ds-display-inline-block"><a href="#" class="ds-text-neutral-cool-7 ds-text-small">Verify</a></li>
                  </ul>
              </div>
          </div>
          <div class="ds-row ds-padding-top-1">
            <div class="ds-col-xs-12 ds-col-xl-4 ds-offset-xl-4 ds-col-lg-8 ds-col-md-8 ds-offset-md-2 ds-offset-lg-2 ds-padding-bottom-3">

                <div class="ds-panel ds-panel-raised ds-panel-rounded ds-panel-floating ds-slide-up ds-animation-delay-4">
                    <div class="ds-panel-header">
                        <h2 class="ds-margin-top-0 ds-margin-bottom-0">Verify Code</h2>
                    </div>
                    <div class="ds-panel-container ds-bg-neutral-2">
                      <?php if(!$error && $called){?>
                        <div id="complete"><div class="ds-col-12 ds-alert ds-success ds-margin-top-0 ds-margin-bottom-1">
                       <p>Verification Complete!</p></div><br>Your device is now ready to be used.<br> <br><a href="/"><button class="ds-button ds-secondary">Go Back</button></a>
                     </div>
                      <?
                      }
                      else{
                        if($error && isset($called)){
                        ?><div id="errId"><div class="ds-col-12 ds-alert ds-error ds-margin-top-0">
                       <p>Verification Failed, Try Again!</p></div>
                     </div><?}?>Please enter the 6-digit code from the IBM Verify App:<br><br>
                     <form id="verifycode" class="login-container" method="POST" action="./verify">
                        <input type="text" style="font-size:28px;font-weight:bold;width:15.5%;height:75px;text-align:center;" maxlength="1" class="ds-input ds-display-inline-block ds-margin-bottom-1" id="totp1" name="totp1" placeholder="">
                        <input type="text" style="font-size:28px;font-weight:bold;width:15.5%;height:75px;text-align:center;" maxlength="1" class="ds-input ds-display-inline-block ds-margin-bottom-1" id="totp2" name="totp2" placeholder="">
                        <input type="text" style="font-size:28px;font-weight:bold;width:15.5%;height:75px;text-align:center;" maxlength="1" class="ds-input ds-display-inline-block ds-margin-bottom-1" id="totp3" name="totp3" placeholder="">
                        <input type="text" style="font-size:28px;font-weight:bold;width:15.5%;height:75px;text-align:center;" maxlength="1" class="ds-input ds-display-inline-block ds-margin-bottom-1" id="totp4" name="totp4" placeholder="">
                        <input type="text" style="font-size:28px;font-weight:bold;width:15.5%;height:75px;text-align:center;" maxlength="1" class="ds-input ds-display-inline-block ds-margin-bottom-1" id="totp5" name="totp5" placeholder="">
                        <input type="text" style="font-size:28px;font-weight:bold;width:15.5%;height:75px;text-align:center;" maxlength="1" class="ds-input ds-display-inline-block ds-margin-bottom-1" id="totp6" name="totp6" placeholder="" onfocusout="$( '#verifycode' ).submit();">
                      <input class="ds-button ds-primary ds-no-expand ds-align-text-center ds-margin-bottom-0 ds-hide" type="submit" value="Verify">
                      <div class="ds-alert ds-warning ds-padding-1 ds-align-text-left">
                          <p><span class="ds-icon-alert ds-padding-right-0_5"></span><b>Note:</b> If you have multiple device registrations, only one has the time-based one time passcode.</p>
                      </div>
                      <div class="ds-row">
                        <div class="ds-col-lg-4 ds-offset-lg-8">
                          <a class="ds-button" href="/index.php">Skip<span class="ds-icon-caret-circle-right ds-padding-left-0_5"></span></a>
                        </div>
                    </div>
                    </form>
                      <?
                      }
                      ?>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php require('./templates/footer.php');?>
    <script>
          $(".ds-input").keyup(function () {
            if (this.value.length == this.maxLength) {
              var $next = $(this).next('.ds-input');
              if ($next.length)
                  $(this).next('.ds-input').focus();
              else
                  $(this).blur();
            }
        });
        </script>
  </body>
</html>
