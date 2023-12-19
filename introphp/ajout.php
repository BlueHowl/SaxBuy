<?php

// ------ connexion à la base de données -----------------------
include('./secret/mdp.php');
echo $designation,$prix,$quantite,$fournisseur;
try
	{$bdd = new PDO("mysql:host=$host;dbname=$db", $user, $userpswd);}
catch(Exception $e)
	{die('Erreur : '.$e->getMessage());}

// ------ récupération des données du formulaire ----------------
$designation = $_POST['designation'];
$prix = $_POST['prix'];
$quantite = $_POST['quantite'];
$fournisseur = $_POST['fournisseur'];

//echo $designation, $prix, $quantite, $fournisseur;

// ------ préparation de la requête -----------------------------
$req = $bdd->prepare('INSERT INTO pieces VALUES(null, :designation, :prix, :quantite, :fournisseur)');

$req->bindValue(':designation', $designation, PDO::PARAM_STR);
$req->bindValue(':prix', $prix, PDO::PARAM_STR);
$req->bindValue(':quantite', $quantite, PDO::PARAM_INT);
$req->bindValue(':fournisseur', $fournisseur, PDO::PARAM_INT);

// ------ exécution de la requête -------------------------------
$req->execute()or die(print_r($req->errorInfo()));


$req->closeCursor(); // Termine le traitement de la requête

echo "taitement terminé";

?>

