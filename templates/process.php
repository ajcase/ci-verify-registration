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
    <script src="/w3ds-3.7.2.js"></script>
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

    <div class="ds-grid">
      <div class="ds-row">
        <div class="ds-col-12">
          <div class="ds-row ds-bg-neutral-8">
            <div class="ds-col-lg-2 ds-col-md-4 ds-padding-top-1 ds-padding-bottom-1">
              <img src="/images/logo.png" style="width:70%"/>
            </div>
            <div class="ds-col-lg-8 ds-col-md-8 ds-padding-top-1 ds-padding-bottom-1">
                &nbsp;
            </div>
            <div class="ds-col-lg-2 ds-col-md-2 ds-padding-top-1 ds-padding-bottom-1 ds-padding-right-1 ds-align-text-right ds-text-neutral-1">
                <?php echo $sso_firstname;?> <?php echo $sso_lastname;?>
            </div>
          </div>
          <div class="ds-row">
              <div class="ds-col-12 ds-padding-left-2" aria-label="breadcrumb" role="navigation">
                  <ul class="ds-padding-0">
                     <li class="ds-hide-xs ds-display-sm-inline-block"><a href="/" class="ds-text-neutral-6 ds-text-small">Device Registration</a></li>
                     <li class="ds-hide-xs ds-display-sm-inline-block ds-text-neutral-6 ds-text-small" role="text" aria-label="next">></li>
                      <li class="ds-hide-xs ds-display-sm-inline-block"><a href="/" class="ds-text-neutral-6 ds-text-small">My Devices</a></li>
                      <?php if($deleted=="complete" || $deleted=="error"){?>
                        <li class="ds-hide-xs ds-display-sm-inline-block ds-text-neutral-6 ds-text-small" role="text" aria-label="next">></li>
                        <li class="ds-display-inline-block"><a href="#" class="ds-text-neutral-cool-7 ds-text-small">Delete Enrollment</a></li>
                      <? } ?>
                  </ul>
              </div>
          </div>
          <div class="ds-row ds-padding-top-1">
            <div class="ds-col-xs-10 ds-col-lg-8 ds-col-md-8 ds-offset-xs-1 ds-offset-md-2 ds-offset-lg-2 ds-padding-bottom-3">

                <div class="ds-panel ds-panel-raised ds-panel-rounded ds-panel-floating ds-slide-up ds-animation-delay-4">
                    <div class="ds-panel-header">
                        <h2 class="ds-margin-top-0 ds-margin-bottom-0">Device Registrations</h2>
                    </div>
                    <div class="ds-panel-container ds-bg-neutral-2">

                      <?php if($deleted=="complete"){?>
                        <div id="complete"><div class="ds-col-12 ds-alert ds-success ds-margin-top-0 ds-margin-bottom-1">
                       <p>Registration Deleted!</p></div>
                     </div>
                      <?
                    }elseif($deleted=="error"){
                      ?>
                        <div id="complete"><div class="ds-col-12 ds-alert ds-error ds-margin-top-0 ds-margin-bottom-1">
                       <p>Deletion Error!</p></div>
                     </div>
                      <?
                    }

                    if($enabled!=1){ ?>
                        <p class="ds-align-text-center">No device registrations found</p>
                        <div style="width:100%;height:225px;background-image: url(/images/emptydevice.svg);background-position: center 75px;background-position-x: center;background-position-y: 25px;background-size: 411px;background-repeat: no-repeat;"></div>
                        <a href="index.php/create"><button class="ds-button ds-width-auto"  style="margin:auto;">Register a new device</button></a>
                      <?
                    }else{
                        ?>
                      <div class="ds-table-container">
                        <table class="ds-table">
                            <tbody>
                                <tr>
                                    <td>Username:</td>
                                    <td><?php echo $username;?></td>
                                    <td><a href="index.php/enrollments" class="ds-button ds-secondary ds-small">App Enrollments<span class="ds-badge ds-element ds-success">New!</span></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>ID:</td>
                                    <td><?php echo $id;?></td>
                                    <td>
                                        <form id="delete" action="index.php/delete" method="POST">
                                          <button class="ds-button ds-small ds-secondary">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Verified:</td>
                                    <td><?php echo ($verified)?"<div class=\"ds-badge ds-success\">TRUE</div>":"<div class=\"ds-badge ds-danger\">FALSE</div>";?></td>
                                    <td>
                                    <a href="index.php/verify" class="ds-button ds-secondary ds-small">Verify<?php echo (!$verified)?"<span class=\"ds-badge ds-element ds-danger\">!</span>":"";?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Enabled:</td>
                                    <td><?php echo ($enabled)?"<div class=\"ds-badge ds-success\">TRUE</div>":"<div class=\"ds-badge ds-danger\">FALSE</div>";?></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <? } ?>
                    </div>
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
