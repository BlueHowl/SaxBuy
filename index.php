<!--
 ________  ________     ___    ___ ________  ___  ___      ___    ___ 
|\   ____\|\   __  \   |\  \  /  /|\   __  \|\  \|\  \    |\  \  /  /|
\ \  \___|\ \  \|\  \  \ \  \/  / | \  \|\ /\ \  \\\  \   \ \  \/  / /
 \ \_____  \ \   __  \  \ \    / / \ \   __  \ \  \\\  \   \ \    / / 
  \|____|\  \ \  \ \  \  /     \/   \ \  \|\  \ \  \\\  \   \/  /  /  
    ____\_\  \ \__\ \__\/  /\   \    \ \_______\ \_______\__/  / /    
   |\_________\|__|\|__/__/ /\ __\    \|_______|\|_______|\___/ /     
   \|_________|        |__|/ \|__|                       \|___|/      
                                                                      
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@                                                                      
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ ,,@@@@@@@@@
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@,,,,,,,,,%@@@@@
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@,,,,@@@@@
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@,,,,,*@@@@@
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@,,,,,*/@@@@@@@
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@,,,,,**@@@@@@@@@@
@@@@@@@@@@@@@@@@@@@@@@@@@%@@@@,,,,,,**@@@@@@@@@@@@
@@@@@@@@@@@@@@@@@@@@@#,,@@.,,,,,,**@,,,,@@@@@@@@@@
@@@@@@@@@@@@@@@@@@@@@@@(,,,,,,**/(,.@,@@@@@@@@@@@@
@@@@@@@@@@@@@@@@@@@@@,,,,,,,**@@@@@@@@@@@@@@@@@@@@
@@@@@@@@@@@@@@@@@@,,,,/(,***@@@@@@@@@@@@@@@@@@@@@@
@@@@@@@@@@@@@@@*,,,,(/***@@@@@@@@,@@@@@@@@@@@@@@@@
@@@@@@@@@@@@,,,,,,/,**/@@@@@@(,,,,,@@@@@@@@@@@@@@@
@@@@@@@@@(,,,,,,,***@@@@&,,,,,,,,,,@@@@@@@@@@@@@@@
@@@@@@@,,,,,,,****@@@,,,,,,,,,,,,,,@@@@@@@@@@@@@@@
@@@@@@,,,,,,,,*(@@@,,,,,,,,,,,,,,,,@@@@@@@@@@@@@@@
@@@@@(,,,,,,,,,,,,,,,,,,,,,,,,,,,,,@@@@@@@@@@@@@@@
@@@@@@,,,,,,,,,,,,,,,,,,***********@@@@@@@@@@@@@@@
@@@@@@@,,,,,,,,,,,,,,***@@@@@@@@***@@@@@@@@@@@@@@@
@@@@@@@@@***,,,******(@@@@@@@@@@@@@@@@@@@@@@@@@@@@
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

Website made by Quentin Barthélemy
-->

<?php
  session_start();

  include('./php/connexionDB.php');

$req = $DB->query("SELECT id_article, img_url, brand, model, price, AVG(rate) AS averageRate FROM articles LEFT JOIN comments ON articles.id_article = comments.fk_article GROUP BY id_article ORDER BY RAND() LIMIT 6");

  $langId = "en";

  $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $url_components = parse_url($actual_link);
  parse_str($url_components['query'], $params);

  if(isset($params['lang'])) {
    $langId = $params['lang'];
  }

  $flang = fopen("./languages/" . $langId . ".lang", "r");
  $flang = fread($flang,filesize("./languages/" . $langId . ".lang"));

  $pageTxt = explode(",*-", $flang);
  parse_str(preg_replace("/\r|\n/", "", $pageTxt[0] . $pageTxt[1]), $nav);

?>

<!DOCTYPE html>
<html lang="<?php echo $langId; ?>">
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<meta http-equiv="X-UA-Compatible" content="ie=edge">  	
		<link rel="stylesheet" href="css/styles.css"/>
		<link rel="icon" href="img/favicon.png">

		<title>SaxBuy</title>

    <script src="./js/konami.js"></script>

    <script>
      var easter_egg = new Konami(function() { 
        alert("Take Five, You found the super secret audio");

        var audio = new Audio('./audio/take_five.mp3');
        audio.play();
        audio.addEventListener('loadeddata', () => {
          let x = audio.duration * 1000;
          document.getElementById("drum").innerHTML = '<img src="./img/drums_gif.gif" style="bottom: 0; right: 20px; position: fixed; z-index:999; width: 400px;">';

          setTimeout(
            function () {
              document.getElementById("piano").innerHTML = '<img src="./img/piano_gif.gif" style="bottom: 0; left: 35%; position: fixed; z-index:999; width: 400px;">';
            }, 7000
          );

          setTimeout(
            function () {
              document.getElementById("sax").innerHTML = '<img src="./img/sax_gif.gif" style="bottom: 0;position: fixed; z-index:999;"><img src="./img/notes_gif.gif" style="bottom: 280px;position: fixed;z-index:999;left: 120px;">';
            }, 20500
          );

          setTimeout(
            function () {
              document.getElementById("sax").innerHTML = '';
            }, 109000
          );

          setTimeout(
            function () {
              document.getElementById("sax").innerHTML = '<img src="./img/sax_gif.gif" style="bottom: 0;position: fixed; z-index:999;"><img src="./img/notes_gif.gif" style="bottom: 280px;position: fixed;z-index:999;left: 120px;">';
            }, 420000
          );

          setTimeout(
            function () {
              document.getElementById("sax").innerHTML = '<img src="./img/sax_gif.gif" style="bottom: 0;position: fixed; z-index:999;"><img src="./img/notes_gif.gif" style="bottom: 280px;position: fixed;z-index:999;left: 120px;">';
            }, (x - 4000)
          );

          setTimeout(
            function () {
              document.getElementById('secret_gif').style.display='none';
            }, x
          );

        })
      });
    </script>

	</head>
	
<body>

  <div id="super_secret_container">
    <div id="secret_gif">
      <div id="drum">
      
      </div>

      <div id="piano">
      
      </div>

      <div id="sax">
      
      </div>
    </div>
  </div> 	
	<header>
		<nav class="navBar">

      <div>

        <link rel="stylesheet" href="css/styles-language_selector.css"/>
        
        <link rel="stylesheet "type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.3.0/css/flag-icon.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.3.0/css/flag-icon.min.css">

        <script>
          $(function(){
            $('.selectpicker').selectpicker();

            $('.selectpicker').change(function (e) {
              localStorage.setItem("lang", e.target.value);
              window.location.replace(window.location.href.replace("?lang=" + getParameterByName("lang"), "").replace("&lang=" + getParameterByName("lang"), ""));
            });

          });

          var lang = localStorage.getItem("lang");
          if(lang && lang != "null" && window.location.href.search("[?&]lang=") == -1) {
            var currentQueryString = window.location.search;
            if (currentQueryString) {
              window.location.replace(window.location.href + "&lang=" + lang);
            } else {
              window.location.replace(window.location.href + "?lang=" + lang);
            }
          }

          function getParameterByName(name, url = window.location.href) {
            name = name.replace(/[\[\]]/g, '\\$&');
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
          }

        </script>

        <div class="custom-select">
          <select class="selectpicker show-menu-arrow" data-width="fit" data-style="btn-primary-i">
            <option data-content='<span class="flag-icon flag-icon-gb"></span> English' value="en" <?php if($langId == "en") {echo "selected";} ?>> English</option>
            <option  data-content='<span class="flag-icon flag-icon-fr"></span> Français' value="fr" <?php if($langId == "fr") {echo "selected";} ?>> Français</option>
          </select>
        </div>
      </div>

			<ul class="menu">	
				<li style="padding-top: 5px; flex: auto;"><a id="pic" href="../index" style="text-decoration: none;"><img id="logo" src="img/title.png" alt="Saxbuy" width="235" height="80"/></a></li>

        <li><a href=""><?php echo $nav['li0']; ?></a></li>
        <li><a href="./php/shop?filter=*"><?php echo $nav['li1']; ?></a></li>
        
        <?php if (!isset($_COOKIE['id']) and !isset($_SESSION['id'])): ?>
          <li><a href="./php/login"><?php echo $nav['li2']; ?></a></li>  
          <li><a href="./php/logon"><?php echo $nav['li3']; ?></a></li>        
        <?php else: ?>
          <li><a href="./php/cart"><?php echo $nav['li4']; ?></a></li>
          <li><a href="./php/account"><?php echo $nav['li5']; ?></a></li>

          <?php if ($_COOKIE['isAdmin'] == true or $_SESSION['isAdmin'] == true): ?>
            <li><a href="./php/adminPanel"><?php echo $nav['li6']; ?></a></li>
          <?php else: ?>
          <?php endif; ?>

          <li><a onclick="disconnect()" style="cursor: pointer;"><?php echo $nav['li7']; ?></a></li>
        <?php endif; ?>

			</ul>

      <script>
        function disconnect() {
          var r = confirm("<?php echo $nav['disconnetMsg']; ?>");

          if(r) {
            window.location.href = "https://bartque.alwaysdata.net/php/logoff";
          }
        }
      </script>

		</nav>
	</header>
	
    <div class="pimg1">
      <div class="ptext">
        <span class="border trans">
        	<p>
			<br>
			<br>
			<br>
        	<?php echo $nav['p0']; ?>
        	<br>
        	<br>
        	<?php echo $nav['p1']; ?>
        	<br>
        	<input type="submit" name="shop_button" value="<?php echo $nav['shopBtn']; ?>" onClick="location.href = './php/shop?filter=*'">
        	<br>
        	<br>
        	<br>
        	</p>
        	
        </span>       

      </div>
    </div>

    <section class="section section-dark">
      <h2><?php echo $nav['highlightTitle']; ?></h2>
    </section>

    <div class="pimg2">
      <div class="ptext" id="highlight" style="width: 100%;">
        <span class="border trans">
          
          <div class="shopping-cart" style="display: flex; flex-wrap: wrap; padding-top: 20px; padding-left: 20px;">
              <?php
                foreach ($req as $row) {
                  echo '<div class="product-card" style="flex-basis: 31.5%;margin: 0.4%;background-color: rgba(40, 46, 52, 0.5);border-radius: 6px;filter: drop-shadow(2px 4px 6px black); overflow:hidden;display: flex;align-items: center;">

                          <a class="_card-href" href="https://bartque.alwaysdata.net/php/product_detail?product=' . $row['id_article'] . '" style="text-decoration:none;">
                            <div class="product-image" style="text-align:center; filter: drop-shadow(2px 4px 6px black);">
                                <img src="' . $row['img_url'] . '" style="width:70%;">
                            </div>
                            <div class="product-info">
                                <h3 style="font-size: 24px;color: #fff;padding-left:20px;">' . $row['brand'] . ' ' . $row['model'] . '</h3>
                                <div style="display:flex; justify-content:space-between; flex-wrap:wrap;">
                                  <h4 style="font-size: 20px;color: #FFD732;padding-left:20px;padding-bottom:10px;">' . number_format($row['price'], 2, ',', ' ') . ' €</h4>
                                  '.(isset($row['averageRate'])? '
                                    <style>
                                      .shopshowrate:not(:checked) > input {
                                        position:absolute;
                                        display: none;
                                      }
                                      .shopshowrate:not(:checked) > label {
                                        float:right;
                                        width:1em;
                                        overflow:hidden;
                                        white-space:nowrap;
                                        font-size:20px;
                                        color:#ccc;
                                      }
                                      .shopshowrate:not(:checked) > label:before {
                                        content: "★";
                                      }
                                    </style>
                                    <div class="shopshowrate" style="padding: 0px 10px;">
                                      <label style="'.($row['averageRate']>=5 ? 'color:#ffc700;' : '').'"></label>
                                      <label style="'.($row['averageRate']>=4 ? 'color:#ffc700;' : '').'"></label>
                                      <label style="'.($row['averageRate']>=3 ? 'color:#ffc700;' : '').'"></label>
                                      <label style="'.($row['averageRate']>=2 ? 'color:#ffc700;' : '').'"></label>
                                      <label style="'.($row['averageRate']>=1 ? 'color:#ffc700;' : '').'"></label>
                                    </div>' : '').'
                                </div>
                            </div>
                            <div class="content-details_product">
                                    <h4>View <br>details</h4>
                                  </div>
                          </a>
                      </div>';
                }
              ?>
          </div>

        </span>

      </div>
    </div>

    <section class="section section-dark">
      <h2 style="font-size: 36px;"><?php echo $nav['partnerTitle']; ?></h2>    	
    </section>

    <div class="pimg3">
      <div class="ptext">
        <span class="border trans">
          	<p><?php echo $nav['p5']; ?></p>
          	<br>
          	<br>
        	<div class="grid">
  				<div class="cell">
  					<div class="content">
             			<a href="https://usa.yamaha.com/products/musical_instruments/winds/saxophones/index.html" target="_blank">
               				<div class="content-overlay"></div>
               				<img class="content-image" src="./img/brand-logos/yamaha.png">
               				<div class="content-details fadeIn-left">
                 				<h4><?php echo $nav['partnerSlideText']; ?></h4>
                 				<p>==></p>
               				</div>
             			</a>
           			</div>					
  				</div>
  				<div class="cell"">
  					<div class="content">
             			<a href="https://www.jupiter.info/en/saxophone-gesamtuebersicht.html" target="_blank">
               				<div class="content-overlay"></div>
               				<img class="content-image" src="./img/brand-logos/jupiter.png">
               				<div class="content-details fadeIn-left">
                 				<h4><?php echo $nav['partnerSlideText']; ?></h4>
                 				<p>==></p>
               				</div>
             			</a>
           			</div>
  				</div>
  				<div class="cell">
  					<div class="content">
             			<a href="https://vandoren.fr/en/" target="_blank">
               				<div class="content-overlay"></div>
               				<img class="content-image" src="./img/brand-logos/vandoren.png">
               				<div class="content-details fadeIn-left">
                 				<h4><?php echo $nav['partnerSlideText']; ?></h4>
                 				<p>==></p>
               				</div>
             			</a>
           			</div>
  				</div>
  				<div class="cell">
  					<div class="content">
             			<a href="https://www.julius-keilwerth.com/en/" target="_blank">
               				<div class="content-overlay"></div>
               				<img class="content-image" src="./img/brand-logos/keilwerth.png">
               				<div class="content-details fadeIn-left">
                 				<h4><?php echo $nav['partnerSlideText']; ?></h4>
                 				<p>==></p>
               				</div>
             			</a>
           			</div>
  				</div>
  				<div class="cell">
  					<div class="content">
             			<a href="https://www.selmer.fr/en/" target="_blank">
               				<div class="content-overlay"></div>
               				<img class="content-image" src="./img/brand-logos/selmer.png">
               				<div class="content-details fadeIn-left">
                 				<h4><?php echo $nav['partnerSlideText']; ?></h4>
                 				<p>==></p>
               				</div>
             			</a>
           			</div>
  				</div>
  				<div class="cell">
  					<div class="content">
             			<a style="cursor: pointer;">
               				<div class="content-overlay"></div>
               				<img class="content-image" src="./img/brand-logos/rico.png">
               				<div class="content-details fadeIn-left">
                 				<h4><?php echo $nav['partnerSlideText']; ?></h4>
               				</div>
             			</a>
           			</div>
  				</div>
  				<div class="cell">
  					<div class="content">
             			<a href="https://www.conn-selmer.com/en-us/our-brands/cg-conn" target="_blank">
               				<div class="content-overlay"></div>
               				<img class="content-image" src="./img/brand-logos/conn.png">
               				<div class="content-details fadeIn-left">
                 				<h4><?php echo $nav['partnerSlideText']; ?></h4>
                 				<p>==></p>
               				</div>
             			</a>
           			</div>
  				</div>
  				<div class="cell">
  					<div class="content">
             			<a href="https://yanagisawa.fr/fr/" target="_blank">
               				<div class="content-overlay"></div>
               				<img class="content-image" src="./img/brand-logos/yanagisawa.png">
               				<div class="content-details fadeIn-left">
                 				<h4><?php echo $nav['partnerSlideText']; ?></h4>
                 				<p>==></p>
               				</div>
             			</a>
           			</div>
  				</div>
  				<div class="cell" style="background-color: rgba(0,0,0,0)"></div>
			</div>  
        </span>

      </div>
    </div>

    <section class="section section-dark">
    	<input type="submit" name="top_button" value="<?php echo $nav['topBtn']; ?>" onclick='window.scrollTo({top: 0, behavior: "smooth"});' style="margin-top: 0px;">
    	<br>
    	<br>
      	<p>&copy Barthélemy Quentin 6B - Travail de fin d'études - Saint-Jean Berchmans 2020-2021</p>
    </section>   
  </body>
	
</html>
