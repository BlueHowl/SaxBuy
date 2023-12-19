<?php
  	session_start();

  	include('./connexionDB.php');
	
  	//$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	//$url_components = parse_url($actual_link);
	//parse_str($url_components['query'], $params);

	if (!isset($_COOKIE['id']) and !isset($_SESSION['id'])){
		header('Location: http://bartque.alwaysdata.net');
		exit;
	}

	$langId = "en";

  	$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$url_components = parse_url($actual_link);
	parse_str($url_components['query'], $params);

	if(isset($params['lang'])) {
		$langId = $params['lang'];
	}

	$flang = fopen("../languages/" . $langId . ".lang", "r");
	$flang = fread($flang,filesize("../languages/" . $langId . ".lang"));

	$pageTxt = explode(",*-", $flang);
	parse_str(preg_replace("/\r|\n/", "", $pageTxt[0] . $pageTxt[3]), $nav);

	if(isset($_COOKIE['id'])) {
		$_idClient = $_COOKIE['id'];
	} else {
		$_idClient = $_SESSION['id'];
	}

	$req = $DB->query("SELECT ar.id_article, ar.brand, ar.model, ar.img_url, ar.price, ar.left_quantity, cal.quantity
		FROM clients cl
		INNER JOIN cart ca
		ON ca.fk_client = cl.id_client
		INNER JOIN cart_line cal
		ON cal.fk_cart = ca.id_cart
		INNER JOIN articles ar
		ON cal.fk_articles = ar.id_article
		WHERE ca.fk_client = :id AND ca.valid = 0",
		array('id' => $_idClient));

	//opti?
	$req_client = $DB->query("SELECT id_cart, clients.street, clients.street_number, clients.cp, clients.city FROM clients CROSS JOIN cart ON clients.id_client = cart.fk_client WHERE clients.id_client = :id",
		array('id' => $_idClient));
	$req_client = $req_client->fetch();


	if(!empty($_POST)){
   		extract($_POST);

	    if (isset($_POST['article'])){
	    	$article = $_POST['article'];

	      	$DB->insert("DELETE FROM cart_line WHERE fk_cart = :id_cart AND fk_articles = :article",
	        	array('id_cart' => $req_client['id_cart'], 'article' => $article));

	      	header('Location: http://bartque.alwaysdata.net/php/cart');
      		exit;
    	}

    	if (isset($_POST['buttonPlus'])){
    		$product_id = $_POST['article_id'];
    		$product_qte = $_POST['article_qte'];
    		$product_left_qte = $_POST['article_leftqte'];

    		if($product_left_qte > $product_qte) {
		    	$DB->insert("UPDATE cart_line SET quantity = quantity + 1 WHERE fk_cart = :id_cart AND fk_articles = :product_id",
	          		array('id_cart' => $req_client['id_cart'], 'product_id' => $product_id));

		      	header('Location: http://bartque.alwaysdata.net/php/cart');
	      		exit;
      		}
    	}

    	if (isset($_POST['buttonMinus'])){
    		$product_id = $_POST['article_id'];
    		$product_qte = $_POST['article_qte'];

    		if ($product_qte <= 1) {
    			$DB->insert("DELETE FROM cart_line WHERE fk_cart = :id_cart AND fk_articles = :product_id",
	        		array('id_cart' => $req_client['id_cart'], 'product_id' => $product_id));
    		} else {
	    		$DB->insert("UPDATE cart_line SET quantity = quantity - 1 WHERE fk_cart = :id_cart AND fk_articles = :product_id",
          			array('id_cart' => $req_client['id_cart'], 'product_id' => $product_id));
	    	}

	      	header('Location: http://bartque.alwaysdata.net/php/cart');
      		exit;
    	}

    	if (isset($_POST['command_btn'])){

    		$product_list = "";
    		$list_price = 0.00;

    		foreach ($req as $row) {

    			$DB->insert("UPDATE articles SET left_quantity = left_quantity - :quantity WHERE id_article = :id_article",
          			array('quantity' => $row['quantity'], 'id_article' => $row['id_article']));

    			$list_price += $row['price'] * $row['quantity'];

    			$product_list .= '<div class="item" style="margin-bottom:15px; border-top:  1px solid #E1E8EE;
  									border-bottom:  1px solid #E1E8EE;">
								    <div class="quantity" style="
								    color: white;
								    float: left;
								    vertical-align: center;
								    margin-top: 50px;
								    font-size: 50px;
								    background-color: rgba(40, 46, 52, 0.8);
								    border-radius: 4px;
								    padding: 10px 30px;
								">
								    <span>' . $row['quantity'] . '</span>
							    </div>
								

							    <div class="image">
							      <img src="' . $row['img_url'] . '" alt="" style="width: 25%;float: left;">
							    </div>
								
							    <div class="description" style="float: left;width: 200px;margin-top: 20px;color: black;font-size: 25px;margin-left: -10px;font-weight: bold;">
							      <span>' . $row['brand'] . '</span>
							      <span>' . $row['model'] . '</span>
							    </div>

							    <div class="total-price" style="
								    float: right;
								    margin-right: 50px;
								    margin-top: 70px;
								    color: black;
								    font-size: 35px;
								">' . $row['price'] * $row['quantity'] . ' €</div>
							  </div>';
    		}

    		$product_list .= '<div id="resume_command" class="item" style="text-align: center;">
								<h2 style="text-align: center; color: #000;">Total price : ' . $list_price . ' €</h2>
								<br>
								<h3 style="text-align: center; color: #000;">The products will be delivered at : ' . $req_client['street'] . ', N°' . $req_client['street_number'] . ' ' . $req_client['cp'] . ' ' . $req_client['city'] . '</h3>
							</div>';


    		require("../phpMailer/src/PHPMailer.php");
			require("../phpMailer/src/SMTP.php");
			require("../phpMailer/src/Exception.php");

			$mailTemplate = fopen("commandMailTemplate.php", "r");
			$mailTemplate = fread($mailTemplate,filesize("commandMailTemplate.php"));

			$msg = str_replace("command_number", $req_client['id_cart'], $mailTemplate);
			$msg = str_replace("place_list", $product_list, $msg);

			if(isset($_COOKIE['mail'])) {
				$_mailClient = $_COOKIE['mail'];
			} else {
				$_mailClient = $_SESSION['mail'];
			}
			
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
			$email->AddAddress($_mailClient);
			$email->CharSet="utf-8";
			$email->Subject='Your SaxBuy command N°' . $req_client['id_cart'];
			$email->Body=$msg;
			
			if(!$email->Send()){
			}
			else{     
					
			}
			$email->SmtpClose();
			unset($email);

			$DB->insert("DELETE FROM cart WHERE fk_client = :id",
	        		array('id' => $_idClient));

			$DB->insert("INSERT INTO cart (valid, fk_client) VALUES (:valid, :id)",
								array('valid' => false, 'id' => $_idClient));

	      	header('Location: http://bartque.alwaysdata.net/php/load');
      		exit;
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
		<link rel="stylesheet" href="../css/styles-cart.css"/>
		<link rel="icon" href="../img/favicon.png">
		<title><?php echo $nav['cartPageTitle']; ?></title>

	</head>

	<body>
		<?php include 'navMenu.php';?>

		<main style="min-height: 65.5%;">

			<div class="shopping-cart">
			  	<div class="title">
			    	<?php echo $nav['cartTitle']; ?>
			  	</div>		  	
					<?php
						$article_number = 0;
						$total_command_price = 0.00;
						foreach ($req as $row) {
							$total_command_price += $row['price'] * $row['quantity'];
							$article_number += 1;
							echo '<div class="item">
									<form name="deleteform' . $row['id_article'] . '" method="post">
									  	<div class="buttons">
									  		<input type="hidden" name="article" value="' . $row['id_article'] . '">
									      	<span class="delete-btn" name="deleteButton" onclick="deleteform' . $row['id_article'] . '.submit()"></span>
									    </div>
									</form>

								    <div class="image">
								      <img src="' . $row['img_url'] . '" alt="" />
								    </div>
									
								    <div class="description">
								      <span>'. $row['brand'] .'</span>
								      <span>' . $row['model'] . '</span>
								    </div>
								 	
								    <div class="quantity">
								    	<form name="plusQte' . $row['id_article'] . '" method="post" style="float:left;">
										    <button class="plus-btn" name="buttonPlus" type="submit">
										    	<img src="../img/svg/plus.svg" alt=""/>
										    </button>
										    <input type="hidden" name="article_id" value="' . $row['id_article'] . '">
										    <input type="hidden" name="article_qte" value="' . $row['quantity'] . '">
										    <input type="hidden" name="article_leftqte" value="' . $row['left_quantity'] . '">
										</form>

									      	<input type="text" name="art_qte" value="' . $row['quantity'] . '" style="height:30px; border-radius:3px; margin:0px 10px;" readonly>

									    <form name="minusQte' . $row['id_article'] . '" method="post" style="float:right;">
										    <button class="minus-btn" name="buttonMinus" type="submit">
										    	<img src="../img/svg/minus.svg" alt="" onclick="changeQte' . $row['id_article'] . '.submit()"/>
										    </button>
										    <input type="hidden" name="article_id" value="' . $row['id_article'] . '">
										    <input type="hidden" name="article_qte" value="' . $row['quantity'] . '">
										</form>
								    </div>
									
								    <div class="total-price">' . $row['price'] * $row['quantity'] . ' €</div>
								  </div>';
						}
					?>



			</div>

			<?php if ($article_number != 0 && $command_bool != true): ?>
				<div id="resume_command" style="text-align: center;">
					<h2 style="text-align: center; color: #fff;"><?php echo $nav['priceTitle']; ?><?php echo $total_command_price; ?> €</h2>
					<br>
					<h3 style="text-align: center; color: #fff;"><?php echo $nav['commandTitle']; ?><?php echo $req_client['street'] . ', N°' . $req_client['street_number'] . ' ' . $req_client['cp'] . ' ' . $req_client['city']; ?></h3>
					<form method="post" id="command_form" name="command_form_name">
						<input type="submit" name="command_btn" value="<?php echo $nav['btnCommand']; ?>" style="margin-bottom: 30px;">
					</form>

				</div>
			<?php else: ?>
				<h2 style="text-align: center; color: #fff;"><?php echo $nav['emptyTitle']; ?></h2>
			<?php endif; ?>

		</main>

		<footer class="section section-dark" style="margin-top: 40px;bottom: 0; padding: 20px 40px;">
	    	<input type="submit" name="top_button" value="<?php echo $nav['topBtn']; ?>" onclick='window.scrollTo({top: 0, behavior: "smooth"});' style="margin-top: 0px;">
	    	<br>
	    	<br>
	      	<p>&copy Barthélemy Quentin 6B - Travail de fin d'études - Saint-Jean Berchmans 2020-2021</p>
	    </footer>

	</body>

</html>