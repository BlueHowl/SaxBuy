<?php
// ------ importation des variables de connexion ----------------
include('./secret/mdp.php');

// ------ connexion à la base de données ------------------------
try
	{$bdd = new PDO("mysql:host=$host;dbname=$db", $user, $userpswd);}

catch(Exception $e)
	{die('Erreur : '.$e->getMessage());}  // arrêt en cas d'erreur 

// ------ préparation de la requete -------------------------------
$req = $bdd->prepare("SELECT * FROM pieces");

// ------ exécution de la requete -------------------------------
$req->execute();

// ------ vérification de la requête ----------------------------
//$req->debugDumpParams();
//echo "<br>";

// ------ préparation de l'affichage titre-----------------------
echo "<FONT size=10 color='red'>";
echo "Voici votre liste", "<br>";
echo  "</FONT>";

// ------ affichage sous forme d'un tableau ---------------------
echo "<table border=2 >";

// --traitement de la requête ($req) ligne par ligne tjs avec SELECT ---
foreach($req as $row) 
	{echo "<tr>";
	 echo "<td>",$row['designation'],"</td>","<td>",$row['prix'],"</td>";
	 echo "</tr>";
	 };

echo "</table>";

$req->closeCursor(); // Termine le traitement de la requête

?>
