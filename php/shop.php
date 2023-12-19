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
	parse_str(preg_replace("/\r|\n/", "", $pageTxt[0] . $pageTxt[8]), $nav);

	if(isset($params['filter']) && $params['filter'] != "*") {
		$filter = $params['filter'];
		$req = $DB->query("SELECT id_article, img_url, brand, model, price, AVG(rate) AS averageRate FROM articles LEFT JOIN comments ON articles.id_article = comments.fk_article WHERE object_type = :filter GROUP BY id_article",
			array('filter' => $filter));
	} else {
		$req = $DB->query("SELECT id_article, img_url, brand, model, price, AVG(rate) AS averageRate FROM articles LEFT JOIN comments ON articles.id_article = comments.fk_article GROUP BY id_article ORDER BY RAND()");
	}

	/*
	if(!empty($_POST)){
	    extract($_POST);

	    if (isset($_POST['search_form'])){
	      $strSearch = $_POST['search'];

	      $req = $DB->query("SELECT id_article, img_url, brand, model, price FROM articles WHERE brand LIKE '%?%' OR model LIKE '%?%'",
	  		array($strSearch));
	      $req_search = $req->fetch();
	      echo $strSearch . " test";

	      exit;
	    }
	}*/

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<meta http-equiv="X-UA-Compatible" content="ie=edge">  	
		<link rel="stylesheet" href="../css/styles.css"/>
		<link rel="stylesheet" href="../css/styles-shop.css"/>
		<link rel="icon" href="../img/favicon.png">
		<title><?php echo $nav['shopPageTitle']; ?></title>
	</head>

	<body>
		<?php include 'navMenu.php';?>

		<nav class="product-filter">

		  <div class="sort">
		  	<div class="collection-search">
		  		<form method="post" name="search_form">
		            <input id="searchbar" type="text" name="search" placeholder="<?php echo $nav['searchbar']; ?>" autocomplete="off">
		    	</form>

		    	<script type="text/javascript">
		    		var input = document.getElementById("searchbar");
					input.addEventListener("input", myFunction);

					function myFunction(e) {
					  var filter = e.target.value.toUpperCase();

					  var list = document.getElementById("product_list");
					  var divs = list.getElementsByClassName("product-card");

					  for (var i = 0; i < divs.length; i++) {
					    var strToSearch = divs[i].getElementsByTagName("h3")[0];

					    if (strToSearch) {
					      if (strToSearch.innerHTML.toUpperCase().indexOf(filter) > -1) {
					        divs[i].style.display = "";
					      } else {
					        divs[i].style.display = "none";
					      }
					    }
					  }

					}
		    	</script>
		  	</div>

		    <div class="collection-sort">
		      <label style="padding-left: 10px;"><?php echo $nav['filterTxt']; ?></label>
		      <select id="Filter" data-width="fit" class="selectpicker1 show-menu-arrow" data-style="btn-primary-i">
		        <option value="*"><?php echo $nav['filterL0']; ?></option>
		        <option value="0"><?php echo $nav['filterL1']; ?></option>
		        <option value="1"><?php echo $nav['filterL2']; ?></option>
		        <option value="2"><?php echo $nav['filterL3']; ?></option>
		        <option value="3"><?php echo $nav['filterL4']; ?></option>
		      </select>

		      <script>
		      	$(function(){
					$('.selectpicker1').selectpicker();
				});
		      </script>

		      <script type="text/javascript">
		      	function selectElement(id, valueToSelect) {    
				    let element = document.getElementById(id);
				    element.value = valueToSelect;
				}

		      	const queryString = window.location.search;
		      	const urlParams = new URLSearchParams(queryString);
		      	const _filterVal = urlParams.get('filter')

		      	const filter = document.getElementById('Filter');

		      	selectElement("Filter", _filterVal);

		      	filter.onchange = (event) => {
			    	var i = event.target.value;
			    	window.location.href = "https://bartque.alwaysdata.net/php/shop?filter=" + i;
			 	}
		      </script>
		    </div>

<!--
		    <div class="collection-sort">
		      <label>Sort by:</label>
		      <select>
		        <option value="/">Featured</option>
		      </select>
		    </div>
-->
		  </div>

		</nav>

		<section class="products" id="product_list">

			<?php 
				foreach ($req as $row) {
					echo '
					  	<div class="product-card">
					  		<a class="card-href" href="https://bartque.alwaysdata.net/php/product_detail?product=' . $row['id_article'] . '">
					    		<div class="product-image">
					      			<img src="' . $row['img_url'] . '">
					    		</div>
					    		<div class="product-info">
					      			<h3>' . $row['brand'] . ' ' . $row['model'] . '</h3>
					      			<div style="display:flex; justify-content:space-between; flex-wrap:wrap;">
						      			<h4>' . number_format($row['price'], 2, ',', ' ') . ' -€</h4>
						      			'.(isset($row['averageRate'])? '
						      			<div class="shopshowrate">
			                                <label style="'.($row['averageRate']>=5 ? 'color:#ffc700;' : '').'">5 stars</label>
			                                <label style="'.($row['averageRate']>=4 ? 'color:#ffc700;' : '').'">4 stars</label>
			                                <label style="'.($row['averageRate']>=3 ? 'color:#ffc700;' : '').'">3 stars</label>
			                                <label style="'.($row['averageRate']>=2 ? 'color:#ffc700;' : '').'">2 stars</label>
			                                <label style="'.($row['averageRate']>=1 ? 'color:#ffc700;' : '').'">1 star</label>
			                            </div>' : '').'
					      			</div>
					    		</div>
					    		<div class="content-details">
	                 				<h4>View <br>details</h4>
	               				</div>
					    	</a>
					 	</div>';
				}
			?>
		</section>

		<section class="section section-dark" style="margin-top: 20px;bottom: 0; padding: 20px 40px;">
	    	<input type="submit" name="top_button" value="<?php echo $nav['topBtn']; ?>" onclick='window.scrollTo({top: 0, behavior: "smooth"});' style="margin-top: 0px;">
	    	<br>
	    	<br>
	      	<p>&copy Barthélemy Quentin 6B - Travail de fin d'études - Saint-Jean Berchmans 2020-2021</p>
	    </section>

	</body>

</html>
