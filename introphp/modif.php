<?php
// ------ importation des variables de connexion ----------------
include('./secret/mdp.php');

// ------ connexion à la base de données ------------------------
try
	{$bdd = new PDO("mysql:host=$host;dbname=$db", $user, $userpswd);}

catch(Exception $e)
	{die('Erreur : '.$e->getMessage());}  // arrêt en cas d'erreur 

$req = $bdd->prepare('UPDATE pieces SET prix = prix + 1');
$req->execute()or die(print_r($req->errorInfo()));

$req->closeCursor();

echo "Terminé";

?>