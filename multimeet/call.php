<!--
https://jitsi.github.io/handbook/docs/dev-guide/dev-guide-iframe
https://github.com/nguyendatdh93/jitsi-meet/blob/master/doc/api.md
https://www.youtube.com/watch?v=6jCbngYr7oQ
-->

<!DOCTYPE html>
<html lang="en" style="height: 100%;">
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<meta http-equiv="X-UA-Compatible" content="ie=edge">  	
		<link rel="stylesheet" href="css/styles.css"/>
		<link rel="icon" href="img/logo.png">

		<title>MultiMeet Call</title>

		<script src="https://meet.jit.si/external_api.js"></script>

	</head>
	
<body style="height: 100%; margin: auto;">

	<div id="meet" style="height:100%;"></div>

	<script>

		const urlParams = new URLSearchParams(window.location.search);

		var domain = "meet.jit.si";
		var options = {
			roomName: "multimeet/" + urlParams.get('room'),
			parentNode: document.getElementById("meet"),
			configOverwrite: {},
			interfaceConfigOverwrite: {}
		}
		var api = new JitsiMeetExternalAPI(domain, options);

		api.executeCommand('displayName', '<?php if(isset($_COOKIE['name'])){ echo $_COOKIE['name']; } ?>');

		api.addEventListener("videoConferenceLeft", function(){ window.location.replace("./main"); }); //window.alert(api.getNumberOfParticipants());

	</script>

	<!--<iframe allow="camera; microphone; display-capture" src="https://meet.jit.si/multimeet/meetidallowfullscreen=" true"="" style="height: 400px; width: 100%; border: 0px;"></iframe>-->
	
</body>
	
</html>
