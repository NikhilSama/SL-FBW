           

      function sendRequest() {
     
        FB.ui({method: 'apprequests',
          message: "Free Service Contest"
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


      function postToFeed() {

        // calling the API ...
        var obj = {
          method: 'feed',
          redirect_uri: '',
          link: '',
          picture: "",
          name: '',
          caption: '',
          description: ""
        };
         function callback(response) {
          document.getElementById('msg').innerHTML = "Post ID: " + response['post_id'];
        }

        FB.ui(obj, callback);
      }

      // Permissions that are needed for the app
    var permsNeeded = ['email', 'manage_pages'];
    
    // Function that checks needed user permissions
    var checkPermissions = function() {
        FB.getLoginStatus(function(response) {
            //If authorized...
            if(response.status=="connected") {
                token = response.authResponse.accessToken;
                FB.api('/me/permissions', function(response) {
                    var permsArray = response.data[0];
                    var permsToPrompt = [];
                    for (var i in permsNeeded) {
                        if (permsArray[permsNeeded[i]] == null) {
                            permsToPrompt.push(permsNeeded[i]);
                        }
                    }
                    
                    if (permsToPrompt.length > 0) {
                        permsToPrompt.join(',');
                        promptForPerms(permsToPrompt);
                    } else {
                        // console.log("Not Permissted");
                        window.location='pagelist.php';
                    }
                });
            } else if (response.status === 'not_authorized') { //user is not authorized... Ask for permissions...
                //loggin in...
                var permsToPrompt = [];
                for (var i in permsNeeded) {
                    permsToPrompt.push(permsNeeded[i]);
                }
            
                if (permsToPrompt.length > 0) {
                    permsToPrompt.join(',');
                    promptForPerms(permsToPrompt);
                } else {
                    // console.log("Not Permissted 2");
                    window.location='pagelist.php';
                }
            } else { //Not logged in in fb....
                alert("Please log in FB");
            }
        });
    };

    var checkProfilePermissions = function() {
        FB.getLoginStatus(function(response) {
            //If authorized...
            console.log(response);
            if(response.status=="connected") {
                token = response.authResponse.accessToken;
                FB.api('/me/permissions', function(response) {
                    console.log(response);
                    window.location='index.php?auth=authorized';
                });
            } else if (response.status === 'not_authorized') { //user is not authorized... Ask for permissions...
                console.log("Not Authorized");
                window.location='index.php?auth=not_authorized';
            }
        });
    };
    
    // Re-prompt user for missing permissions
    var promptForPerms = function(perms) {
        FB.login(function(response) {
            console.log(response);
            window.location='pagelist.php';
            // console.log("Permissted ");
        }, {scope: perms.join(',')});
    };

    var removePermissions = function(perms) {
        FB.api({
            method: 'auth.revokeExtendedPermission',
                perm: perms.join(',')
            },
            function(response) {
                console.log(response);
            }
        ); 
    };