<?php 
//------CONSTANTS FILE---------
//------KEEP CONSTANTS HERE----
//------PREFERABLY KEEP CONSTANTS NAME UPPERCASE----

//Required ... Without these the app wont run...
define("APPID","573559196064698");
define("APPSECRET","1ac4f42d2be503264089571d6929b515");
define("APPDIR","https://zecross.net/fb/snaplionfbw/");
define("APPNAMESPACE","snaplionfbw");
define("PAGEID","526001477410636");
define("PAGENAMESPACE","zecrossapptest");


//Needed for sendNotification method... 
//App Access Token can be got by sending request to.... 
//https://graph.facebook.com/oauth/access_token?client_id=<APPID>&client_secret=<APPSECRET>&grant_type=client_credentials
define("APPACCESSTOKEN","");

//----DATABASE CONNECTIVITY PARAMETERS-----
//Online
define("SERVER","pdb12.awardspace.com");
define("USERNAME","1025453_snaplion");
define("PASSWORD","SL#2014DBtest");
define("DB","1025453_snaplion");


//defining constants for the app to be installed on the other page
define("INSTALLED_APP_ID","573559196064698");
define("INSTALLED_APP_SECRET","1ac4f42d2be503264089571d6929b515");

//api key for snaplion
define("KEY","MMKSKAIzaSyA7bjgLuOCUMMKSK5BiLpMMKSK9KyuN0ynwBhiKCZ3RoMMKSK");
define("REGISTER_URL","http://api.snaplion.com/users/registeration.json");
define("ADD_DATA_URL","http://api.snaplion.com/users/add_content.json");
define("ADD_APP_URL","http://api.snaplion.com/users/create_app.json");
define("ADD_INGREDIENTS_URL","http://api.snaplion.com/users/add_ingredient.json");
define("GET_INGREDIENTS_URL","http://api.snaplion.com/users/app_ingredients.json");
define("SID_URL","http://api.snaplion.com/users/existing_user_login.json");

//Further define more constants here like TABLE names or any other info
define("USERS","snaplion_user");
define("PAGE","snaplion_page");
define("APPTAB_ID","snaplion_apptab");
define("CATEGORY","snaplion_category");
define("SUBCATEGORY","snaplion_subcategory");
define("VISIT","snaplion_visits");
?>