<?php
	session_start();

	include('./connexionDB.php');

	if (isset($_COOKIE['id']) or isset($_SESSION['id'])){
		header('Location: http://bartque.alwaysdata.net/index');
		exit;
	}

	$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$url_components = parse_url($actual_link);
	parse_str($url_components['query'], $params);

	$langId = "en";

	if(isset($params['lang'])) {
		$langId = $params['lang'];
	}

	$flang = fopen("../languages/" . $langId . ".lang", "r");
	$flang = fread($flang,filesize("../languages/" . $langId . ".lang"));

	$pageTxt = explode(",*-", $flang);
	parse_str(preg_replace("/\r|\n/", "", $pageTxt[0] . $pageTxt[6]), $nav);
	
	function createRandomPassword() { 
	
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '' ;
		
		while ($i <= 5) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		
		return $pass;
	}

	if(!empty($_POST)){
		//extract($_POST); //dangereux
		$valid = true;

		if (isset($_POST['register_button'])){
			$mail = strtolower($_POST['Email']);
			$password = $_POST['Password'];
			$passwordrepeat = $_POST['Passwordrepeat'];
			$name = $_POST['Name'];
			$surname = $_POST['Surname'];
 			$birthdate = $_POST['Birthdate'];
 			$city = $_POST['City'];
 			$street = $_POST['Street'];
 			$streetNumber = $_POST['Streetnumber'];
 			$cp = $_POST['Cp'];

			$req_mail = $DB->query("SELECT mail FROM clients WHERE mail = :mail",
				array('mail' => $mail));

			$req_mail = $req_mail->fetch();
 
			if ($req_mail['mail'] <> ""){
				$valid = false;
				$er_mail = "This mail already exist";
			}
			
			if($password != $passwordrepeat){
				$valid = false;
				$er_passwordrepeat = "The comfirmation password must be the same";
			}

			$img=$_FILES['img'];
			$url = " ";

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
			    	//
			    }else{
			    	//echo $pms['data']['error'];
			    }
			 }
	 
			if($valid){
				$password = md5($salt_prefix . $password . $salt_suffix);
				$inscription_date = date('Y-m-d');
				$confirmationcode = createRandomPassword();

				$DB->insert('INSERT INTO clients VALUES (null, :mail, :password, :name, :surname, :birthdate, :city, :street, :street_number, :cp, :confirmed, :confirmation_code, :profil_img, :isAdmin, :inscription_date)',
					array('mail' => $mail, 'password' => $password, 'name' => $name, 'surname' => $surname, 'birthdate' => $birthdate, 'city' => $city, 'street' => $street, 'street_number' => $streetNumber, 'cp' => $cp, 'confirmed' => false, 'confirmation_code' => md5($confirmationcode), 'profil_img' => $url, 'isAdmin' => false, 'inscription_date' => $inscription_date));

				
				require("../phpMailer/src/PHPMailer.php");
    			require("../phpMailer/src/SMTP.php");
    			require("../phpMailer/src/Exception.php");

    			$mailTemplate = fopen("confirmMailTemplate.php", "r");
    			$mailTemplate = fread($mailTemplate,filesize("confirmMailTemplate.php"));

    			$msg = str_replace("confirmation_code", $confirmationcode, $mailTemplate);
				
				$email = new PHPMailer\PHPMailer\PHPMailer();
				$email->SetLanguage('fr');
				$email->IsSMTP();
				$email->IsHTML(true);
				$email->SMTPDebug=0;
				$email->SMTPAuth=true;
				$email->SMTPSecure='ssl';
				$email->Host='';
				$email->Port='465';
				$email->Username='';
				$email->Password='';
				$email->From='';
				$email->FromName='SaxBuy';
				$email->AddAddress($mail);
				$email->CharSet="utf-8";
				$email->Subject='Confirmation SaxBuy';
				$email->Body=$msg;
				
				if(!$email->Send()){
				}
				else{     
  					
				}
				$email->SmtpClose();
				unset($email);
 
				header('Location: http://bartque.alwaysdata.net/php/login?code');
				exit;
			}
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<meta http-equiv="X-UA-Compatible" content="ie=edge">  	

		<link rel="stylesheet" href="../css/styles.css"/>
		<link rel="stylesheet" href="../css/styles-register.css"/>

		<link rel="stylesheet" href="../css/styles-eye.css"/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">

		<link rel="icon" href="../img/favicon.png">
		<title><?php echo $nav['logonPageTitle']; ?></title>

		<script type="text/javascript">
			window.onload = function () {
				const togglePassword = document.getElementById("togglePassword");
				const password = document.getElementById('password');
				const togglePassword2 = document.getElementById("togglePassword2");
				const password2 = document.getElementById('password2');

				togglePassword.onclick = function() {
	    			const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
	    			password.setAttribute('type', type);
	    			this.classList.toggle('fa-eye-slash');
				};

				togglePassword2.onclick = function() {
	    			const type = password2.getAttribute('type') === 'password' ? 'text' : 'password';
	    			password2.setAttribute('type', type);
	    			this.classList.toggle('fa-eye-slash');
				};
			}
		</script>

	</head>

	<body>
		<?php include 'navMenu.php';?>

		<div id="register-panel">
			<form id="register-panel-back" method="post">
				<h2><?php echo $nav['logonTitle']; ?></h2>

				<input class="inputBox" id="mail" type="email" name="Email" placeholder="<?php echo $nav['emailph']; ?>" value="<?php if(isset($mail)){ echo $mail; }?>" required maxlength="45">

				<?php if (isset($er_mail)): ?>
					<script type="text/javascript">
						const email = document.getElementById("mail");

						email.focus();
						email.setCustomValidity("<?= $er_mail ?>");

						email.addEventListener("input", function (event) {
							if(email.value == "<?= $mail ?>") {
								email.focus();
								email.setCustomValidity("<?= $er_mail ?>");
							} else {
								email.setCustomValidity("");
							}
						});
					</script>					
				<?php else: ?>

				<?php endif; ?>

				<div class="container" style="padding: 0; width: auto;">
					<input class="inputBox" id="password" type="password" name="Password" placeholder="<?php echo $nav['passph']; ?>" value="<?php if(isset($password)){ echo $password; }?>" required maxlength="45">	
					<i class="far fa-eye" id="togglePassword"></i>
				</div>

				<div class="container" style="padding: 0; width: auto;">
					<input class="inputBox" id="password2" type="password" placeholder="<?php echo $nav['passRepeatph']; ?>" name="Passwordrepeat" required maxlength="45">
					<i class="far fa-eye" id="togglePassword2"></i>
				</div>

				<script type="text/javascript">
					const pass = document.getElementById("password");
					const pass2 = document.getElementById("password2");

					<?php if (isset($er_passwordrepeat)): ?>
						pass2.focus();
					<?php else: ?>

					<?php endif; ?>

					pass2.setCustomValidity("<?= $er_passwordrepeat ?>");

					pass2.addEventListener("input", function (event) {
						if(pass2.value != pass.value) {
							pass2.focus();
							pass2.setCustomValidity("The comfirmation password must be the same");
						} else {
							pass2.setCustomValidity("");
						}
					});
				</script>
<!--
				<div style="margin-top: 20px;height: 100px;">
					<style type="text/css">
						#profil_img_holder {
							width: 80px;
							height: 80px;
							background-repeat: no-repeat;
							border-radius: 50px;
							border: 3px solid white;
							background-position: center;
							background-size: cover;
							flex:none;
							display: grid;
							background-color: #FFD732;
						}
					</style>

					<div id="profil_img_holder" style="float: left;">
				   		<i id="defaultIcon" class="fas fa-user fa-3x" style="text-align:center; margin-top:12px;"></i>
				   	</div>
	            	
	            	<div style="float: right;width: 70%; margin-top: 11px;">
						<h4 style="color: white;font-size: 20px;">Choose Image :</h4>
						<input name="img" accept="image/*" size="35" type="file" onchange="readURL(this)" style="border-bottom: none; width: 95%" />
						<br/>
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
					</div>
				</div>
-->
        		<input class="inputBox" type="text" placeholder="<?php echo $nav['name']; ?>" name="Name" value="<?php if(isset($name)){ echo $name; }?>" pattern="[^0-9]*" required maxlength="45">

        		<input class="inputBox" type="text" placeholder="<?php echo $nav['surname']; ?>" name="Surname" value="<?php if(isset($surname)){ echo $surname; }?>" pattern="[^0-9]*" required maxlength="45">

        		<input class="inputBox" type="date" placeholder="<?php echo $nav['birthdate']; ?>" name="Birthdate" value="<?php if(isset($birthdate)){ echo $birthdate; }?>" required>

        		<input class="inputBox" type="text" placeholder="<?php echo $nav['city']; ?>" name="City" value="<?php if(isset($city)){ echo $city; }?>" pattern="[^0-9]*" required maxlength="45">

        		<input class="inputBox" type="text" placeholder="<?php echo $nav['street']; ?>" name="Street" style="width: 69%; margin-right: 2%" value="<?php if(isset($street)){ echo $street; }?>" required maxlength="45">

        		<input class="inputBox" type="text" placeholder="<?php echo $nav['streetN']; ?>" name="Streetnumber" style="width: 27%;" value="<?php if(isset($streetNumber)){ echo $streetNumber; }?>" required maxlength="6">

       			<input class="inputBox" type="number" placeholder="<?php echo $nav['pc']; ?>" name="Cp" value="<?php if(isset($cp)){ echo $cp; }?>" required max="99999">

				<div style="text-align: center;">
					<input id="registerButton" type="submit" name="register_button" value="<?php echo $nav['logonBtn']; ?>">
					<a id="forgot" href="./login"><?php echo $nav['haveAccTxt']; ?></a>
				</div>
			</form>

		</div>

		<section class="section section-dark" style="margin-top: 40px;bottom: 0; padding: 20px 40px;">
	    	<input type="submit" name="top_button" value="<?php echo $nav['topBtn']; ?>" onclick='window.scrollTo({top: 0, behavior: "smooth"});' style="margin-top: 0px;">
	    	<br>
	    	<br>
	      	<p>&copy Barthélemy Quentin 6B - Travail de fin d'études - Saint-Jean Berchmans 2020-2021</p>
	    </section>

	</body>

</html>
