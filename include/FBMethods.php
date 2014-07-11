<?php	 
include 'facebook.php';
require_once ("constants.php");

/*
*	FBMethods Class to connect to facebook api
* 	and provide abstraction for easy access of FB Data...
*/
/*
	List of METHODS
	1 	isLiked
	2 	isAdmin
	3 	isOpenedFromPage
	4 	getPageUrl
	5 	getFBScript
	6 	login
	7 	isAuthorized
	8 	getCurrentPermissions
	9 	getAccessToken
	10 	setAccessToken
	11 	setLongLivedToken
	12 	getName
	13 	getFBID
	14 	api
	15 	fql
	16 	postToFeed
	17 	sendNotification
*/
class FBMethods
{
//Class level variables....
private $appId,$appSecret,$appDir,$appNamespace,$pageId,$pageNamespace,$appAccessToken;
private $initialized;

//Publicly keeping $facebook object..Contains Core Facebook Class Object.. so that core methods can be run directly...
public $facebook;
//Publicly keeping $request object... Contains Signed Request.. So that core things can be accessed... 
public $request;

	/*
	Use this array for custom initialization....
	$config = array("appId"=>,"appSecret"=>,"appDir"=>,"appNamespace"=>,"pageId"=>,,"pageNamespace"=>,"appAccessToken"=>);
	List of params
	appId
	appSecret
	appDir
	appNamespace
	pageId
	pageNamespace
	appAccessToken
	*/

	/*
	*	Constructor to initialize configurations to connect to facebook
	*	Config can be provided by config array
	*	OR WILL BE DIRECTLY PICKED FROM "./constants.php" FILE...
	*	
	*	@param 		config 	Array Optional 		Optional array containing all the configurations told above... 	
	*/
	public function __construct($config="")
	{
		$this->initialized=false;
		if(!empty($config))
		{
			//Configuration picked from config array provided by user....
			extract($config);
			$this->appId= !empty($appId)?$appId:"";
			$this->appSecret= !empty($appSecret)?$appSecret:"";
			$this->appDir= !empty($appDir)?$appDir:"";
			$this->appNamespace= !empty($appNamespace)?$appNamespace:"";
			$this->pageId= !empty($pageId)?$pageId:"";
			$this->pageNamespace= !empty($pageNamespace)?$pageNamespace:"";
			$this->appAccessToken= !empty($appAccessToken)?$appAccessToken:"";
		}
		else
		{
			//Configuration automatically picked from "constants.php" file....
			$this->appId= defined('APPID')?APPID:"";
			$this->appSecret= defined('APPSECRET')?APPSECRET:"";
			$this->appDir= defined('APPDIR')?APPDIR:"";
			$this->appNamespace= defined('APPNAMESPACE')?APPNAMESPACE:"";
			$this->pageId= defined('PAGEID')?PAGEID:"";
			$this->pageNamespace= defined('PAGENAMESPACE')?PAGENAMESPACE:"";
			$this->appAccessToken= defined('APPACCESSTOKEN')?APPACCESSTOKEN:"";
		}
		if(!empty($this->appId) && !empty($this->appSecret))
		{
			$this->facebook = new Facebook(array(
						        'appId' => $this->appId,
						        'secret' => $this->appSecret,
						        'cookie' => true,
	        					));
			$this->facebook->setFileUploadSupport(true);
			$this->request = $this->facebook->getSignedRequest();
			$this->initialized=true;
		}
	}

	/*
	*	Function to check if the Page from which the app is opened is liked by the user.
	*	Depends on signed request.. If there is no signed request.. will always return FALSE...
	*
	*	@return 	status 	Boolean 	 Returns boolean value signifying whether the page is liked or not...
	*/
	public function isLiked()
	{
		if($this->initialized && !empty($this->request))
			return $this->request["page"]["liked"];
		else
			return false;
	}

	/*
	*	Function to check if the user opening the app if page's admin
	*	Depends on signed request.. If there is no signed request.. will always return FALSE...
	*
	*	@return 	status 	Boolean 	 Returns boolean value signifying whether the user is app's admin or not...
	*/
	public function isAdmin()
	{
		if($this->initialized && !empty($this->request))
			return $this->request["page"]["admin"];
		else
			return false;
	}

	/*
	*	Function to check if the App is opened from page or not...
	* 	Very helpful to check if the app is opened from page or canvas...
	*
	*	@return 	status 	Boolean 	 Returns boolean value signifying whether the app is opened from page or not...
	*/
	public function isOpenedFromPage()
	{
		if($this->initialized && !empty($this->request))
			return !empty($this->request["page"]["id"]);
		else
			return false;
	}

	/*
	*	Function to get the page tab url of app... can be used to redirect in case app is not opened from page...
	*	Will only work if PAGE NAMESPACE is defined in constants or given in cofig array or explicitely passed...
	*
	*	@param 		data 	String 	Optional 	Data that you want to send along with the url.. This data will be accessible through signed request as $request['app_data']
	*	@return 	url 	String 	 			Returns constructed path to the page tab.. or blank string...
	*/
	public function getPageUrl($data="")
	{
		if(!empty($this->pageNamespace) && !empty($this->appId))
		{	
			if(!empty($data))
				return "http://www.facebook.com/$this->pageNamespace/app_$this->appId?app_data=".$data;
			else
				return "http://www.facebook.com/$this->pageNamespace/app_$this->appId";
		}
		else
			return "";
	}
	
	/*
	*	Function to simply get the facebook script which needs to be included on the page....
	*	Preferably print the returned string at the bottom of the page in the app...
	*
	*	@return 	scipt 	String 		Facebook script to be included on the page...
	*/
	public function getFBScript()
	{
		$script=<<<SCRIPT
		<div id="fb-root"></div>
		<script>
		var FBObject;
		window.fbAsyncInit = function() {
					FB.init({
					appId: '{$this->appId}',
					cookie: true,
					xfbml: true,
					frictionlessRequests: true,
					oauth: true
				});
				FB.Canvas.setAutoGrow(true);
				FB.Canvas.setSize({height:100});
				FB.Canvas.scrollTo(0,0);
				FBObject = FB;
			};

			(function() {
				var e = document.createElement('script'); e.async = true;
				e.src = document.location.protocol +
				'//connect.facebook.net/en_US/all.js';
				document.getElementById('fb-root').appendChild(e);

			}());
		</script>
SCRIPT;
		return $script;
	}

	/*
	*	Function to create and print login url of facebook.. 
	*	ITS USER'S RESPONSIBILITY TO CALL die() ON PAGE AFTER CALLING THIS FUNCTION....
	*
	*	@param 		permission 		String 		Optional 	The permissions that you want to request for...
	*	@param 		redirect_uri	String 		Optional 	The url to which fb redirects after getting the permissions...
	*
	*/
	public function login($permission="",$redirect_uri="")
	{
		if($this->initialized)
		{
			//If the redirect_uri is empty... Default page url is got and sent as redirect uri...
			if(empty($redirect_uri))
				$redirect_uri=self::getPageUrl();

			//Create parameter array for getLoginUrl function..
			$params = array(
			  'scope' => "{$permission}",
			  'redirect_uri' => "{$redirect_uri}"
			);
			$loginUrl = $this->facebook->getLoginUrl($params);
			echo "<script>window.top.location='{$loginUrl}'</script>";
		}
	}

	/*
	*	IMPORTANT function to get if the current user has the permissions being specified by the permission parameter.. 
	*	In a nutshell...FUNCTION RETURNS THE PERMISSIONS THAT THE APP DOES NOT HAVE....
	*
	*	THIS FUNCTION RETURNS THE FOLLOWING
	*
	*	CASES -----
	*	1) APP OPENED FOR THE FIRST TIME-----
	*	RETURN - ALL THE PERMISSIONS ARE RETURNED AS IT IS, BCOZ NO PERMISSION IS PRESENT AT THT TIME...
	*	2) NO ACCESS TOKEN PRESENT------
	*	RETURN - ALL THE PERMISSIONS ARE RETURNED AS IT IS, BCOZ THERE IS NO ACCESS TOKEN TO FIND THE PERMISSIONS
	*	3) VALID ACCESS TOKEN IS PRESENT ----
	*	IT FINDS OUT WHICH ALL PERMISSIONS ARE PRESENT IN THE ACCESS TOKEN..
	*	THEN COMPARES IT TO THE PERMISSIONS SPECIFIED IN THE PERMISSION PARAMETER...
	*	REMOVES THOSE PERMISSIONS WHICH IS ALREADY PRESENT...
	*	RETURN BACK THOSE PERMISSIONS WHICH THE ACCESS TOKEN DOES NOT HAVE 
	*	-------------------VERY IMPORTANT--------------------
	*	IF ALL THE PERMISSIONS BEING ASKED ARE ALREADY PRESENT... IT WILL RETURN "TRUE" IN STRING... NOT AS A BOOLEAN VALUE...
	*	SO CHECK AS FOLLOWS 
	*	$REMAININGPERMISSIONS = $FBMETHODSOBJECT->isAuthorized("email,userlikes");
	*	IF($REMAININGPERMISSIONS=="true")
	*	{//HAVE GOT ALL THE PERMISSIONS}
	*	ELSE
	*	{
	*	 //LOGIN
	*	 $FBMETHODSOBJECT->login($REMAININGPERMISSIONS);
	*	}
	*	-------------------------------------------------------
	*
	*	@param 		permissions 	String 		Optional 	The permissions that you want to request for...
	*	@return 	permissions 	String 					The permissions you does not have in COMMA SEPERATED STRING.. CAN BE DIRECTLY PASSED TO LOGIN FUNCTION...
	*
	*/
	public function isAuthorized($permissions="")
	{
		if($this->initialized)
		{

			$token=self::getAccessToken();
			// No access token present or no valid access token present...
			if(empty($token) || strpos($token,"|"))
				return $permissions;
			else
			{
				if(empty($permissions))
					return "true";

				$already= self::getCurrentPermissions();
				$permissions = explode(",",$permissions);
				$toTake=array();
				$toTake=array_diff($permissions,$already);
				if(!empty($toTake))
					return implode(",", $toTake);
				else
					return "true";
			}
		}
		else
			return $permissions;
	}

	/*
	*	Function to get the current permissions present in an access token...
	*	Either specify the access token yourself or the access token of the current context will be picked
	*	It returns an array containing the permissions...
	*	IT WILL REMOVE ALL THE BASIC PERMISSIONS OF FACEBOOK FROM THE ARRAY...
	*
	*	@param 		accessToken		String 		Optional 	The accessToken whose set of permissions need to be got....
	*	@return		permissions		Array 				 	The array containing the permissions that are present in access token except basic permissons..
	*
	*/
	public function getCurrentPermissions($accessToken="")
	{
		$token = empty($accessToken)?self::getAccessToken():$accessToken;
		$qry="/me/permissions?limit=1000&access_token=".$token;
		$data=$this->facebook->api($qry);
		//var_dump($data["data"][0]);
		unset($data["data"][0]["installed"]);
		unset($data["data"][0]["user_friends"]);
		unset($data["data"][0]["basic_info"]);

		return array_keys($data["data"][0]);
	}

	/*
	*	Function to get the current access token from the core facebook object...
	*
	*	@return		accessToken		String	 	Current Access Token
	*
	*/
	public function getAccessToken()
	{
		if($this->initialized)
			return $this->facebook->getAccessToken();
		else
			return "";
	}
	/*
	*	Function to set the current access token to the core facebook object...
	*
	*	@param		accessToken		String	 	Optional 	Access Token to be set
	*
	*/
	public function setAccessToken($accessToken="")
	{
		if(!empty($accessToken))
		{
			$this->facebook->setAccessToken($accessToken);
			$this->request= $this->facebook->getSignedRequest();
			$this->initialized=true;
		}
	}
	/*
	*	Function to convert the access token into long lived access token and set it in the current context...
	*	If accessToken is left blank... The current context's access token is fetched and converted...
	*
	*	@param		accessToken		String	 	Optional 	Access Token convert into long lived access token
	*
	*/
	public function setLongLivedToken($accessToken="")
	{
		if($this->initialized)
		{
			$accessToken="";
			if(empty($accessToken))
				$accessToken=self::getAccessToken();

			$url="https://graph.facebook.com/oauth/access_token?client_id={$this->appId}&client_secret={$this->appSecret}&grant_type=fb_exchange_token&fb_exchange_token={$accessToken}";
			$result=file_get_contents($url);
			$op=explode('=',$result);
			$op=explode('&',$op[1]);
			$new_token=$op[0];
			$this->facebook->setAccessToken($new_token);
		}
	}
	/*
	*	Function to get the name of the user currently being referenced....
	*
	*	@return 	name 	String 		Returns name of the user if class is initialized or blank string...
	*	@see 		api
	*/
	public function getName()
	{
		if($this->initialized)
			{
				$data = self::api("me?fields=name");
				return $data["name"];
			}
		else
			return "";
	}
	/*
	*	Function to get the facebook id of the user currently being referenced....
	*
	*	@return 	id 	String 		Returns id of the user if class is initialized or blank string...
	*	@see 		api
	*/
	public function getFBID()
	{
		if($this->initialized)
		{
			$data = self::api("me?fields=id");
			return $data["id"];
		}
		else
			return "";
	}
	/*
	*	Function to get all the friends id of the current user in a single dimensional array....
	*
	*	@return 	friendsId 	Array 		Returns a single dimensional array of friends id...
	*	@see 		fql
	*/
	public function getFriendsId()
	{
		if($this->initialized)
		{
			$data = self::fql("select uid2 from friend where uid1 = me()");
			$friends = array();
			foreach ($data as $key => $value) {
				$friends[] = $value["uid2"];
			}
			return $friends;
		}
		else
			return "";
	}

	/*
	*	Function to run api method on the core facebook's object...
	*	If accessToken is left blank... The current context's access token is fetched and converted...
	*
	*	@param		param	String/Array	parameters to send to api method
	*	@return 	data 	Array 			Array containing the response data or error message
	*
	*/
	public function api($param,$method = 'GET', $data = NULL)
	{
		if($this->initialized)
		{
			if(self::isAuthorized()!="true")
				self::login();
			try
			{
				if($data == NULL)
				$data=$this->facebook->api($param);
				else
				$data=$this->facebook->api($param,$method,$data);
				return $data;
			}
			catch(Exception $q)
			{
				return $q->getMessage();
			}
		}
	}
	/*
	*	Function to run fql queries...
	*
	*	@param		query	String		The fql query to run...
	*	@return 	data 	Array 		Array containing the response data or error message
	*
	*/
	public function fql($query)
	{
		if($this->initialized)
		{
			$param  =   array(
		           'method'    => 'fql.query',
		           'query'     => $query,
		           'callback'  => ''
		       );
			$data = self::api($param);
			return $data;
		}
	}

	/*
	*	Function to POST TO FEED of current user
	*
	*	Switch
		1 	- 	Status Update
				Params - message (compulsory)
		2 	- 	Feed Post
				Params - link (compulsory),name,picture,caption,description,source,place,tag,message
		3 	- 	Post photo on wall
				Params - url (compulsory), text
	*
	*	@param		switch	String		switch to select which type of post is it.. Status, feed or photo
	*	@param		params	Array		Array containing the data specified above
	*	@return 	data 	Array 		Response from facebook or blank string
	*
	*/
	public function postToFeed($switch,$params)
	{
		if($this->initialized)
		{
			$permissions = self::getCurrentPermissions();
			switch ($switch) 
			{
				case 1:
				case 2:
					if(!in_array("publish_actions", $permissions))
						self::login("publish_actions");
					$data=array();
					if($switch==1)
					{
						if(!isset($params["message"]))
							return "";
						else
							$data["message"]=$params["message"];
					}
					else
					{
						if(!isset($params["link"]))
							return "";
						else
							{
							$data["link"]=$params["link"];
							$data["name"]=!empty($params["name"])?$params["name"]: "";
							$data["picture"]=!empty($params["picture"])?$params["picture"]: "";
							$data["caption"]=!empty($params["caption"])?$params["caption"]: "";
							$data["description"]=!empty($params["description"])?$params["description"]: "";
							$data["source"]=!empty($params["source"])?$params["source"]: "";
							$data["place"]=!empty($params["place"])?$params["place"]: "";
							$data["tag"]=!empty($params["tag"])?$params["tag"]: "";
							$data["message"]=!empty($params["message"])?$params["message"]: "";
							}
					}
					$ret_obj = $this->facebook->api('/me/feed', 'POST',$data);
				   	return $ret_obj;
				   	break;
				case 3:
					if(!in_array("publish_stream", $permissions))
						self::login("publish_stream");
			 		$data =array();
			 		if(!isset($params["url"]))
			 			return "";
			 		else
			 			$data["url"]=$params["url"];
			 		if(isset($params["text"]))
			 			$data["text"]=$params["params"];
				  	$ret_obj = $this->facebook->api('/me/photos', 'POST',$data);
				   	return $ret_obj;
				   	break;
			}
				
		}
		else
		{
			
		}
	}

	/*
	*	Function to send Notification... Notification can only be sent to those users who have authorized the app..
	*	The canvas settings of the app must be initialized to use this method..
	*
	*	@param		id			String				The id of the user to whom you want to send the notification
	*	@param		template	String				The text containing what to post in notification.. max 180 characters.. To include a person's name write... @[<FACEBOOKID>]
	*	@param		href		String	Optional	The location to open on clicking the notification. It is a relative path appended to the canvas url of the app.
	*	@return 	data 		Array 				Array containing the response data or error message
	*
	*/
	public function sendNotification($id,$template,$href="")
	{
		if($this->initialized)
		{
			$arr = array("template"=>$template,
						"href"=>$href,
						"access_token"=>$this->appAccessToken);
			$data = $this->facebook->api("/{$id}/notifications","POST",$arr);
			return $data;
		}
	}


}

 ?>