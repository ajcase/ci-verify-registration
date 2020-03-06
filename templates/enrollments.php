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
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="/w3ds.js"></script>
    <script type="text/javascript">
    $(document).ready( function () {
        $('#log').DataTable();
    } );
    function testPush(userid, client_id, auth_id, device_name){
       $.ajax({
        method: "POST",
        url: "./index.php/testPush?auth_id="+auth_id+"&authclient_id="+client_id+"&userid="+userid
       })
       .done(function(data) {
          var content = "Waiting for response from "+device_name+"...";
          $("#pushNoticeText").text(content)
          $("#pushNotice").removeClass("ds-hide")
          $("#sendPush").removeClass("ds-disabled")
          function worker(){
            $.ajax({
               method: "GET",
               url: "./index.php/verifyPush?tid="+data+"&auth_id="+auth_id
              })
              .done(function(result) {
                    if(result==1){
                      var vc = device_name+" verified the transaction successfully.";
                      $("#verifyNoticeText").text(vc)
                      $("#verifyNotice").removeClass("ds-hide")
                      $("#pushNotice").addClass("ds-hide")
                    }
                    else if(result==2){
                      var dt = device_name+" successfully denied the transaction.";
                      $("#delNoticeText").text(dt)
                      $("#delNotice").removeClass("ds-hide").addClass( "ds-show")
                      $("#delPush").removeClass("ds-disabled")
                      $("#pushNotice").addClass("ds-hide")
                    }
              });
          }
          var counter = 0;
          function watch()
          {
              if (counter < 6){
                  counter++
                  worker();
                  window.setTimeout(watch, 4000); // perform every 4 seconds
              }
              else{
                $("#pushNotice").addClass("ds-hide")
                var dt = device_name+" did not respond to the transaction request.";
                $("#delNoticeText").text(dt);
                $("#delNotice").removeClass("ds-hide").addClass( "ds-show");
              }
          }
          watch();
       });
    }
    function deleteApp(userid, auth_id, device_name){
       $.ajax({
        method: "POST",
        url: "./index.php/deleteApp?auth_id="+auth_id+"&userid="+userid
       })
       .done(function(data) {
        var content = "Successfully deleted device "+device_name+". This page will refresh automatically.";
        $("#delNoticeText").text(content)
        $("#delNotice").removeClass("ds-hide").addClass( "ds-show")
        $("#delPush").removeClass("ds-disabled")
        setTimeout(function () { location.reload(true); }, 3000);
       });
    }
    function changeclass(element) {
        $(element).addClass('ds-disabled');
    }
    var dismissibleElement = document.querySelector('.ds-dismissible')
    w3ds.dismissible(dismissibleElement)
    </script>
  </head>
  <body class="ds-has-sticky-footer">
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
                  </ul>
              </div>
          </div>
          <div class="ds-row ds-padding-top-1">
            <div class="ds-col-xs-12 ds-col-sm-12 ds-col-lg-8 ds-col-md-10 ds-offset-md-1 ds-offset-lg-2 ds-padding-bottom-3">
                <div class="ds-panel ds-panel-raised ds-panel-rounded ds-panel-floating ds-slide-up ds-animation-delay-4">
                    <div class="ds-panel-header">
                        <h2 class="ds-margin-top-0 ds-margin-bottom-0">Device Registrations</h2>
                    </div>
                    <div class=" ds-panel-container ds-bg-neutral-2">
                      <div class="ds-row">
                    <?php if($enabled!=1){ ?>
                        <p class="ds-align-text-center">No device registrations found</p>
                        <div style="width:100%;height:225px;background-image: url(/images/emptydevice.svg);background-position: center 75px;background-position-x: center;background-position-y: 25px;background-size: 411px;background-repeat: no-repeat;"></div>
                        <div class="ds-col-6 ds-offset-3"><a href="./index.php/create" class="ds-button ds-width-auto" style="margin:auto;">Register a new device</a></div>
                      <?php
                    }else{
                      ?>
                        <div id="pushNotice" class="ds-col-xs-12 ds-alert ds-bg-contextual-yellow-1 ds-slide-down ds-animation-10 ds-hide">
                            <p id="pushNoticeText">Push Sent</p>
                        </div>
                        <div id="verifyNotice" class="ds-dismissible ds-col-xs-12 ds-alert ds-success ds-slide-down ds-animation-10 ds-hide">
                            <button class="ds-close ds-button ds-flat ds-close-button ds-bg-transparent" aria-label="dismiss alert"><span class="ds-icon-close"></span></button>
                            <p id="verifyNoticeText">Verified</p>
                        </div>
                        <div id="delNotice" class="ds-dismissible ds-col-xs-12 ds-alert ds-error ds-slide-down ds-animation-10 ds-hide">
                          <button class="ds-close ds-button ds-flat ds-close-button ds-bg-transparent" aria-label="dismiss alert"><span class="ds-icon-close"></span></button>
                            <p id="delNoticeText">Device Deleted</p>
                        </div>
                        <div class="ds-col-4 ds-margin-bottom-1">
                            <div class="ds-set-height-group-1 ds-panel ds-panel-raised">
                                <div class="ds-panel-container">
                                  <p class="ds-heading-5">
                                    One Time Passcode
                                  <p>
                                  <p class="ds-text-small">
                                      <b class="ds-font-weight-bold">Enabled:</b> <span class="ds-badge-small ds-bg-contextual-green-4">True</span></p>
                                  <p class="ds-text-small">
                                      <b class="ds-font-weight-bold">Verified:</b> <?php echo ($verified)?"<span class=\"ds-badge-small ds-bg-contextual-green-4\">True</span>":"<span class=\"ds-badge-small ds-danger\">False</span>";?>
                                  </p>
                                  <p class="ds-text-small">
                                      &nbsp;
                                  </p>
                                  <div class="ds-button-group-h ds-row ds-margin-bottom-1">
                                    <a href="./index.php/verify" class="ds-col-6 ds-button ds-truncate">
                                        <span class="ds-icon-more-filled-horizontal"></span> Verify
                                    </a>
                                  </div>
                                </div>
                            </div>
                        </div>
                      <?php
                      $x = 0;
                      while($x < $rc){
                        ?>
                        <div class="ds-col-4 ds-margin-bottom-1">
                            <div class="ds-set-height-group-1 ds-panel ds-panel-raised">
                                <div class="ds-panel-container">
                                  <p class="ds-heading-5">
                                    <?=$response['authenticators'][$x]['attributes']['deviceName']?>
                                  <p>
                                  <p class="ds-text-small">
                                      <b class="ds-font-weight-bold">Platform:</b> <?=$response['authenticators'][$x]['attributes']['platformType']?> (<?=$response['authenticators'][$x]['attributes']['osVersion']?>)</p>
                                  <p class="ds-text-small">
                                      <b class="ds-font-weight-bold">Device Type:</b> <?=$response['authenticators'][$x]['attributes']['deviceType']?>
                                  </p>
                                  <p class="ds-text-small">
                                      <b class="ds-font-weight-bold">App Version:</b> <?=$response['authenticators'][$x]['attributes']['applicationVersion']?>
                                  </p>
                                  <div class="ds-button-group-h ds-row ds-margin-bottom-1">
                                    <button id="sendPush" onClick="changeclass(this); testPush('<?=$userid?>','<?=$response['authenticators'][$x]['clientId']?>','<?=$response['authenticators'][$x]['id']?>','<?=addslashes($response['authenticators'][$x]['attributes']['deviceName'])?>');" class="ds-col-6 ds-button ds-truncate">
                                        <span class="ds-icon-mobile"></span> Test
                                    </button>
                                    <button id="delPush" class="ds-col-6 ds-button ds-danger ds-truncate" onClick="changeclass(this); deleteApp('<?=$userid?>','<?=$response['authenticators'][$x]['id']?>','<?=addslashes($response['authenticators'][$x]['attributes']['deviceName'])?>');" >
                                        <span class="ds-icon-minus-circle"></span> Delete
                                    </button>
                                  </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $x++;
                      }
                      ?>
                        <div class="ds-col-4 ds-margin-bottom-1">
                            <div class="ds-padding-1 ds-bg-blue-1 ds-set-height-group-1 ds-panel ds-panel-raised ds-flex ds-flex-fill ds-flex-align-items-center ds-flex-align-content-center">
                                    <a href="./index.php/create" class="ds-button ds-bg-blue-6">
                                      <span class="ds-icon-plus-circle"></span>
                                      New Device</a>
                            </div>
                        </div>

                  <?php } ?>
                </div>
              </div>
            </div>
              <?php
                    if(count($log)>0){
                    ?>
                  <div class="ds-panel ds-panel-raised ds-panel-rounded ds-panel-floating ds-slide-up ds-animation-delay-4 ds-margin-top-2">
                      <div class="ds-panel-header">
                          <h2 class="ds-margin-top-0 ds-margin-bottom-0">Action History</h2>
                      </div>
                      <div class=" ds-panel-container ds-bg-neutral-2">
                        <div class="ds-col-12 ds-margin-bottom-2">
                          <p class="ds-label">
                              Transaction Log
                          </p>
                            <div class="ds-table-container">
                                <table id="log" class="ds-table ds-table-compact ds-hover">
                                    <thead>
                                        <tr>
                                            <th>Device Name</th>
                                            <th>Test Type</th>
                                            <th>Creation Time</th>
                                            <th>Verify State</th>
                                            <th>Push Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      <?php $z = 0;
                                      while($z < count($log)){
                                         ?>
                                        <tr>
                                            <td><?=$log[$z]['deviceName']?></td>
                                            <td><?=($log[$z]['authType']=="userPresence")?"User Presence":"<img src=\"images/fingerprint.png\" style=\"height:28px;\">"?></td>
                                            <td><?=date("F jS, Y \@ h:ia \G\M\T", strtotime($log[$z]['creationTime']))?></td>
                                            <td>
                                              <?php if($log[$z]['state']=="VERIFY_SUCCESS"){
                                                echo "<span class=\"ds-badge-small ds-bg-contextual-green-4\">Success</span>";
                                              }elseif(strtolower($log[$z]['state'])=="pending"){
                                                echo "<span class=\"ds-badge-small ds-text-neutral-cool-8 ds-border-contextual-yellow-3 ds-bg-contextual-yellow-3\">".ucfirst(strtolower($log[$z]['state']))."</span>";
                                              }
                                              elseif(strtolower($log[$z]['state'])=="user_denied"){
                                                echo "<span class=\"ds-badge-small ds-danger\">Denied</span>";
                                              }
                                              else{
                                                echo "<span class=\"ds-badge-small ds-text-neutral-cool-8 ds-border-neutral-cool-2 ds-bg-neutral-cool-2\">".ucfirst(strtolower($log[$z]['state']))."</span>";
                                              }
                                              ?>
                                            </td>
                                            <td><?=(!(strtolower($log[$z]['pushStatus'])=="failed"))?"<span class=\"ds-badge-small ds-bg-contextual-green-4\">Success</span>":"<span class=\"ds-badge-small ds-danger\">Failed</span>";?></td>
                                        </tr>
                                        <?php $z++;
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                      </div>
                  </div>
                  <?php } ?>
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
