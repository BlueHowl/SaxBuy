<?php
	session_start();

	include('./connexionDB.php');

	if (isset($_COOKIE['id']) or isset($_SESSION['id'])){
		header('Location: http://bartque.alwaysdata.net/index');
		exit;
	}

	function createRandomPassword() { 
	
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '' ;
		
		while ($i <= 7) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		
		return $pass;
		
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
	parse_str(preg_replace("/\r|\n/", "", $pageTxt[0] . $pageTxt[5]), $nav);

	if(isset($params['forgot'])) {
		if(!empty($_POST)){
			//extract($_POST);
			$valid = true;

			if (isset($_POST['reset_button'])){
				$mail = strtolower($_POST['Email']);

				$req_mail = $DB->query("SELECT mail FROM clients WHERE mail = :mail",
					array('mail' => $mail));
	 
				$req_mail = $req_mail->fetch();
	 
				if ($req_mail['mail'] == ""){
					$valid = false;
					$er_mail = "This mail is not existing";
				}
				
				if($valid){
					require("../phpMailer/src/PHPMailer.php");
	    			require("../phpMailer/src/SMTP.php");
	    			require("../phpMailer/src/Exception.php");

	    			$connexionCode = createRandomPassword();
	    			
	    			$DB->insert("UPDATE clients SET confirmation_code = :confirmation_code WHERE mail = :mail",
						array('confirmation_code' => md5($connexionCode), 'mail' => $mail));

	    			$mailTemplate = fopen("resetPassMailTemplate.php", "r");
	    			$mailTemplate = fread($mailTemplate,filesize("resetPassMailTemplate.php"));

	    			$msg = str_replace("connexion_code", $connexionCode, $mailTemplate);
					
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
					$email->Subject='Reset Password SaxBuy';
					$email->Body=$msg;
					
					if(!$email->Send()){
					}
					else{
	  					
					}
					$email->SmtpClose();
					unset($email);
	 
					header('Location: http://bartque.alwaysdata.net/php/login?reset');
					exit;
				}
			}
		}
	} elseif (isset($params['reset'])) {
		$resetCode = $params['reset']; //connexion code

		if(!empty($_POST)){
			//extract($_POST);
			$valid = true;

			if (isset($_POST['login_button'])){
				$mail = strtolower($_POST['Email']);
				$password = $_POST['Password_reset'];
				$connexionCode = $_POST['ConnexionCode'];

				$req_mail = $DB->query("SELECT mail FROM clients WHERE mail = :mail",
					array('mail' => $mail));
	 			/////pas opti?
				$req_mail = $req_mail->fetch();
	 
				if ($req_mail['mail'] == ""){
					$valid = false;
					$er_mail = "This mail is not existing";
				}
				
				if($valid){
					$req = $DB->query("SELECT * FROM clients WHERE mail = :mail",
						array('mail' => $mail));
					$req = $req->fetch();

					if($req['confirmed'] == true) {
						if($req['confirmation_code'] == md5($connexionCode)) {
							$DB->insert("UPDATE clients SET confirmation_code = '', password = :pwd WHERE id_client = :id", //reset passwordResetConnexionCode
								array("pwd" => md5($salt_prefix . $password . $salt_suffix), "id" => $req['id_client']));

							finish_connexion($req, false);
						} else {
							$er_password = "Wrong password";
						}
					}
				}
			}
		}

	} else {

		$code = $params['code']; //confirmation code

		if(!empty($_POST)){
			//extract($_POST);
			$valid = true;

			if (isset($_POST['login_button'])){
				$mail = strtolower($_POST['Email']);
				$password = $_POST['Password'];
				$code = $_POST['Code'];
				$keepconnect = $_POST['KeepConnected'];

				$req_mail = $DB->query("SELECT mail FROM clients WHERE mail = :mail",
					array('mail' => $mail));
	 
				$req_mail = $req_mail->fetch();
	 			//prblm opti
				if ($req_mail['mail'] == ""){
					$valid = false;
					$er_mail = "This mail is not existing";
				}
				
				if($valid){
					$req = $DB->query("SELECT * FROM clients WHERE mail = :mail",
						array('mail' => $mail));
					$req = $req->fetch();

					if($req['confirmed'] == true) {
						if($req['password'] == md5($salt_prefix . $password . $salt_suffix)) {
							//$DB->insert("UPDATE clients SET confirmation_code = '' WHERE id_client = ?", //reset passwordResetConnexionCode
								//array($req['id_client']));						
							finish_connexion($req, isset($keepconnect));
						} else {
							$er_password = "Wrong password";
						}
					} else {
						$er_confirmed = "Account not confirmed yet";

						if($req['confirmation_code'] == md5($code)) {
							$DB->insert("UPDATE clients SET confirmed = 1, confirmation_code = '' WHERE id_client = :id",
								array('id' => $req['id_client']));
							$DB->insert("INSERT INTO cart (valid, fk_client) VALUES (:valid, :id)",
								array('valid' => false, 'id' => $req['id_client']));

							finish_connexion($req, isset($keepconnect));
						} else {
							$er_confirmed = "Wrong code";
							$bad_code = $code;
						}
					}

				}
			}
		}
	}

	function finish_connexion($request, $keep) { 
		if($keep) {
			setcookie('id', $request['id_client'], time() + 365*24*3600, '/');
			setcookie('mail', $request['mail'], time() + 365*24*3600, '/');
			setcookie('name', $request['name'], time() + 365*24*3600, '/');
			setcookie('surname', $request['surname'], time() + 365*24*3600, '/');
			setcookie('isAdmin', $request['isAdmin'], time() + 365*24*3600, '/');
		} else {
			$_SESSION['id'] = $request['id_client'];
			$_SESSION['mail'] = $request['mail'];
			$_SESSION['name'] = $request['name'];
			$_SESSION['surname'] = $request['surname'];
			$_SESSION['isAdmin'] = $request['isAdmin'];
		}		

		header('Location: http://bartque.alwaysdata.net');
		exit;
		
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<meta http-equiv="X-UA-Compatible" content="ie=edge">
    	
		<link rel="stylesheet" href="../css/styles.css"/>
		<link rel="stylesheet" href="../css/styles-login.css"/>
		
		<link rel="stylesheet" href="../css/styles-eye.css"/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">

		<link rel="icon" href="../img/favicon.png">
		<title><?php echo $nav['loginPageTitle']; ?></title>

		<script src="../js/password_visibility.js"></script>

	</head>

	<body>
		<?php include 'navMenu.php';?>

		<main style="min-height: 74.7%;">
			<div id="login-panel">
				<form id="login-panel-back" method="post">
					<h2><?php echo $nav['loginTitle']; ?></h2>

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

					<?php if (isset($params['forgot'])): ?>
						<div style="text-align: center;">
							<input id="resetButton" type="submit" name="reset_button" value="<?php echo $nav['resetBtn']; ?>">
							<a id="back" class="href-link" href="http://bartque.alwaysdata.net/php/login"><?php echo $nav['backLogin']; ?></a>
						</div>		
					<?php else: ?>
						<!-- forgot else -->

						<?php if (isset($resetCode)): ?>
						<?php else: ?>
							<!--password field -->
							<div class="container" style="padding: 0; width: auto;">
								<input id="password" class="inputBox" type="password" name="Password" placeholder="<?php echo $nav['passph']; ?>" value="<?php if(isset($password)){ echo $password; }?>" required maxlength="45">	
								<i class="far fa-eye" id="togglePassword"></i>
							</div>

							<div style="padding-top: 20px;">
								<input type="checkbox" id="keepConnected" name="KeepConnected">
								<label><?php echo $nav['keepConnectTxt']; ?></label>
							</div>

							<?php if (isset($er_password)): ?>
								<script type="text/javascript">
									const pass = document.getElementById("password");

									pass.focus();
									pass.setCustomValidity("<?= $er_password ?>");

									pass.addEventListener("input", function (event) {
										if(pass.value == "<?= $password ?>") {
											pass.focus();
											pass.setCustomValidity("<?= $er_password ?>");
										} else {
											pass.setCustomValidity("");
										}
									});
								</script>								

							<?php else: ?>

							<?php endif; ?>
							<!--password field end -->
						<?php endif; ?>

						<?php if (isset($er_confirmed) || isset($code)): ?>
							<input class="inputBox" id="code" type="text" name="Code" placeholder="<?php echo $nav['confirmph']; ?>" value="<?php if(isset($code)){ echo $code; }?>" required maxlength="45">
							<p style="text-align: center; padding-top: 10px; font-size: 14px;"><?php echo $nav['codeInfoTxt']; ?></p>

							<div style="padding-top: 20px;">
								<input type="checkbox" id="keepConnected" name="KeepConnected">
								<label><?php echo $nav['keepConnectTxt']; ?></label>
							</div>

							<script type="text/javascript">
								const validCode = document.getElementById("code");

								validCode.focus();
								validCode.setCustomValidity("<?= $er_confirmed ?>");

								validCode.addEventListener("input", function (event) {
									if(validCode.value == "<?= $bad_code ?>") {
										validCode.focus();
										validCode.setCustomValidity("<?= $er_confirmed ?>");
									} else {
										validCode.setCustomValidity("");
									}
								});
							</script>
						<?php else: ?>

						<?php endif; ?>

						<?php if (isset($resetCode)): ?>

							<script type="text/javascript">
								window.onload = function () {
									const togglePassword1 = document.getElementById("togglePassword1");
									const password1 = document.getElementById('password1');
									const togglePassword2 = document.getElementById("togglePassword2");
									const password2 = document.getElementById('password2');

									togglePassword1.onclick = function() {
						    			const type = password1.getAttribute('type') === 'password' ? 'text' : 'password';
						    			password1.setAttribute('type', type);
						    			this.classList.toggle('fa-eye-slash');
									};

									togglePassword2.onclick = function() {
						    			const type = password2.getAttribute('type') === 'password' ? 'text' : 'password';
						    			password2.setAttribute('type', type);
						    			this.classList.toggle('fa-eye-slash');
									};
								}
							</script>

							<div class="container" style="padding-left: 0px;">
								<input class="inputBox" id="password1" type="password" name="Password_reset" placeholder="<?php echo $nav['passph']; ?>" value="<?php if(isset($password)){ echo $password; }?>" required maxlength="45">	
								<i class="far fa-eye" id="togglePassword1"></i>
							</div>

							<div class="container" style="padding-left: 0px;">
								<input class="inputBox" id="password2" type="password" placeholder="<?php echo $nav['passRepeatph']; ?>" name="Passwordrepeat" required maxlength="45">
								<i class="far fa-eye" id="togglePassword2"></i>
							</div>

							<script type="text/javascript">
								const pass1 = document.getElementById("password1");
								const pass2 = document.getElementById("password2");

								pass2.focus();
								pass2.setCustomValidity("<?= $er_passwordrepeat ?>");

								pass2.addEventListener("input", function (event) {
									if(pass2.value != pass1.value) {
										pass2.focus();
										pass2.setCustomValidity("The comfirmation password must be the same");
									} else {
										pass2.setCustomValidity("");
									}
								});
							</script>

							<input class="inputBox" id="code" type="text" name="ConnexionCode" placeholder="<?php echo $nav['connph']; ?>" value="<?php if(isset($resetCode)){ echo $resetCode; }?>" required maxlength="45">

							<script type="text/javascript">
								const validCode = document.getElementById("code");

								validCode.focus();
								validCode.setCustomValidity("<?= $er_confirmed ?>");

								validCode.addEventListener("input", function (event) {
									if(validCode.value == "<?= $bad_code ?>") {
										validCode.focus();
										validCode.setCustomValidity("<?= $er_confirmed ?>");
									} else {
										validCode.setCustomValidity("");
									}
								});
							</script>
						<?php else: ?>

						<?php endif; ?>

						<div style="text-align: center;">
							<input id="loginButton" type="submit" name="login_button" value="<?php echo $nav['logInBtnTxt']; ?>" style="margin-top: 25px;">
							<br>
							<a id="passreset" class="href-link" href="?forgot"><?php echo $nav['forgotPassLink']; ?></a>
						</div>
					<!-- forgot else end -->
					<?php endif; ?>
		
				</form>
			</div>
		</main>

		<footer class="section section-dark" style="margin-top: 15px;bottom: 0;padding: 20px 40px;">
	    	<input type="submit" name="top_button" value="<?php echo $nav['topBtn']; ?>" onclick='window.scrollTo({top: 0, behavior: "smooth"});' style="margin-top: 0px;">
	    	<br>
	    	<br>
	      	<p>&copy Barthélemy Quentin 6B - Travail de fin d'études - Saint-Jean Berchmans 2020-2021</p>
	    </footer>

	</body>

</html>
