<?php
  	session_start();

  	include('./connexionDB.php');

  	if (!isset($_COOKIE['id']) and !isset($_SESSION['id'])) {
		header('Location: http://bartque.alwaysdata.net');
		exit;
	}

	if(isset($_COOKIE['id'])) {
		$_idClient = $_COOKIE['id'];
	} else {
		$_idClient = $_SESSION['id'];
	}
	//à changer
	$req_admin = $DB->query("SELECT isAdmin FROM clients WHERE id_client = :id",
		array('id' => $_idClient));
	$req_admin = $req_admin->fetch();

	if($req_admin['isAdmin'] == false) {
		header('Location: http://bartque.alwaysdata.net');
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
	parse_str(preg_replace("/\r|\n/", "", $pageTxt[0] . $pageTxt[5]), $nav);

	$req_out_articles = $DB->query("SELECT id_article, brand, model FROM articles WHERE left_quantity = 0");

	if(!empty($_POST)){
		extract($_POST);
		$valid = true;

		if (isset($_POST['addProduct_button'])){
			$brand = $_POST['Brand'];
			$model = $_POST['Model'];
			$desc = $_POST['Desc'];
			$imageUrl = $_POST['ImageUrl'];
 			$objType = $_POST['ObjType'];
 			$quantity = $_POST['Quantity'];
 			$price = $_POST['Price'];

			$DB->insert("INSERT INTO articles VALUES (:brand, :model, :description, :img_url, :object_type, :left_quantity, :price)",
				array('brand' => $brand, 'model' => $model, 'description' => $desc, 'img_url' => $imageUrl, 'object_type' => $objType, 'left_quantity' => $quantity, 'price' => $price));
			header('Location: https://bartque.alwaysdata.net/php/adminPanel');
			exit;
		}

		if (isset($_POST['button_add_stock'])){
			$id = $_POST['art_id'];
			$qte = $_POST['add_stock_num'];
			if($qte > 0) {
				$DB->insert("UPDATE articles SET left_quantity = left_quantity + :qte WHERE id_article = :id",
	          		array('qte' => $qte, 'id' => $id));
				header('Location: https://bartque.alwaysdata.net/php/adminPanel');
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
		<link rel="icon" href="../img/favicon.png">
		<title>SaxBuy-Admin-Panel</title>

	</head>

	<body style="background-color: black;">
		<?php include 'navMenu.php';?>

		<main style="color:black">
			<div id="addProcductDiv">
				<form method="post">
					<h3>Add Product : </h3>

					<input class="inputBox" id="brand" type="text" name="Brand" placeholder="Brand name" value="<?php if(isset($brand)){ echo $brand; }?>" required maxlength="45">

					<input class="inputBox" id="model" type="text" name="Model" placeholder="Model name" value="<?php if(isset($model)){ echo $model; }?>" required maxlength="45">

					<textarea name="Desc" cols="40" rows="5" placeholder="Description" required maxlength="2000"><?php if(isset($desc)){ echo $desc; }?></textarea>

					<input class="inputBox" id="imageUrl" type="text" name="ImageUrl" placeholder="Image url" value="<?php if(isset($imgurl)){ echo $imgurl; }?>" required maxlength="100">

					<input class="inputBox" id="objType" type="number" name="ObjType" placeholder="Object type" value="<?php if(isset($objType)){ echo $objType; }?>" required>

					<input class="inputBox" id="quantity" type="number" name="Quantity" placeholder="Stock quantity" value="<?php if(isset($quantity)){ echo $quantity; }?>" required>

					<input class="inputBox" id="price" type="text" name="Price" placeholder="Price" value="<?php if(isset($price)){ echo $price; }?>" required>

					<input id="addProductBtn" type="submit" name="addProduct_button" value="Add Product">

				</form>
			</div>

			<div style="padding: 30px 0px; color:white">
				<h3 style="padding-bottom: 20px;">Out of stock products : </h3>
				<ul style="max-width:800px">
					<?php
						foreach ($req_out_articles as $row) {
							echo '<li style="display: inline-flex; padding-bottom: 10px;"><h3 style="margin: 0px 15px;">' . $row['brand'] . ' ' . $row['model'] . '</h3>
							<form method="post">
								<input type="number" name="add_stock_num" style="width: 50px; color: black;">
								<input type="submit" name="button_add_stock" value="Add stock" style="width: 100px; margin: 0px; margin-left: 5px;">
								<input type="hidden" name="art_id" value="' . $row['id_article'] . '">
							</form>
						</li>';
						}
					?>
				</ul>
			</div>
			
			<div style="padding: 15px 0px;">
				<button style="margin-left: 10px;" onclick="window.open('https://phpmyadmin.alwaysdata.com/phpmyadmin/index.php?lang=en&collation_connection=utf8mb4_unicode_ci&token=abb62eda49e98df20f10671973dbe2ec', '_blank')">Base de donnée</button>

			</div>
		</main>
	</body>

</html>
