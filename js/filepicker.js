var pathToController = "AjaxMethods.php";

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

var mime_type;
var iconImage = $("img.iconImage");
var dummyImage = $("img.dummy_image");

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
				target_element.html(loader);
			}

			filepicker.convert(blob, {crop: [x, y, w, h]},
				function(cropped_blob){
					console.log("CROPPED", cropped_blob);

					filepicker.convert(cropped_blob, {width: min_width, height: min_height, fit: "clip"}, 
						getS3StoragePath(),
						function(new_InkBlob) {
							// console.log("SCALED", new_InkBlob, getS3StoragePath());
							console.log("SCALED", new_InkBlob);

							if(target_element == 'splashScreen')
							{	
								splashUpload = new Object();
								splashUpload.url = new_InkBlob.url;
								splashUpload.action = "uploadImage";
								splashUpload.param = "splashUpload";
								sendAjaxRequest(pathToController,splashUpload,'html','sizeCheck');

								dummyImage.attr("src",new_InkBlob.url);
								dummyImage.css({"width":"298px","height":"450px","margin-top":"0px","margin-left":"0px","top":"0px","left":"13px"});
								dummyImage.data("value","1");
							} else
							{

							}
							// if(target_element && target_element.length) {
							// 	$(target_element).empty().append("<img src='"+getURLfromInkBlob(new_InkBlob)+"' class='main_image' />");
							// }
							// if(input_field && input_field.length) {
							// 	$(input_field).val(getURLfromInkBlob(new_InkBlob));
							// }
						}
						);
				});

			$("#crop_modal").modal("hide");
		});
});
}


function attach_file_picker(elem) {
	console.log("attaching to ", elem);
	$(elem).on("click", function() {
		var input_field = $("#" + $(this).data("input-field-id")),
		cropping_flag = Boolean($(this).data("cropping")),
		min_height = $(this).data("cropping-min-height") && parseInt($(this).data("cropping-min-height")),
		min_width = $(this).data("cropping-min-width") && parseInt($(this).data("cropping-min-width")),
		preview = $("#" + $(this).data("preview-id"));


		if(cropping_flag) {
			filepicker.pick(
			{
				mimetype: ['image/png', 'image/jpg', 'image/jpeg',  'image/gif', 'image/bmp'], 
				maxSize: 3*1024*1024
			},  
			function(InkBlob) {
                	// Check image dimensions
                	$("#loading").show();
                	filepicker.stat(InkBlob, {width: true, height: true},function(metadata){
                		console.log(JSON.stringify(metadata));
                		

                		if(metadata.width < min_width || metadata.height < min_height) {
							// Small image
							alert("This image is too small.  Mobile phones have high resolution retina displays, and you want your app to look awesome.  Please upload an image with minimum height "+min_height+" pixels, and minimum width of "+min_width+" pixels.");
							$("#loading").hide();
							filepicker.remove(InkBlob, function(){
								console.log("Removed");
							});
						} else {
							$("#loading").show();
							console.log("cropping");
							if (metadata.width == min_width && metadata.height == min_height) {
								//no need to crop, just save
								console.log("No cropping needed, size is as required.  Just storing.")
								filepicker.store(InkBlob, 
									getS3StoragePath(), 

									function(new_InkBlob) {
										console.log("Stored", new_InkBlob, getS3StoragePath());

										if(preview && preview.length) {
											$(preview).empty().append("<img src='"+getURLfromInkBlob(new_InkBlob)+"' class='main_image' />");
											$("#loading").hide();
										}
										if(input_field && input_field.length) {
											$(input_field).val(getURLfromInkBlob(new_InkBlob));
										}
									}, 
									function() {
										console.log("Error storing blob");;
									});
							} else {
								$("#loading").hide();
								crop(InkBlob, { "min_height": min_height, "min_width": min_width, "true_height": metadata.height, "true_width": metadata.width, target_element: preview, input_field: input_field });
							}
						}
					});
}
);
} else {
			//No Cropping required
			$("#loading").hide();
			console.log(getS3StoragePath());
			filepicker.pickAndStore(
			{
				mimetype: ['image/png', 'image/jpg', 'image/jpeg',  'image/gif', 'image/bmp'], 
				maxSize: 3*1024*1024
			}, 
			getS3StoragePath(),
			function(InkBlobs) {
				console.log(InkBlobs);
				if($(preview).length) {
					$(preview).html("<img src='" + getURLfromInkBlob(InkBlobs[0]) + "' />" )
				}
				input_field.val(getURLfromInkBlob(InkBlobs[0]));
			}
			);
		}	
	});
}
