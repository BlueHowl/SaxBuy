<?php
// ------ importation des variables de connexion ----------------
include('./secret/mdp.php');

// ------ connexion à la base de données ------------------------
try
	{$bdd = new PDO("mysql:host=$host;dbname=$db", $user, $userpswd);}

catch(Exception $e)
	{die('Erreur : '.$e->getMessage());}  // arrêt en cas d'erreur 

$code_article = $_POST['id_article'];

$req = $bdd->prepare("DELETE FROM pieces WHERE code = $code_article");
$req->execute()or die(print_r($req->errorInfo()));

$req->closeCursor(); // Termine le traitement de la requête

echo "taitement terminé";

//$req->closeCursor(); // Termine le traitement de la requête

?>
