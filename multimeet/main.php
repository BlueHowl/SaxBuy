<?php

	include('./auth.php');

	try
		{$bdd = new PDO('mysql:host='.$host.';dbname='.$db, $user, $userpswd);}
	catch(Exception $e)
		{die('Erreur : '.$e->getMessage());}


	if(!empty($_POST)){
		if (isset($_POST['join_rnd_meet']) and isset($_POST['num']) and isset($_POST['name'])) {
			$number = $_POST['num'];
			$name = $_POST['name'];

			setcookie('name', $name, 2147483647, "/multimeet/");

			if($number < 2) {
				$number = 2;
			} else if($number > 32) {
				$number = 32;
			}
			
			$reqroom = $bdd->prepare('SELECT roomid, token FROM room WHERE slots = :num AND slot_left > 0 ORDER BY RAND() LIMIT 1');
			$reqroom->bindValue(':num', $number, PDO::PARAM_INT);

			$reqroom->execute()or die(print_r($reqroom->errorInfo()));

			$reqroom = $reqroom->fetch();

			if (!empty($reqroom)) {

				$req = $bdd->prepare('UPDATE room SET slot_left = slot_left - 1 WHERE roomid = :id');
				$req->bindValue(':id', $reqroom['roomid'], PDO::PARAM_STR);

				$req->execute()or die(print_r($req->errorInfo()));

				$req->closeCursor();

				header('Location: http://bartque.alwaysdata.net/multimeet/call?room=' . $reqroom['token']);
				exit;
			} else {
				$token = md5(uniqid($name, true));

				$req = $bdd->prepare('INSERT INTO room VALUES(null, :token, :slots, :slot_left)');
				$req->bindValue(':token', $token, PDO::PARAM_STR);
				$req->bindValue(':slots', $number, PDO::PARAM_INT);
				$req->bindValue(':slot_left', $number - 1, PDO::PARAM_INT);

				$req->execute()or die(print_r($req->errorInfo()));

				$req->closeCursor();

				header('Location: http://bartque.alwaysdata.net/multimeet/call?room=' . $token);
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

		<link rel="icon" href="img/logo.png">
		<title>MultiMeet</title>
	</head>

	<body style="background-color: #37404f;">

		<header>
			<img src="img/banner.png" alt="banner" style="width: 100%; margin-top: -3%;">
		</header>

		<main>
			<div style="text-align: center;">
				<form id="join-meet-panel" method="post">
					<input class="inputBox" type="text" placeholder="Pseudo" name="name" value="<?php if(isset($_COOKIE['name'])){ echo $_COOKIE['name']; } ?>" required maxlength="100" style="width: 25%; text-align: center;">
					<br>
					<input id="joinRndMeet" type="submit" name="join_rnd_meet" value="Rejoindre une salle" style="width: 18%;">
					<input class="inputBox" type="number" placeholder="Places" name="num" value="4" required min="2" max="32" style="width: 6.5%;">
				</form>
			</div>
		</main>

	</body>

</html>