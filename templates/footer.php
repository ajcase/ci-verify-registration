<footer class="ds-sticky-footer ds-grid ds-bg-neutral-warm-2" style="width: 100%; height: 90px;" >
  <div class="ds-row">
    <div class="ds-col-lg-2 ds-padding-bottom-1 ds-padding-top-1">
        <div id="clock">
              12:55 PM
        </div>
    </div>
    <div class="ds-col-lg-2 ds-padding-bottom-1 ds-padding-top-1">
        <div class="ds-tag ds-border-contextual-red-3 ds-close ds-bg-contextual-red-3 ds-text-neutral-1" role="tag" aria-label="green tag">
          For Demonstration Purposes Only
        </div>
    </div>
    <div class="ds-col-lg-2 ds-offset-lg-6 ds-padding-top-1 ds-padding-bottom-1 ds-padding-right-1">
        <a href="<?php echo $launchpadURL;?>"><button class="ds-button">App Launchpad</button></a>
    </div>
    <script>
      function updateClock(){var e=new Date,t=e.getHours(),a=e.getMinutes(),n=e.getSeconds();a=(10>a?"0":"")+a,n=(10>n?"0":"")+n;var o=12>t?"AM":"PM";t=t>12?t-12:t,t=0==t?12:t;var c=t+":"+a+" "+o;document.getElementById("clock").firstChild.nodeValue=c}updateClock(),setInterval("updateClock()",1e3);
      var currentRequest = null;
    </script>
  </div>
</footer>
