var pathToController= "AjaxController.php";
var accordion = $("#accordion");



function sendAjaxRequest(urlName,dataMsg,datatype,successFunction)
{
	$.post(
		urlName,
		dataMsg,
		function(msg)
		{
			console.log(msg);
			//alert("Msg is"+msg);
			eval(successFunction)(msg);
		},
		datatype
	);
}

$(function() {
	accordion.accordion();

	$('#userData').dataTable({
		"bJQueryUI": true
	});
		
});

$("#admin_login").click( function() {
	var name = $(".name").val().trim();
	var pass = $(".pass").val().trim();

	if(name=="" || pass=="")
	{	
		alert("Please enter both username and password.");
		return;
	}

	var data = new Object();
	data.param = "validateAdmin";
	data.name = name;
	data.pass = pass;
	sendAjaxRequest(pathToController,data,"html","isUserValid");

});

$("body").keypress(function(e) {
	if($("#admin_login").length>0 && e.which==13)
		$("#admin_login").click();
	else if($("#startTest").length>0 && e.which==13)
		$("#startTest").click();
					
});

function isUserValid(msg)
{
	if(msg=="1")
	{
		window.open("admin_panel.php","_self");
	}
	else
		alert("Invalid username or password");
}

$("#logout").click(function()
{
	var data = new Object();
	data.param ="logout";
	console.log("Loggin out");
	sendAjaxRequest(pathToController,data,"html","successLogout");
});

function successLogout(msg)
{
	//console.log("Here");
	window.open("index.php","_self");
}

/*------------------------------------------------------
------------------ Javascript for admin home page ------
--------------------------------------------------------*/


$("body").on('click',".removeItem",function() {
	
	//confirming with user the delete action
	var response = confirm("Are you sure you want to delete this subcategory item");
	if(response == true)
	{
		listTarget = $(this);
		var removeData = new Object();
		removeData.param = 'removeRow';

		//this is the category id of the category whose subcategory is to be deleted
		removeData.id = listTarget.data('id');
		removeData.sid = listTarget.data('sub');
		//cname gives the name of the subcategory to be deleted
		removeData.cname = listTarget.data('name');
		
		sendAjaxRequest(pathToController,removeData,'html','removeItem');
	}
	

});

function removeItem(msg)
{	

	var parentDiv = listTarget.parent();
	if( !isNaN(msg) )
	{	
		//removing the item from the dom
		parentDiv.remove();
	}
}

var overlay = $("div#overlay");
var subCategory = $("div#moveSubcategory");

$("body").on('click',".moveItem",function() {
	
	//getting the parent div of the move item clicked and its HTML
	moveParent = $(this).parent();
	
	
	//setting display of all category-name divs to block
	$(".catName").css("display","block");

	moveData = new Object();
	//getting id and name of the subcategory to change
	moveData.id = $(this).data('id');
	moveData.param = 'moveSubcategory';
	moveData.cname = $(this).data('name');

	//data-sub gives id of subcategory
	moveData.sid = $(this).data('sub');

	//hiding the only div that has been clicked upon
	$(".catName[data-id='"+moveData.id+"']").css("display","none");
	overlay.css("display","block");
	subCategory.css("display","block");

});


$("button.cancelChange").on('click',function(){
	overlay.css("display","none");
	subCategory.css("display","none");
});


$("button.changeCategory").on('click',function(){

	var radioValue = $("input[name='categoryname']:checked").val();
	if(!radioValue)
	{	
		//if no category is selected
		alert('Please select atleast one category');
	} else
	{	
		//it gives the id of the category that is to be changed to
		moveData.cid = radioValue;
		sendAjaxRequest(pathToController,moveData,'html','moveItem');

		//setting the display of the overlay and the subcategory change div to none
		overlay.css("display","none");
		subCategory.css("display","none");
	}

});


function moveItem(msg)
{	
	if( !isNaN(msg) )
	{	

		$("input[name='categoryname']").checked = false;
		// setting the data-id of the parent div of move button clicked and also the data-id of buttons inside
		moveParent.attr('data-id',moveData.cid);
		
		//setting the data id of children buttons to new id i.e. category changed to
		moveParent.find("button").attr('data-id',moveData.cid);
		moveParentHtml = moveParent[0].outerHTML;

		//removing the div from its original position
		moveParent.remove();
		//appending the div to the container with given data-id
		$("div.container[data-id='"+moveData.cid+"']").append(moveParentHtml);
	}
	
}


$("body").on('click',".createCategory",function(){
	categoryName = $("input[name='addCategory']").val();
	categoryId = $("input[name='addId']").val();
	console.log(categoryName + ' ' + categoryId);

	var message = '';

	if(categoryName.trim()  == '' )
	{
		message = "Please enter a valid name for the category";
	} else if( categoryId.trim().match(/^\+?(0|[1-9]\d*)$/) == null )
	{
		message = "Please enter a valid category id";
	}


	if(message)
	{	
		alert(message);
	} else
	{
		data = new Object();
		data.param = "addCategory";
		data.catname = categoryName;
		data.catId = categoryId;
		sendAjaxRequest(pathToController,data,'html','addCategory');
	}
});


function addCategory(msg)
{	
	if( !isNaN(msg) )
	{	
		//adding the radio button to the list of items where it can be moved
		var hiddenRadio = $("#hiddenRadio");
		hiddenRadio.find(".catName").attr("data-id",msg);
		hiddenRadio.find("input[name='categoryname']").attr("value",msg);
		hiddenRadio.find("span").html(data.catname);
		var radioHtml = hiddenRadio.html();
		$("#moveSubcategory").find(".row-fluid").last().before(radioHtml);

		var hiddenDiv = $("#hiddenCategory");
		hiddenDiv.find("h3").html(categoryName);

		// setting the data-id of the child-div to returned id
		hiddenDiv.find("div.subcategoryData").attr("data-id",msg);
		hiddenDiv.find(".subcategoryItems").attr("data-id",msg);
		hiddenDiv.find("input").attr("data-id",msg);
		hiddenDiv.find("button").attr("data-id",msg);

		//destroying the accordion
		//accordion is destroyed because elements cannot be directly appended to it so it is destroyed and re-enabled
		accordion.accordion("destroy");
		divInnerHtml = hiddenDiv.html();
		
		//appending to the accordion new category
		$("div.subcategoryData").last().after(divInnerHtml);
		
		//re-enabling the accordion
		accordion.accordion();
		// accordion.accordion("option","active",);
		$("#accordion").accordion( "option","active",$(".subcategoryData").length-2 );
		$("input[name='addCategory']").val("");
		
	} else if( msg.trim() == "errorname")
	{	
		//this is the error that is reported if there already exists a category with similar name
		alert("The category name already exists, please enter another category name ");
	} else if( msg.trim() == "errorid" )
	{	
		//this is the error that is reported if there already exists a category with similar id
		alert("The category id already exists, please enter another category id");
	}
}


$("body").on('click',".createSubcategory", function(){

	//getting the data-id of the button and this data-id has to match the data-id of input field
	dataId = $(this).attr("data-id");
	subcat = $("input[data-id='"+dataId+"'][name='addsubcategory']");

	if(subcat.length > 1)
	{	
		//this is the case that is encountered when subcategory is being created in a new category
		subcategoryName = subcat.eq(1).val();
	} else
	{	
		//when subcategory is being created in an old category
		subcategoryName = subcat.val();
	}
	
	if( !subcategoryName.trim() )
	{
		alert("please enter a valid subcategory name");
	} else
	{
		subcatData = new Object();
		subcatData.param = "addSubcategory";
		subcatData.id = dataId;
		subcatData.sname = subcategoryName;
		sendAjaxRequest(pathToController,subcatData,'html','addSubcategory');
	}
});


function addSubcategory(msg)
{	
	if( !isNaN(msg) )
	{	
		subcat.val("");
		//the msg gives the id of the row created in the database
		var id = msg;
		//selecting the parent div with data-id as with added subcategory
		var subcategoryDiv = $("div.subcategoryItems[data-id='"+dataId+"']");

		//getting the hidden div having the structure of the subcategory items
		var hiddenSubcategory = $("#hiddenSubcategory");

		//adding data-id and text to the children 
		hiddenSubcategory.find(".rowItems").attr("data-id",dataId).attr("data-sub",id);
		hiddenSubcategory.find("span").html(subcategoryName);

		//adding data-name and data-id to the buttons 
		hiddenSubcategory.find("button").attr("data-id",dataId).attr("data-name",subcategoryName).attr("data-sub",id);

		innerHtml = hiddenSubcategory.html();
		subcategoryDiv.append(innerHtml);
	}
	

}

/*------------------------------------------------------
--------------------- Javascript for userdata page -----
--------------------------------------------------------*/
var pageDataDiv = $("#pageData");

$("body").on("click",".installCount", function(){
	var installCount = $(this).text();
	installCount = parseInt(installCount);

	//checking if the install count is a number and that is it greater than zero
	if( !isNaN(installCount) && installCount > 0 )
	{
		if( pageDataDiv.css("display") == "block" )
		{	
			if( $(this).data("id") == pageData.id )
			{
				return;
			} else
			{
				pageDataDiv.html("");
			}
			
		}
		//setting display pageData div to block
		pageDataDiv.css("display","block");

		//pageData div is further appended to the row on which click was received
		

		pageData = new Object();
		pageData.id = $(this).data("id");
		pageData.param = "showPageData";
		$("td[data-id='"+pageData.id+"']").append(pageDataDiv);
		sendAjaxRequest(pathToController,pageData,'html','showPageData');
	}
	

});

function showPageData(msg)
{	
	//msg here contains the html that has to be appended to pageData div
	pageDataDiv.append(msg);

}

$("body").on("click",".closeImg",function(e){

	//setting the display of pagedtaa div to none when close image clicked upon
	pageDataDiv.css("display","none");

	//setting the html of pagedata div to empty
	pageDataDiv.html("");

	//stopping the further propogation of event which would have led to click having reached to td
	e.stopPropagation();
});