<?php
// ------ importation des variables de connexion ----------------
include('./secret/mdp.php');

// ------ connexion à la base de données ------------------------
try
	{$bdd = new PDO("mysql:host=$host;dbname=$db", $user, $userpswd);}

catch(Exception $e)
	{die('Erreur : '.$e->getMessage());}  // arrêt en cas d'erreur 

$code_article = $_POST['id_article'];

$req = $bdd->prepare("SELECT designation, prix FROM pieces WHERE code = :code_article");
$req->bindValue(':code_article', $code_article, PDO::PARAM_INT);
$req->execute();
$req = $req->fetch();

echo $req["designation"] . " : " . $req["prix"] . " €";

//$req->closeCursor(); // Termine le traitement de la requête

?>
