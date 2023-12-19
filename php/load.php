<?php
	$langId = "en";

  	$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$url_components = parse_url($actual_link);
	parse_str($url_components['query'], $params);

	if(isset($params['lang'])) {
		$langId = $params['lang'];
	}

	$flang = fopen("../languages/" . $langId . ".lang", "r");
	$flang = fread($flang,filesize("../languages/" . $langId . ".lang"));

	$pageTxt = explode(",*-", $flang);
	parse_str(preg_replace("/\r|\n/", "", $pageTxt[4]), $nav);
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<meta http-equiv="X-UA-Compatible" content="ie=edge">  	
		<link rel="icon" href="../img/favicon.png">
		<title><?php echo $nav['loadPageTitle']; ?></title>

		<style type="text/css">
			html {
			  height: 100%;
			}

			body {
				min-height: 70%;
				background-color: black;
				display: flex;
				justify-content: center;
				align-items: center;
			}

			.loader,
			.loader:before,
			.loader:after {
			  background: #ffffff;
			  -webkit-animation: load1 1s infinite ease-in-out;
			  animation: load1 1s infinite ease-in-out;
			  width: 1em;
			  height: 4em;
			}
			.loader {
			  color: #ffffff;
			  text-indent: -9999em;
			  margin: 150px auto;
			  position: relative;
			  font-size: 30px;
			  -webkit-transform: translateZ(0);
			  -ms-transform: translateZ(0);
			  transform: translateZ(0);
			  -webkit-animation-delay: -0.16s;
			  animation-delay: -0.16s;
			}
			.loader:before,
			.loader:after {
			  position: absolute;
			  top: 0;
			  content: '';
			}
			.loader:before {
			  left: -1.5em;
			  -webkit-animation-delay: -0.32s;
			  animation-delay: -0.32s;
			}
			.loader:after {
			  left: 1.5em;
			}
			@-webkit-keyframes load1 {
			  0%,
			  80%,
			  100% {
			    box-shadow: 0 0;
			    height: 4em;
			  }
			  40% {
			    box-shadow: 0 -2em;
			    height: 5em;
			  }
			}
			@keyframes load1 {
			  0%,
			  80%,
			  100% {
			    box-shadow: 0 0;
			    height: 4em;
			  }
			  40% {
			    box-shadow: 0 -2em;
			    height: 5em;
			  }
			}
		</style>

	</head>

	<body>

		<div id="fake_load_command" style="height: 300px;">
			<div style="height: 100px;">
				<div class="loader"><?php echo $nav['loaderTxt']; ?></div>
			</div>
			<h2 style="text-align: center; color: #fff; margin-top: 120px; font-size: 50px;"><?php echo $nav['sendTxt']; ?></h2>
		</div>

		<script type="text/javascript">
			setTimeout(
            function () {
              window.location.replace("http://bartque.alwaysdata.net/php/cart");
            }, 2000
          );
		</script>

	</body>

</html>