<html lang="en">
	<head>
	    <meta charset="utf-8">

	    <!-- Bootstrap 2.3.2 -->
	    <link rel="stylesheet" href="css/bootstrap.min.css">

	    <script type="text/javascript" src="//api.filepicker.io/v1/filepicker.js"></script>

	    <!-- Your CSS -->
	    <link rel="stylesheet" href="css/style.css">
	</head>
	<body>

	<button type="button" id="upload">Upload</button>
	<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js'></script>
	<script>
	$(document).ready(function(){

		filepicker.setKey('AQ5G20QvTseVecjZm1aswz');
		$("#upload").click(function(){
			filepicker.pick(function(InkBlob){
			  console.log(InkBlob.url);
			});
		});
	});
	</script>
	</body>
</html>