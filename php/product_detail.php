<?php 
	
	session_start();

	include('./connexionDB.php');
	
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
  parse_str(preg_replace("/\r|\n/", "", $pageTxt[0] . $pageTxt[7]), $nav);

  if(isset($_COOKIE['id'])) {
    $_idClient = $_COOKIE['id'];
  } else {
    $_idClient = $_SESSION['id'];
  }
	
	if(isset($params['product'])) {
		$product_id = $params['product'];
		
		$req = $DB->query("SELECT *, AVG(rate) AS averageRate FROM articles INNER JOIN comments ON articles.id_article = comments.fk_article WHERE id_article = :product_id",
			array('product_id' => $product_id));
		$req = $req->fetch();
	}

  if(isset($_idClient)) {
    $req_client = $DB->query("SELECT id_cart, profil_img, (SELECT id_client FROM comments INNER JOIN clients ON comments.fk_client = clients.id_client WHERE fk_article = :product_id AND id_client = :id) AS id_client FROM clients INNER JOIN cart ON clients.id_client = cart.fk_client WHERE clients.id_client = :id",
      array('product_id' => $product_id, 'id' => $_idClient));
    $req_client = $req_client->fetch();
  }

  $req_comments = $DB->query("SELECT id_client, surname, name, profil_img, id_comment, comment_text, rate, _date, _time FROM comments INNER JOIN clients ON comments.fk_client = clients.id_client WHERE fk_article = :product_id",
      array('product_id' => $product_id));

  if(!empty($_POST)){
    extract($_POST);

    if (isset($_POST['AddButton'])){
      $qte = $_POST['Quantity_select'];

      $req_article = $DB->query("SELECT fk_articles FROM clients INNER JOIN cart ON clients.id_client = cart.fk_client INNER JOIN cart_line ON cart.id_cart = cart_line.fk_cart WHERE clients.id_client = :id AND fk_articles = :product_id",
        array('id' => $_idClient, 'product_id' => $product_id));
      $req_article = $req_article->fetch();

      if ($req_article['fk_articles'] != $product_id){
        $DB->insert("INSERT INTO cart_line (fk_cart, fk_articles, quantity) VALUES (:id_cart, :product_id, :qte)",
          array('id_cart' => $req_client['id_cart'], 'product_id' => $product_id, 'qte' => $qte));
      } else {
        $DB->insert("UPDATE cart_line SET quantity = quantity + :qte WHERE fk_cart = :id_cart AND fk_articles = :product_id",
          array('qte' => $qte, 'id_cart' => $req_client['id_cart'], 'product_id' => $product_id));
      }

      header('Location: http://bartque.alwaysdata.net/php/shop?product=*');
      exit;
    }

    if (isset($_POST['button_send'])){
      if(isset($_COOKIE['id']) || isset($_SESSION['id'])) {
        $comment = $_POST['comm_text'];
        if(isset($_POST['rate'])) {
          $rate = $_POST['rate'];
        } else {
          $rate = 0;
        }

        $DB->insert("INSERT INTO comments VALUES (:comment_text, :rate, :_date, :_time, :fk_article, :fk_client)",
          array('comment_text' => htmlspecialchars($comment), 'rate' => $rate, '_date' => date('Y-m-d'), '_time' => date('h:i'), 'fk_article' => $product_id, 'fk_client' => $_idClient));

        header('Location: https://bartque.alwaysdata.net/php/product_detail?product=' . $product_id);
        exit;
      }
    }

    if (isset($_POST['comment_id'])){
      if ((isset($_COOKIE['id']) && $_COOKIE['isAdmin'] == true) || (isset($_SESSION['id']) && $_SESSION['isAdmin'] == true) || ($_POST['com_client_id'] == $_COOKIE['id']) || ($_POST['com_client_id'] == $_SESSION['id'])) {
        $comment_id = $_POST['comment_id'];

        $DB->insert("DELETE FROM comments WHERE id_comment = :comment_id",
          array('comment_id' => $comment_id));

        header('Location: https://bartque.alwaysdata.net/php/product_detail?product=' . $product_id);
        exit;
      }
    }

  }

?>

<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $nav['productPageTitle']; ?></title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="../css/styles.css"/>
    <link rel="stylesheet" href="../css/styles-product_detail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">

    <link rel="icon" href="../img/favicon.png">
  </head>

  <body>
    <?php include 'navMenu.php';?>
	
  <main>
  	<?php if (!empty($req)): ?>
      <div class="container" style="margin-bottom: 0px;">
        <div class="img_preview">
          <img src="<?php if(isset($req['img_url'])){ echo $req['img_url']; }?>" alt="product_image">
        </div>

        <div class="right-column">
          <form method="post">
            <div class="description">
              <h1><?php if(isset($req['brand'])){ echo $req['brand']; }?> <?php if(isset($req['model'])){ echo $req['model']; } ?></h1>
              <div class="showrate">
                <?php if (isset($req['averageRate'])): ?>
                  <label style="<?php if($req['averageRate'] >= 5){ echo 'color:#ffc700;'; }?>">5 stars</label>
                  <label style="<?php if($req['averageRate'] >= 4){ echo 'color:#ffc700;'; }?>">4 stars</label>
                  <label style="<?php if($req['averageRate'] >= 3){ echo 'color:#ffc700;'; }?>">3 stars</label>
                  <label style="<?php if($req['averageRate'] >= 2){ echo 'color:#ffc700;'; }?>">2 stars</label>
                  <label style="<?php if($req['averageRate'] >= 1){ echo 'color:#ffc700;'; }?>">1 star</label>
                <?php else: ?>
                  <p><?php echo $nav['rateTxt']; ?></p>
                <?php endif; ?>
                
              </div>

            </div>

            <div class="description">
              <h3><?php if(isset($req['description'])){ echo $req['description']; }?></h3>
            </div>
            
              <div class="button_choice">
              <!--
                <span>Strength :</span>
                <div class="choice">
                  <button>1</button>
                  <button>1.5</button>
                  <button>2</button>
                  <button>2.5</button>
                  <button>3</button>
                  <button>3.5</button>
                  <button>4</button>
                </div
                -->
                <div class="button_choice">

                  <?php if (isset($req['left_quantity']) && $req['left_quantity'] != 0): ?>
                    <span><?php echo $nav['qt']; ?></span>
                    <div class="choice">
                      <select name="Quantity_select" data-width="fit" class="selectpicker1 show-menu-arrow" data-style="btn-primary-i">
                        <?php 
                          if(isset($req['left_quantity'])) {
                            $left_quantity = $req['left_quantity'];
                            $i = 1;
                            while ($i <= $left_quantity) {
                              echo '<option value="' . $i . '">' . $i . '</option>';
                              $i++;
                            }
                          }
                        ?>
                      </select>

                      <script>
                        $(function(){
                          $('.selectpicker1').selectpicker();
                        });
                      </script>

                    </div>
                  <?php else: ?>
                      <h4 style="margin-bottom: 10px; font-size: 20px;"><?php echo $nav['so']; ?></h4>
                  <?php endif; ?>
              </div>
            </div>

            <div class="price">
              <span><?php if(isset($req['price'])){ echo number_format($req['price'], 2, ',', ' '); }?> €</span>
              <?php 
                if(isset($_COOKIE['id']) || isset($_SESSION['id'])) { 
                  if(isset($req['left_quantity']) && $req['left_quantity'] != 0) {
                    echo '<input name="AddButton" type="submit" class="addButton" value="' . $nav["addBtn"] . '">'; 
                  }
                } else {
                  echo "<p>" . $nav['noAccTxt'] . "</p>";
                }
              ?>
            </div>
          </form>
        </div>

        <div style="z-index:9999;">
          <a style="cursor:pointer; text-decoration: none; color: white; margin-right: 10px;" href="https://bartque.alwaysdata.net/php/shop?filter=*"><?php echo $nav['b']; ?></a>
        </div>

      </div>

      <div style="display: flex;">
        <div class="container" style="margin-top: 5px; display: inline-block; width: 100%;">
          <div style="text-align: center; padding-top: 10px;">
            <h1 style="color: #FFD732;"><?php echo $nav['commentTitle']; ?></h1>
          </div>

          <div class="comment_block">

            <?php

                if(isset($_COOKIE['id']) || isset($_SESSION['id'])) {
                  if(!isset($req_client['id_client'])) {
                    echo '<div class="create_new_comment">
                    <div id="profil_img_holder" class="profil_img">
                      <i id="defaultIcon" class="fas fa-user fa-3x" style="text-align:center; margin-top:7px;"></i>
                    </div>

                    <form style="height:80px;"  method="post">
                      <div class="input_comment">
                        <input type="text" name="comm_text" placeholder="Write your review.." maxlength="510" required>
                        <input type="submit" name="button_send" value="Send" style="width: 20%; margin: 0px 10px; height: inherit;">
                      </div>

                      <div class="rate" style="padding-top:0px; padding-left:10px;">
                        <input type="radio" id="star5" name="rate" value="5" />
                        <label for="star5" title="text">5 stars</label>
                        <input type="radio" id="star4" name="rate" value="4" />
                        <label for="star4" title="text">4 stars</label>
                        <input type="radio" id="star3" name="rate" value="3" />
                        <label for="star3" title="text">3 stars</label>
                        <input type="radio" id="star2" name="rate" value="2" />
                        <label for="star2" title="text">2 stars</label>
                        <input type="radio" id="star1" name="rate" value="1" />
                        <label for="star1" title="text">1 star</label>
                      </div>

                    </form>

                   </div>';

                   if(isset($req_client['profil_img'])) {
                      echo '<style>
                          #profil_img_holder {
                            background-image: url("'.$req_client['profil_img'].'")
                          } 
                          #defaultIcon {
                            display:none;
                          }
                        </style>';
                    }
                  } else {
                    echo '<p>You already reviewed this product</p>';
                  }

                } else {
                  echo '<p>Create an acount to review products</p>';
                }
              ?>

           <div class="new_comment">

            <ul class="user_comment">

              <?php
                
                foreach ($req_comments as $row) {
                  if((isset($_COOKIE['id']) && $_COOKIE['isAdmin'] == true) || (isset($_SESSION['id']) && $_SESSION['isAdmin'] == true)) {
                    echo '<li>
                      <div id="profil_img_holder' . $row['id_client'] . '" class="profil_img">
                        <i id="defaultIcon' . $row['id_client'] . '" class="fas fa-user fa-3x" style="text-align:center; margin-top:7px;"></i>
                      </div>

                      <div class="comment_body">
                        <p>' . $row['comment_text'] . '</p>
                      </div>

                      <div class="comment_toolbar">
                        <div class="comment_details">
                          <ul>
                            <li>
                              <div class="commentshowrate">
                                <label style="'.($row['rate']>=5 ? 'color:#ffc700;' : '').'">5 stars</label>
                                <label style="'.($row['rate']>=4 ? 'color:#ffc700;' : '').'">4 stars</label>
                                <label style="'.($row['rate']>=3 ? 'color:#ffc700;' : '').'">3 stars</label>
                                <label style="'.($row['rate']>=2 ? 'color:#ffc700;' : '').'">2 stars</label>
                                <label style="'.($row['rate']>=1 ? 'color:#ffc700;' : '').'">1 star</label>
                              </div>
                            </li>
                            <li><i class="far fa-clock"></i> ' . $row['_time'] . '</li>
                            <li><i class="fa fa-calendar"></i> ' . $row['_date'] . '</li>
                            <li><i class="fa fa-pencil"></i> <span class="user">' . $row['surname'] . ' ' . $row['name'] . '</span></li>
                            <li><form name="deleteComment' . $row['id_comment'] . '" method="post"><a name="deleteCom" style="text-decoration: none;" onclick="deleteComment' . $row['id_comment'] . '.submit()"><i class="far fa-times-circle" style="color:red;"></i><p style="float:right; margin-left:3px; color:red; margin-top : -1px">remove comment</p></a><input type="hidden" name="comment_id" value="' . $row['id_comment'] . '"></form></li>
                          </ul>
                        </div>
                      </div>

                    </li>';
                  } else if($row['id_client'] == $_COOKIE['id'] || $row['id_client'] == $_SESSION['id']) {
                    echo '<li>
                      <div id="profil_img_holder' . $row['id_client'] . '" class="profil_img">
                        <i id="defaultIcon' . $row['id_client'] . '" class="fas fa-user fa-3x" style="text-align:center; margin-top:7px;"></i>
                      </div>

                      <div class="comment_body">
                        <p>' . $row['comment_text'] . '</p>
                      </div>

                      <div class="comment_toolbar">
                        <div class="comment_details">
                          <ul>
                            <li>
                              <div class="commentshowrate">
                                <label style="'.($row['rate']>=5 ? 'color:#ffc700;' : '').'">5 stars</label>
                                <label style="'.($row['rate']>=4 ? 'color:#ffc700;' : '').'">4 stars</label>
                                <label style="'.($row['rate']>=3 ? 'color:#ffc700;' : '').'">3 stars</label>
                                <label style="'.($row['rate']>=2 ? 'color:#ffc700;' : '').'">2 stars</label>
                                <label style="'.($row['rate']>=1 ? 'color:#ffc700;' : '').'">1 star</label>
                              </div>
                            </li>
                            <li><i class="far fa-clock"></i> ' . $row['_time'] . '</li>
                            <li><i class="fa fa-calendar"></i> ' . $row['_date'] . '</li>
                            <li><i class="fa fa-pencil"></i> <span class="user">' . $row['surname'] . ' ' . $row['name'] . '</span></li>
                            <li><form name="deleteComment' . $row['id_comment'] . '" method="post"><a name="deleteCom" style="text-decoration: none;" onclick="deleteComment' . $row['id_comment'] . '.submit()"><i class="far fa-times-circle" style="color:white;"></i><p style="float:right; margin-left:3px; color:white; margin-top: -1px;">remove comment</p></a><input type="hidden" name="comment_id" value="' . $row['id_comment'] . '"><input type="hidden" name="com_client_id" value="' . $row['id_client'] . '"></form></li>
                          </ul>
                        </div>
                      </div>

                    </li>';
                  } else {
                    echo '<li>
                      <div id="profil_img_holder' . $row['id_client'] . '" class="profil_img">
                        <i id="defaultIcon' . $row['id_client'] . '" class="fas fa-user fa-3x" style="text-align:center; margin-top:7px;"></i>
                      </div>

                      <div class="comment_body">
                        <p>' . $row['comment_text'] . '</p>
                      </div>

                      <div class="comment_toolbar">
                        <div class="comment_details">
                          <ul>
                            <li>
                              <div class="commentshowrate">
                                <label style="'.($row['rate']>=5 ? 'color:#ffc700;' : '').'">5 stars</label>
                                <label style="'.($row['rate']>=4 ? 'color:#ffc700;' : '').'">4 stars</label>
                                <label style="'.($row['rate']>=3 ? 'color:#ffc700;' : '').'">3 stars</label>
                                <label style="'.($row['rate']>=2 ? 'color:#ffc700;' : '').'">2 stars</label>
                                <label style="'.($row['rate']>=1 ? 'color:#ffc700;' : '').'">1 star</label>
                              </div>
                            </li>
                            <li><i class="far fa-clock"></i> ' . $row['_time'] . '</li>
                            <li><i class="fa fa-calendar"></i> ' . $row['_date'] . '</li>
                            <li><i class="fa fa-pencil"></i> <span class="user">' . $row['surname'] . ' ' . $row['name'] . '</span></li>
                          </ul>
                        </div>
                      </div>

                    </li>';
                  }

                  if(isset($row['profil_img'])) {
                    echo '<style>
                        #profil_img_holder' . $row['id_client'] . ' {
                          background-image: url("'.$row['profil_img'].'")
                        } 
                        #defaultIcon' . $row['id_client'] . ' {
                          display:none;
                        }
                      </style>';
                  }
                  
                }

              ?>
              <!--
              <li>

                <div class="user_avatar">
                  <i class="fas fa-user fa-4x"></i> change if no pic
                  <img src="avatar">
                </div>

                <div class="comment_body">
                  <p>text</p>
                </div>

                <div class="comment_toolbar">
                  <div class="comment_details">
                    <ul>
                      <li><i class="far fa-clock"></i> time</li>
                      <li><i class="fa fa-calendar"></i> date</li>
                      <li><i class="fa fa-pencil"></i> <span class="user">surname name</span></li>
                    </ul>
                  </div>
                </div>

              </li>
              -->
            </ul>

           </div>

         </div>
          
        </div>
      </div>

      <section class="section section-dark" style="margin-top: 40px;bottom: 0; padding: 20px 40px;">
        <input type="submit" name="top_button" value="<?php echo $nav['topBtn']; ?>" onclick='window.scrollTo({top: 0, behavior: "smooth"});' style="margin-top: 0px;">
        <br>
        <br>
        <p>&copy Barthélemy Quentin 6B - Travail de fin d'études - Saint-Jean Berchmans 2020-2021</p>
      </section>

     <?php else: ?>
  		<h1 style="text-align: center; line-height: 40px; font-size: 40px; right: 35%; left: 35%; top: 45%; bottom: 45%; color: #fff; position: absolute;
      top: 50%;"><?php echo $nav['404']; ?></h1>
  	 <?php endif; ?>

   </main>
				
  </body>
</html>
