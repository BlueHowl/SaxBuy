<?php
// ------ importation des variables de connexion ----------------
include('./secret/mdp.php');

// ------ connexion à la base de données ------------------------
try
	{$bdd = new PDO("mysql:host=$host;dbname=$db", $user, $userpswd);}

catch(Exception $e)
	{die('Erreur : '.$e->getMessage());}  // arrêt en cas d'erreur 

$req = $bdd->prepare("SELECT COUNT(code) AS numCode FROM pieces WHERE fournisseur = 8");
$req->execute();
$req = $req->fetch();

echo "Le nombre d'article du fournisseur n°8 est : " . $req["numCode"];

?>
