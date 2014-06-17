//Keep Script here

//To send ajax request use method...
//myajax.js should be included along with this file on main page...
//-----------------
//sendAjaxRequest("URL_to_sen_req_to",parameter_array,"response_type_eg_html_or_json","callbackfunction");
//-----------------
//Callback function is optional... 
//you can just send "" in the fourth parameter if you dont want any method to get invoked on success...
//write the success method as----
//function callbackfunction(response)
//{}

var INSTALLED_APP_ID = 573559196064698;
var pathToController = "AjaxMethods.php";
var circleLoader = $("#loadingCircle");

function sendAjaxRequest(urlName,dataMsg,datatype,successFunction) {
    $.ajax({
      	url: urlName,
      	method:"POST",
      	data: dataMsg,
      	dataType: datatype,
		success: function(msg) {
			console.log(msg);
        	if(successFunction!="")
         		eval(successFunction)(msg);
  		}
	});
}
  
var pageTarget = null;
//to install app on another page
$(".install-btn").click(function() {
	pageTarget = $(this);
	var data = new Object();
	data.action = 'install';
	data.id = $(this).data('id');
	data.pname = $(this).data('name');
	sendAjaxRequest(pathToController,data,'html','checkPageAdded');
});

function checkPageAdded(msg) {	
	//id returns page id
	var pageId = pageTarget.data('id');
	//appId gives app id of the app to be installed
	var appId = $("#installedAppId").val();
	
	var anchorTag = $("a[data-id='"+pageId+"']").attr("href","https://www.facebook.com/"+pageId+"?id="+pageId+"&sk=app_"+appId);
	button = $("button[data-id='"+pageId+"']");
	button.text("Go to App");
	// console.log(pageTarget.data('id'));
	//msg returns page access token
	console.log(msg);
}


var submitList = $("#submitList");

$("div.nonimport").on('click',function() {
	if( $("#submitList").length ) {	
		console.log(submitList.length);
		$(this).toggleClass("active");
		$(this).toggleClass("inactive");
	}
});

progressbar = $( "#progressbar" );
if(progressbar.length) {
	$( "#progressbar" ).progressbar({
      value: false
	});
}
var imageIngredients = $("img.appIngredients");
var responseText = $("#responsetext");
submitList.on("click", function(){
	if(! ($("input[type=checkbox]:checked.importSection").length) ) {
		alert("Please select atleast one item to submit");
	} else {
		circleLoader.show();
		itemList = {pageinfo : 0, events : 0, posts : 0, photos : 0, videos : 0}  //creating a new object

		$("input[type=checkbox]:checked.importSection").each(function() {
			var value = $(this).attr("name");
			//setting values to the object
			itemList[value] = 1;
		});

		// console.log(itemList);
		sendAjaxRequest("importprogress.php", itemList, 'text', 'importSuccess');
		// submitList.css("display","none");
		// imageIngredients.css("display","none");
		// progressbar.css("display","block");
		// responseText.css("display","none");
		FB.Canvas.setSize({width:800,height:800});
	}
});

$("body").on("click", ".laterImport", function(event){
	event.preventDefault();

	if(! ($(this).parents('.strip-12').find("input[type=checkbox]:checked.importSection").length) ) {
		alert("Please select checkbox first");
	} else {
		circleLoader.show();

		itemList = {pageinfo : 0, events : 0, posts : 0, photos : 0, videos : 0}  //creating a new object

		$("input[type=checkbox]:checked.importSection").each(function() {
			var value = $(this).attr("name");
			//setting values to the object
			itemList[value] = 1;
		});

		// console.log(itemList);
		sendAjaxRequest("importprogress.php", itemList, 'text', 'importSuccess');
		// submitList.css("display","none");
		// imageIngredients.css("display","none");
		// progressbar.css("display","block");
		// responseText.css("display","none");
		FB.Canvas.setSize({width:800, height:800});
		window.location = "imported.php";

		circleLoader.hide();
	}
});

function importSuccess(msg) {	
	// progressbar.css("display","none");
	// responseText.css("display","block");
	// imageIngredients.css("display","block");
	var output = JSON.parse(msg);
	console.log(output);
	if(output['output'] == 1) {	
		// console.log("hello");
		for(var key in output) {
	    	switch(key) {
	    		case 'events':
	    			$("#events").find("h5.itemCount").removeClass("displayNone").addClass("displayBlock").text("Total Events - "+output['events']);
	    		break;
	    		case 'videos':
	    			$("#videos").find("h5.itemCount").removeClass("displayNone").addClass("displayBlock").text("Total Videos - "+output['videos']);
	    		break;
	    		case 'album':
	    			$("#albums").find("h5.itemCount").removeClass("displayNone").addClass("displayBlock").text(output['album']+" Albums "+output['photo']+" Photos");
	    		break;
	    		case 'posts':
	    			$("#posts").find("h5.itemCount").removeClass("displayNone").addClass("displayBlock").text("Total Posts - "+output['posts']);
	    		break;
	    	}
		}

		if(! $("img.appIngredients").length ) {
			window.location = "imported.php";
		}
		
		responseText.html("<p>Your data was posted successfully</p>");
		responseText.css("border","1px solid green");
			
		submitList.css("display","block");
		imageIngredients.css("display","block");
		$("div.tick").each(function() {
			$(this).removeClass("tick");
			$(this).addClass("alreadyImported");
			$(this).siblings().children(".right").removeClass("unUpdate").addClass("autoUpdate");
			//setting values to the object
		});

		if( $(".alreadyImported").length == $(".checkboxes").length ) {
			submitList.css("display","none");
		}
	} else {	
		//if there was an error while posting
		responseText.html("<p>There was an error while posting your data, please check that if you have any items relating to this category</p>");
		responseText.css("border","1px solid red");
		submitList.css("display","block");
	}
	// circleLoader.hide();
}

$("body").on("click","div.tick, div.untick", function() {
	if( (progressbar).css("display") == "block" ) {
		return;
	} else {
		$(this).toggleClass("untick");
		$(this).toggleClass("tick");
	}
});

$(".closeMessage").on('click', function() {
	$("#confirmMessage").css("display","none");
	$(".closeMessageOverlay").css("display","none");
	$("div#coverUp").css("display","none");
});

$("button.snaplionLogin").on('click', function(){
	$("#floatingCirclesG").css({"display":"block","z-index":"1010"});
	var userLogin = new Object();
	//getting the email id and password
	userLogin.param = 'userLogin';
	userLogin.email = $("input[name='email']").val();
	userLogin.password = $("input[name='password']").val();
	sendAjaxRequest(pathToController,userLogin,'html','checkLogin');
});

function checkLogin(msg) {	
	$("#floatingCirclesG").css("display","none");
	console.log(msg);
	msg = msg.trim();
	if(msg == "error") {
		//showing the error message when wrong password is entered
		$(".errorMessage").css("display","block");
	} else if(msg == "success") {	
		//hiding the login divs when user has entered a valid password
		$("#confirmMessage").css("display","none");
		$("div#coverUp").css("display","none");
	}
}

$(document).on("click", "a.appLinkDiv",function(){
	//getting the page id of the div clicked upon
	var pageId = $(this).data("id");
	window.open( "https://www.facebook.com/"+pageId+"?id="+pageId+"&sk=app_"+INSTALLED_APP_ID,"_blank");
	// window.top.location.href = "https://www.facebook.com/"+pageId+"?id="+pageId+"&sk=app_"+INSTALLED_APP_ID,"_blank";
});

$("body").on("click", "span.toggleradio",function(){
	if( $(this).hasClass("activeRadio") ) {
		$(this).removeClass("activeRadio");
		$(this).addClass("inactiveRadio");
	} else {
		$("span.toggleradio").removeClass("activeRadio").addClass("inactiveRadio");
		$(this).toggleClass("inactiveRadio");
		$(this).toggleClass("activeRadio");
	}
});

$(document).on("click", ".selectedAppInstall", function(){
	circleLoader.show();

	$(this).parents('.uninstalledAppPage').find('.newAppRadio').prop('checked', true);
	var selectedRadio = $('input[type=radio]:checked.newAppRadio');
	// if(selectedRadio.length) {

		pageInstall = new Object();
		pageInstall.action = "install";
		pageInstall.id = selectedRadio.data("id");
		pageInstall.pname = selectedRadio.data("name");
		sendAjaxRequest(pathToController, pageInstall, 'html', 'installApp');

	// } else {
	// 	alert("Please Select Atleast One Page To Install App on");
	// }
});

$("img.nextStep").on("click", function(){

	if( !($("span.activeRadio").length) ) {
		alert("Please Select Atleast One Page To Install App on");
	} else {	
		$("#floatingCirclesG").css("display","block");
		pageInstall = new Object();
		pageInstall.action = "install";
		pageInstall.id = $("span.activeRadio").data("id");
		pageInstall.pname = $("span.activeRadio").data("name");
		sendAjaxRequest(pathToController,pageInstall,'html','installApp');
	}
});

function installApp(msg) {	
	circleLoader.hide();
	window.location = "appInstalledPage.php?id="+pageInstall.id+"&name="+pageInstall.pname ;
}

$(".proceedToWizard").on("click",function() {
	var pageId = $(this).data("id");
	// window.open( "https://www.facebook.com/"+pageId+"?id="+pageId+"&sk=app_"+INSTALLED_APP_ID,"_blank");
	window.top.location.href = "https://www.facebook.com/"+pageId+"?id="+pageId+"&sk=app_"+INSTALLED_APP_ID,"_blank";
});

//setting the auto update preferences for the user
$("body").on("click", "input[type=checkbox].autoUpdate", function(){
	var autoUpdate = new Object();
	if( $(this).is(':checked')) {
		autoUpdate.action = "addUpdate";
	} else {
		autoUpdate.action = "removeUpdate";
	}

	autoUpdate.id = $(this).data("id");
	autoUpdate.name = $(this).data("name");
	autoUpdate.param = "changePreference";
	//sending the ajax update to change the user preference of auto update
	sendAjaxRequest(pathToController, autoUpdate, 'html', 'preferenceChanged');
});

function preferenceChanged(msg) {

}

// $("body").on("click","span.unUpdate", function(){
// 	//checking if the sibling of the parent of the span is an already imported item list
// 	//this script runs on page load when span may have unUpdate class but the item may have been already imported
// 	if( $(this).parent().siblings("div.alreadyImported").length ) {
// 		$(this).removeClass("unUpdate");
// 		$(this).addClass("autoUpdate");

// 		var autoUpdate = new Object();
// 		autoUpdate.action = "addUpdate";
// 		autoUpdate.id = $(this).data("id");
// 		autoUpdate.name = $(this).data("name");
// 		autoUpdate.param = "changePreference";
// 		sendAjaxRequest(pathToController,autoUpdate,'html','preferenceChanged');
// 	}
// });

var iconImage = $("img.iconImage");
var dummyImage = $("img.dummyImage");
var homedummyImage = $("img.homedummy_image");

$(".ingredientFinish").on("click",function(e){
	e.preventDefault();

	circleLoader.show();
	var message = '';
	$("label.error.danger").remove();
	$(".required").each(function(){
		if( $(this).val().trim() == "" ) {	
			console.log($(this).val())
			message = "Please enter all the fields";
			$(this).after("<label class='error danger' for='"+$(this).attr('id')+"''>This field is required</label>");
		}
	});

	if( $("#appName").val().match(/^[\w -]{0,12}$/) == null ) {
		message = "No Special Characters Allowed. Max Length 12";
		$("#appName").after("<label class='error danger' for='"+$(this).attr('id')+"''>"+message+"</label>");
	} 

	if( $("#appTitle").val().match(/^[\w -]{0,100}$/) == null ) {
		message = "No Special Characters Allowed. Max Length 100";
		$("#appTitle").after("<label class='error danger' for='"+$(this).attr('id')+"''>"+message+"</label>");
	}
	if( $("#keyWords").val().match(/^[\w -,]{0,100}$/) == null ) {
		message = "No Special Characters Allowed. Max Length 100";
		$("#keyWords").after("<label class='error danger' for='"+$(this).attr('id')+"''>"+message+"</label>");
	}
	if( $("#appDescription").val().length > 1000 ) {
		message = "Max Length 1000";
		$("#appDescription").after("<label class='error danger' for='"+$(this).attr('id')+"''>"+message+"</label>");
	}
	
	if( $(".url_validate").val() && $(".url_validate").val().match(/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/) == null ) {
		message = "Please enter a valid URL";
		$(".url_validate").after("<label class='error danger' for='"+$(this).attr('id')+"''>"+message+"</label>");
	}

	if(message) {	
		circleLoader.css({"display":"none"});
		//alert(message)
	} else if( ! (dummyImage.attr("data-value") == "1" && iconImage.attr("data-value") == "1") ) {	
		// circleLoader.css({"display":"none"});
		// alert("Please Upload missing images");
		var appIngredients = new Object();
		appIngredients.param = "submitIngredients";
		appIngredients.name = $("#appName").val();
		appIngredients.title = $("#appTitle").val();
		appIngredients.description = $("#appDescription").val();
		appIngredients.url = $("#appUrl").val();
		appIngredients.keywords = $("#keyWords").val();

		if(dummyImage.attr("data-value") != "1") {
			appIngredients.appSplashImage = 'http://static.snaplion.com/snaplionfbw/loadingScreen.png';
		}
		
		if(iconImage.attr("data-value") != "1") {
			appIngredients.app_icon = 'http://static.snaplion.com/snaplionfbw/1024x1024.png';
		}
		sendAjaxRequest(pathToController, appIngredients, 'html', 'ingredientsDataSent');
		e.stopPropagation();
	} else if( (dummyImage.attr("data-value") == "1" && iconImage.attr("data-value") == "1") ) {
		var appIngredients = new Object();
		appIngredients.param = "submitIngredients";
		appIngredients.name = $("#appName").val();
		appIngredients.title = $("#appTitle").val();
		appIngredients.description = $("#appDescription").val();
		appIngredients.url = $("#appUrl").val();
		appIngredients.keywords = $("#keyWords").val();
		console.log(appIngredients);
		e.preventDefault();
		sendAjaxRequest(pathToController, appIngredients, 'html', 'ingredientsDataSent');
		e.stopPropagation();
	}
	circleLoader.hide();
});

function ingredientsDataSent(msg) {	
	circleLoader.css({"display":"none"});
	console.log(msg);
	if(msg == 1) {
		alert("Data has been submitted successfully");
		if( $(".paymentLink").length ) {
			window.location = "payment.php";
		} else {
			window.location = "imported.php";
		}
	} else {
		alert("There was an error while submitting your data");
	}
	circleLoader.hide();
}

$(".add-gloss").on("click", function() {
	var appGloss = new Object();
	appGloss.param = "appGloss";

	if( $(".add-gloss").is(":checked") ) {
		appGloss.value = 1; 
	} else {
		appGloss.value = 0;
	}

	sendAjaxRequest(pathToController,appGloss,'html','checkGloss');
});

function checkGloss(msg) {
	if(msg == 1) {
		console.log("App gloss updated");
	}
}

function getURLfromInkBlob (blob) {
	return 'http://static.snaplion.com/'+blob.key;
}

function getS3StoragePath () {
	return {
		location: 'S3',
		path: '/' +window.app_id +'/'+ window.model_class +'/',
		name: (""+Math.random()).substring(2,7),
		access: 'public'
	}	
}

function crop(blob, obj) {
	console.log(blob);
	var min_height = obj.min_height,
	min_width = obj.min_width,
	true_width = obj.true_width,
	true_height = obj.true_height;
	// target_element = obj.target_element,
	input_field = obj.input_field;
	target_element = obj.target_element;

	var btn = $("#crop_modal").find(".btn.btn-primary"),
	img_url = $(this).attr("src"),
	jcrop_api;

	console.log("Calling crop...");
	$("#crop_modal").find(".btn.btn-primary").attr("disabled", "true").text("Loading image...");


	$("#crop_modal").modal("show");
	$("#crop_modal .modal-body").empty().append("<img src='" + blob.url +"' alt=''>")

	$("#crop_modal .modal-body img").one("load", function() {
		console.log("Loaded");
		$(this).Jcrop({
			minSize: [ min_width, min_height ],
			trueSize: [true_width, true_height],
			boxWidth: 450, 
			boxHeight: 400,
			aspectRatio: min_width/min_height
		}, function() {
			jcrop_api = this;
			jcrop_api.setSelect([200, 335, 800, 1335]);	
		});

		$("#crop_modal").find(".btn.btn-primary").removeAttr("disabled").text("Save Changes");

		btn.one("click", function(e) {
			circleLoader.css({"display":"block","z-index":"100"});
			switch(target_element)
			{
				case 'splashScreen':
					circleLoader.css("top","690px");
				break;
				case 'iconImage':
					circleLoader.css("top","342px");
				break;
				case 'homeScreen':
					circleLoader.css("top","1290px");
				break;
			}
			e.preventDefault();
			var coords = jcrop_api.tellSelect();
			var x = Math.ceil(coords.x),
			y = Math.ceil(coords.y),
			w = Math.ceil(coords.w),
			h = Math.ceil(coords.h);


			if(target_element && target_element.length) {
				var loader = $("<div>", {
					"class": "loader",
					"style": "position: relative;top:" + "10%;"
				});
				// target_element.html(loader);
			}

			filepicker.convert(blob, {crop: [x, y, w, h]},
				function(cropped_blob){
					// console.log("CROPPED", cropped_blob);

					filepicker.convert(cropped_blob, {width: min_width, height: min_height, fit: "clip"}, 
						getS3StoragePath(),
						function(new_InkBlob) {
							// console.log("SCALED", new_InkBlob, getS3StoragePath());
							console.log("SCALED", new_InkBlob);

							if(target_element == 'splashScreen') {	
								splashUpload = new Object();
								splashUpload.url = new_InkBlob.url;
								splashUpload.action = "uploadImage";
								splashUpload.param = "splashUpload";
								
								sendAjaxRequest(pathToController,splashUpload,'html','sizeCheck');
								// dummyImage.onload = hideLoader();
								//attch here a function to handle the checking if the image has been sent to snaplion server without any error
								dummyImage.attr("src",new_InkBlob.url);
								// dummyImage.css({"width":"216px","height":"383px","margin-top":"0px","margin-left":"0px","top":"0px","left":"0px"});
								dummyImage.attr("data-value","1");
							} else if(target_element == "iconImage" ) {
								iconUpload = new Object();
								iconUpload.url = new_InkBlob.url;
								iconUpload.action = "uploadImage";
								iconUpload.param = "iconUpload";
								// circleLoader.css({"display":"block","top":"342px","z-index":"100"});
								sendAjaxRequest(pathToController,iconUpload,'html','sizeCheck');
								// iconImage.onload = hideLoader();
								//attch here a function to handle the checking if the image has been sent to snaplion server without any error
								iconImage.attr("src",new_InkBlob.url);
								// iconImage.css({"width":"102px","height":"102px"});
								console.log(iconImage.data("value"));
								iconImage.attr("data-value","1");
							} else if(target_element == 'homeScreen') {	
								homeUpload = new Object();
								homeUpload.url = new_InkBlob.url;
								homeUpload.action = "uploadImage";
								homeUpload.param = "homeUpload";
								// circleLoader.css({"display":"block","top":"1290px","z-index":"100"});
								sendAjaxRequest(pathToController,homeUpload,'html','sizeCheck');
								// homedummyImage.onload = hideLoader();
								//attch here a function to handle the checking if the image has been sent to snaplion server without any error
								homedummyImage.attr("src",new_InkBlob.url);
								homedummyImage.css({"width":"324px","height":"450px","margin-top":"0px","margin-left":"0px","top":"0px","left":"0px"});
								homedummyImage.attr("data-value","1");

							}
							circleLoader.css("display","none");
						}
					);
				});

			$("#crop_modal").modal("hide");
		});
	});
}

function sizeCheck(msg) {		
	if(msg.trim() != 1) {
		alert("There was an error while uploading the image.");
	}
}

function hideLoader() {
 	
}

$("form.payment").on("submit",function(e) {
	var userName = $("input[name='card_holder_name']");
	var address1 = $("input[name='street_address']");
	var city = $("input[name='city']");
	var state = $("input[name='state']");
	var country = $("input[name='country']");
	var email = $("input[name='email']");

	var message = '';
	if( userName.val().match(/^[\w -]{3,30}$/) == null ) {	
		message = "Please Enter a Valid Name";
		// alert("Please Enter a Valid Name");
		userName.addClass("errorMessage");
	}

	if(address1.val().trim() == '') {	
		message = "Please Enter a valid Billing Address";
		// alert("Please Enter a valid Billing Address");
		address1.addClass("errorMessage");	
		// address1.after("<span class='error danger'>"+message+"</span>");
	}

	if( city.val().match(/^[\w -]{2,30}$/) == null ) {	
		message = "Please Enter a Valid City Name";
		// alert("Please Enter a Valid City Name");
		city.addClass("errorMessage");	
		// city.after("<span class='error danger'>"+message+"</span>");
	}

	if( state.val().match(/^[\w -]{2,30}$/) == null ) {	
		message = "Please Enter a Valid State Name";
		// alert("Please Enter a Valid State Name");
		state.addClass("errorMessage");	
		// state.after("<span class='error danger'>"+message+"</span>");
	}

	if( country.val().match(/^[\w -]{2,30}$/) == null ) {
		message = "Please Enter a Valid Country Name";
		// alert("Please Enter a Valid Country Name");
		country.addClass("errorMessage");	
		// country.after("<span class='error danger'>"+message+"</span>");
	}

	if( email.val().match(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/) == null ) {	
		message = "Please Enter a Valid Email Address";
		// alert("Please Enter a Valid Email Address");
		email.addClass("errorMessage");	
		// email.after("<span class='error danger'>"+message+"</span>");
	}

	if(message) {
		alert("Please Enter all the Fields in Proper Format");
		e.preventDefault();
		e.stopPropagation();
	}
});

$("form.payment .required").on("keyup",function(){
	if($(this).val() != '') {
		$(this).removeClass("errorMessage");
	}
});

var hiddenInfoDiv = $("#helpInformation");
var hiddenInfoText = $("#helpInformation .tooltipInner");

$("img.helpText").mouseenter(function() {
	checkMouseEnter(".has-tip","right",$(this));
}).mouseleave(function(){
	hiddenInfoDiv.css("display","none");
	hiddenInfoText.html("");
});

$("img.imageHelpText").mouseenter(function(){
	checkMouseEnter(".has-tooltip","left",$(this));
}).mouseleave(function(){
	hiddenInfoDiv.css("display","none");
	hiddenInfoText.html("");
});

//in this function we have to give hints to the user for the app ingredients
function checkMouseEnter(selectedClass,side,movedOver) {
	var text = movedOver.siblings(selectedClass).attr("title");
	hiddenInfoText.html(text);
	movedOver.parent().append(hiddenInfoDiv);
	if(side == "left") {
		hiddenInfoDiv.css({"left":"200px","right":"auto"});
	} else {
		hiddenInfoDiv.css({"right":"2px","left":"auto"});
	}

	hiddenInfoDiv.css("display","block");
}

var installedPage = $("#hiddenInstalled");
var uninstalledPage = $("#hiddenUninstalled");
var uninstalledNewPage = $("#hiddenUninstalledNew");
var uninstalledAppPages = $("#uninstalledAppPages");

var uninstalledNewAppPages = $("#uninstalledNewAppPages");
var installedAppPages = $("#installedAppPages");
var uninstalledAppPage = $("#uninstalledAppPage");

function getPageList(msg) {
	circleLoader.show();
	if(msg) {
		var parseOutput = JSON.parse(msg);
		var installed = parseOutput.installed;
		var uninstalled = parseOutput.uninstalled;
		console.log(parseOutput);

		if(installed.length == 0) {
			if(uninstalled.length) {	
				var message = '';
				
				if( !installed.length ) {
					uninstalledAppPages.css("margin-top","15%");
					message  = "Start Building Your First Snaplion App";
				} else {
					message = "Build Apps for More Pages";
				}
				uninstalledAppPages.find("h4.newAppMessage").text(message);

				var uninstalledNewPageHtml = uninstalledNewPage.clone();
				for(var k = 0; k < uninstalled.length; k++) {
					uninstalledNewPageHtml.find("img.uninstalledAppImage").attr("src","https://graph.facebook.com/"+uninstalled[k].id+"/picture?height=64&width=64");
					uninstalledNewPageHtml.find(".pageName").text(uninstalled[k].name);
					uninstalledNewPageHtml.find(".pageCategory").text(uninstalled[k].category);
					uninstalledNewPageHtml.find(".pageLikes").text(uninstalled[k].likes + ' likes');
					uninstalledNewPageHtml.find(".newAppRadio").attr("data-id", uninstalled[k].id).attr("data-name", uninstalled[k].name);
					uninstalledNewPageHtml.find(".newAppRadio").attr("id", 'radio' + k);
					uninstalledNewPageHtml.find(".newAppRadio").next('label').attr("for", 'radio' + k);

					uninstalledNewAppPages.append(uninstalledNewPageHtml.html());
				}
			}

			$('.uninstalledApps').show();
		} else {
			if(uninstalled.length) {
				var uninstalledNewPageHtml = uninstalledPage.clone();

				for(var k = 0; k < uninstalled.length; k++) {
					// uninstalledPage.find("div.unistalledPageName span.toggleradio").attr("data-id",uninstalled[k].id).attr("data-name",uninstalled[k].name);
					uninstalledNewPageHtml.find("img.uninstalledAppImage").attr("src","https://graph.facebook.com/"+uninstalled[k].id+"/picture?height=64&width=64");
					uninstalledNewPageHtml.find(".pageName").text(uninstalled[k].name);
					uninstalledNewPageHtml.find(".pageCategory").text(uninstalled[k].category);
					uninstalledNewPageHtml.find(".pageLikes").text(uninstalled[k].likes + ' likes');
					uninstalledNewPageHtml.find(".newAppRadio").attr("data-id", uninstalled[k].id).attr("data-name", uninstalled[k].name);
					uninstalledNewPageHtml.find(".newAppRadio").attr("id", 'radio' + k);
					uninstalledNewPageHtml.find(".newAppRadio").next('label').attr("for", 'radio' + k);
					var innerHtml = uninstalledNewPageHtml.html();
					uninstalledAppPages.append(innerHtml);
				}

				installedAppPages.prepend('<h4 class="install-heading">Install Facebook Wizard on your ' + uninstalled.length + ' other pages?</h4>');
			}

			var installedPageHtml = installedPage.clone();
			for(var i = 0; i < installed.length; i++) {
				installedPageHtml.find("div.installedAppPage, a.appLinkDiv").attr("data-id",installed[i].id);
				installedPageHtml.find("img.installedAppImage").attr("src","https://graph.facebook.com/"+installed[i].id+"/picture?height=64&width=64");
				installedPageHtml.find(".pageName").text(installed[i].name);
				installedPageHtml.find(".pageCategory").text(installed[i].category);
				installedPageHtml.find(".pageLikes").text(installed[i].likes + ' likes');
				var innerHtml = installedPageHtml.html();
				installedAppPages.prepend(innerHtml);
			}

			$('.installedApps').show();
		}

		circleLoader.hide();
	} else {
		window.location = "pagelist.php";
	}
}