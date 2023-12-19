<?php
	session_start();

	include('./connexionDB.php');

	if (!isset($_COOKIE['id']) and !isset($_SESSION['id'])){
		header('Location: http://bartque.alwaysdata.net');
		exit;
	}

	if(isset($_COOKIE['id'])) {
		$_idClient = $_COOKIE['id'];
	} else {
		$_idClient = $_SESSION['id'];
	}

	$req_client = $DB->query("SELECT * FROM clients WHERE id_client = :id",
		array('id' => $_idClient));
	$req_client = $req_client->fetch();

	$age = date('Y') - $req_client['birthdate'];
	if (date('md') < date('md', strtotime($req_client['birthdate']))) {
		$age -= 1;
	}

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
	parse_str(preg_replace("/\r|\n/", "", $pageTxt[0] . $pageTxt[2]), $nav);

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<meta http-equiv="X-UA-Compatible" content="ie=edge">  

		<link rel="stylesheet" href="../css/styles.css"/>
		<link rel="stylesheet" href="../css/styles-profil.css"/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">

		<link rel="icon" href="../img/favicon.png">
		<title><?php echo $nav['accountPageTitle']; ?></title>

	</head>

	<body>
		<?php include 'navMenu.php';?>

		<main style="min-height: 79.4%;">
			<div id="profil-panel">
				<div id="profil-panel-back" method="post">
					<h2><?php echo $nav['accountTitle']; ?></h2>

					<ul id="info_list">
						<li>
						   	<div id="profil_img_holder">
						   		<i id="defaultIcon" class="fas fa-user fa-4x" style="text-align:center; margin-top:12px;"></i>
						   	</div>

	                      <?php
	                      		if(isset($req_client['profil_img'])) {
	                      			echo '<style>
			                      			#profil_img_holder {
		                      					background-image: url("'.$req_client['profil_img'].'")
		                      				}
		                      				#defaultIcon {
		                      					display:none;
		                      				}
		                      			</style>';
	                      		}

								$img=$_FILES['img'];
								if(isset($_POST['submit'])){
									if($img['name']==''){
								  		//echo "<h2>An Image Please.</h2>";
									}else{
										$filename = $img['tmp_name'];
										$client_id="45baa77b0e9a55b";
										$handle = fopen($filename, "r");
										$data = fread($handle, filesize($filename));
										$pvars   = array('image' => base64_encode($data));
										$timeout = 30;
										$curl = curl_init();
										curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
										curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
										curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id));
										curl_setopt($curl, CURLOPT_POST, 1);
										curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
										curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
										$out = curl_exec($curl);
										curl_close ($curl);
										$pms = json_decode($out,true);
										$url=$pms['data']['link'];
								    	if($url!=""){
								  			$DB->insert("UPDATE clients SET profil_img = :url WHERE id_client = :id",
          										array('url' => $url, 'id' => $_idClient));

									   		header('Location: http://bartque.alwaysdata.net/php/account');
											exit;
								    	}else{
								    		//echo "<h5>There's a Problem</h5>";
								    		//echo $pms['data']['error'];
								  		}
								 	}
								}
							?>
                        	
                        	<form enctype="multipart/form-data" method="POST" style="flex:auto; margin-left: 15px;">
								<h4><?php echo $nav['imgTxtLabel']; ?></h4>
								<input name="img" accept="image/*" size="35" type="file" onchange="readURL(this)" /><br/>
								<input type="submit" name="submit" value="<?php echo $nav['imgButton']; ?>" style="width:85%; margin:0;"/>
								<script>
									function readURL(input) {
							            if (input.files && input.files[0]) {
							                var reader = new FileReader();

							                reader.onload = function (e) {
							                    var holder = document.getElementById("profil_img_holder");
							                    var defaultImg = document.getElementById("defaultIcon");
							                    holder.style.backgroundImage = "url("+ e.target.result +")";
							                    defaultImg.style.display = "none";
							                };

							                reader.readAsDataURL(input.files[0]);
							            }
							        }
								</script>
							</form>
	                  </li>
						<li><h4 class="info_name"><?php echo $nav['infoL0']; ?></h4> <h4 class="info_data"><?php if(isset($req_client['surname'])){ echo $req_client['surname']; }?> <?php if(isset($req_client['name'])){ echo $req_client['name']; }?></h4></li>
						<li><h4 class="info_name"><?php echo $nav['infoL1']; ?></h4> <h4 class="info_data"><?php if(isset($age)){ echo $age; }?> years old</h4></li>
						<li><h4 class="info_name"><?php echo $nav['infoL2']; ?></h4> <h4 class="info_data"><?php if(isset($req_client['mail'])){ echo $req_client['mail']; }?></h4></li>
						<li><h4 class="info_name"><?php echo $nav['infoL3']; ?></h4> <h4 class="info_data"><?php if(isset($req_client['street'])){ echo $req_client['street']; }?>, N°<?php if(isset($req_client['street_number'])){ echo $req_client['street_number']; }?> <br><?php if(isset($req_client['cp'])){ echo $req_client['cp']; }?> <?php if(isset($req_client['city'])){ echo $req_client['city']; }?></h4></li>
						<li><h4 class="info_name"><?php echo $nav['infoL4']; ?></h4> <h4 class="info_data"><?php if(isset($req_client['inscription_date'])){ echo $req_client['inscription_date']; }?></h4></li>
					</ul>

				</div>
			</div>

		</main>

		<footer class="section section-dark" style="margin-top: 40px;bottom: 0; padding: 20px 40px;">
	      	<p>&copy Barthélemy Quentin 6B - Travail de fin d'études - Saint-Jean Berchmans 2020-2021</p>
	    </footer>

	</body>

</html>