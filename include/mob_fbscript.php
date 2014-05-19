<div id='fb-root'></div>
<script type="text/javascript">
            window.fbAsyncInit = function() {
                FB.init({
                    appId: '<?=$appId?>',
                    cookie: true,
                    xfbml: true,
                    frictionlessRequests: true,
                    oauth: true
                });

                FB.getLoginStatus(function(response) {
                console.log(response);
                if (response.status === 'connected') {
                  // the user is logged in and has authenticated your app
                  var uid = response.authResponse.userID;
                  var accessToken = response.authResponse.accessToken;
                } else if (response.status === 'not_authorized') {
                  // the user is logged in to Facebook, 
                  // but has not authenticated your app

                } else {
                  // the user isn't logged in to Facebook.
                }
               });

                FB.Event.subscribe('edge.create',
                    function(response) {
                     // alert();
                        window.location.reload();
                        //alert('You liked the URL: ' + response);
                    }
                );

                FB.Event.subscribe('edge.remove',
                    function(response) {
                     // alert();
                        window.location.reload();
                        //alert('You liked the URL: ' + response);
                    }
                );

    

           };
            (function() {
                var e = document.createElement('script'); e.async = true;
                e.src = document.location.protocol +
                    '//connect.facebook.net/en_US/all.js';
                document.getElementById('fb-root').appendChild(e);
            }());

      function sendRequest() {
     
        FB.ui({method: 'apprequests',
          message: "Skin O2 giveaway"
        }, requestCallback);
      }
      function requestCallback(response)
      {
        if(response)
        {
            //console.log(response);
              var ids = response["to"];
              var idStr = "";
            for (var i = 0; i < ids.length; ++i)
            {
                idStr = idStr +ids[i] + "|";          
            }
                     $.ajax({
                          url: 'sendinvites.php',
                          data:{'reqid':response.request,'to':idStr,'uid':'<?=$me[id]?>','contestid':'<?=$cid?>','request':1},
                          type:'POST',
                          success: function(data) {
                          //alert(data);
                          }
                        });
        }
      }


     function loginUser()
        {    
             FB.login(function(response) { 
                     if (response.authResponse) {
                         console.log(response);
                         FB.api('/me', function(response) {
                           alert('Good to see you, ' + response.name + '-'+ response.email);
                         });
                       } else {
                         alert('User cancelled login or did not fully authorize.');
                         window.location.reload();
                       }

             }, {scope:'email'});     
         }

</script>