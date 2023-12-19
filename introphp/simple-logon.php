<?php
	session_start();

	include('./connexionDB.php');

	if (isset($_SESSION['id'])){
		header('Location: http://bartque.alwaysdata.net/index');
		exit;
	}

	if(!empty($_POST)){
		extract($_POST);
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
	 
			if($valid){
				$password = md5($password);

				$req = $bdd->prepare('INSERT INTO clients VALUES(null, :mail, :password, :name, :surname, :birthdate, :city, : street, :street_number, :cp, :isAdmin');
				$req->bindValue(':mail', $mail, PDO::PARAM_STR);
				$req->bindValue(':password', $password, PDO::PARAM_STR);
				$req->bindValue(':name', $name, PDO::PARAM_STR);
				$req->bindValue(':surname', $surname, PDO::PARAM_STR);
				$req->bindValue(':birthdate', $birthdate, PDO::PARAM_STR);
				$req->bindValue(':city', $city, PDO::PARAM_STR);
				$req->bindValue(':street', $street, PDO::PARAM_STR);
				$req->bindValue(':street_number', $street_number, PDO::PARAM_STR);
				$req->bindValue(':cp', $cp, PDO::PARAM_INT);
				$req->bindValue(':isAdmin', false, PDO::PARAM_BOOL);

				$req->execute()or die(print_r($req->errorInfo()));
				$req->closeCursor();
				//			
 
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

	</head>

	<body>

		<div id="register-panel">
			<form id="register-panel-back" method="post">

				<input class="inputBox" id="mail" type="email" name="Email" placeholder="<?php echo $nav['emailph']; ?>" value="<?php if(isset($mail)){ echo $mail; }?>" required maxlength="45">

				<input class="inputBox" id="password" type="password" name="Password" placeholder="<?php echo $nav['passph']; ?>" value="<?php if(isset($password)){ echo $password; }?>" required maxlength="45">	

        		<input class="inputBox" type="text" placeholder="name" name="Name" value="<?php if(isset($name)){ echo $name; }?>" pattern="[^0-9]*" required maxlength="45">

        		<input class="inputBox" type="text" placeholder="surname" name="Surname" value="<?php if(isset($surname)){ echo $surname; }?>" pattern="[^0-9]*" required maxlength="45">

        		<input class="inputBox" type="date" placeholder="birth date" name="Birthdate" value="<?php if(isset($birthdate)){ echo $birthdate; }?>" required>

        		<input class="inputBox" type="text" placeholder="city" name="City" value="<?php if(isset($city)){ echo $city; }?>" pattern="[^0-9]*" required maxlength="45">

        		<input class="inputBox" type="text" placeholder="street" name="Street" style="width: 69%; margin-right: 2%" value="<?php if(isset($street)){ echo $street; }?>" required maxlength="45">

        		<input class="inputBox" type="text" placeholder="street num" name="Streetnumber" style="width: 27%;" value="<?php if(isset($streetNumber)){ echo $streetNumber; }?>" required maxlength="6">

       			<input class="inputBox" type="number" placeholder="cp" name="Cp" value="<?php if(isset($cp)){ echo $cp; }?>" required max="99999">

				<div style="text-align: center;">
					<input id="registerButton" type="submit" name="register_button" value="logon">
				</div>
			</form>

		</div>

	</body>

</html>
