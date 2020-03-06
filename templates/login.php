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
    <link rel="stylesheet" href="https://adamcase.me/w3ds-3.3.1.css" type="text/css">
    <!-- if data tables are required -->
    <link rel="stylesheet" href="https://adamcase.me/w3ds-data-tables.css" type="text/css">
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
    <link rel="stylesheet" href="https://adamcase.me/w3ds-prism.css" rel="text/css" />
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript">
    function onLoadPage() {
        setFocus();
    }
    function setFocus() {
        document.getElementById("username").focus();
    }
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
      <!-- DO NOT TRANSLATE OR MODIFY any part of the hidden parameter(s) -->

      <!--
        The following block of code provides users with a warning message
        if they do not have cookies configured on their browsers.
        If this environment does not use cookies to maintain login sessions,
        simply remove or comment out the block below.
      -->

      <!-- BEGIN Cookie check block -->
      <!--
      DO NOT TRANSLATE anything inside the SCRIPT tag except the quoted
      string warningString and the first line of the JavaScript redirection
      instruction beginning with "// Uncomment the following ..."

      i.e.	var warningString = "Translate this string";
      -->
      <script type="text/javascript">
        var warningString = "%ERROR%";
        document.cookie = 'acceptsCookies=yes';
        if(document.cookie == ''){
          document.write(warningString);
        }
        else{
          document.cookie = 'acceptsCookies=yes; expires=Fri, 13-Apr-1970 00:00:00 GMT';
          // Uncomment the following line for JavaScript redirection
          // document.cookie = 'ISAMOriginalURL=' + encodeURIComponent(window.location) + "; Path=/;";
        }
      </script>
      <noscript>
        <b>WARNING:</b> To maintain your login session, make sure that<br/>
        your browser is configured to accept Cookies.
      </noscript>
    <div class="ds-grid">
      <div class="ds-row">
        <div class="ds-col-12">
          <div class="ds-row ds-padding-top-4">
            <div class="ds-col-xs-10 ds-col-lg-4 ds-col-md-6 ds-offset-xs-1 ds-offset-md-3 ds-offset-lg-4 ds-padding-bottom-3">
              <form class="login-container" method="POST" action="/registrations">
                <div class="ds-panel ds-panel-raised ds-panel-rounded ds-panel-floating">
                    <div class="ds-panel-header">
                        <h2 class="ds-margin-top-0 ds-margin-bottom-0">Corporate Single-Sign On</h2>
                    </div>
                    <div class="ds-panel-container ds-bg-neutral-2">
                      <?php if($status=="failed"){?><div id="errId"><div class="ds-col-12 ds-alert ds-error ds-margin-top-0">
                     <p><span class="ds-icon-alert"></span> Login Failed!</p></div>
                   </div><?}?>
                    <input TYPE="hidden" NAME="login-form-type" VALUE="pwd">
                    <input TYPE="hidden" NAME="token" VALUE="%CREDATTR{tagvalue_session_index}%">
                    <input type="text" class="ds-input ds-display-inline-block ds-margin-bottom-1" id="username" name="username" placeholder="email">
                    <input type="password" class="ds-input ds-display-inline-block ds-margin-bottom-1" id="password" type="password" name="password" autocomplete="off" placeholder="password">
                    <input class="ds-button ds-primary ds-no-expand ds-align-text-center ds-margin-bottom-0" type="submit" value="Login">
                    </div>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
    <footer class="ds-sticky-footer ds-grid ds-bg-neutral-warm-2" style="width: 100%; height: 90px;" >
      <div class="ds-row">
          <div class="ds-col-lg-12 ds-padding-bottom-1 ds-padding-top-1">
              <div id="clock">
                    12:55 PM
              </div>
          </div>

        <script>
          function updateClock(){var e=new Date,t=e.getHours(),a=e.getMinutes(),n=e.getSeconds();a=(10>a?"0":"")+a,n=(10>n?"0":"")+n;var o=12>t?"AM":"PM";t=t>12?t-12:t,t=0==t?12:t;var c=t+":"+a+" "+o;document.getElementById("clock").firstChild.nodeValue=c}updateClock(),setInterval("updateClock()",1e3);
        </script>
      </div>
    </footer>
  </body>
</html>
