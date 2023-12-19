<header>
	<nav class="navBar">

    <div>

      <link rel="stylesheet" href="../css/styles-language_selector.css"/>

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
          <option data-content='<span class="flag-icon flag-icon-fr"></span> Français' value="fr" <?php if($langId == "fr") {echo "selected";} ?>> Français</option>
        </select>
      </div>
    </div>

    <ul class="menu">   
        <li style="padding-top: 5px; flex: auto;"><a id="pic" href="../index" style="text-decoration: none;"><img id="logo" src="../img/title.png" alt="Saxbuy" width="235" height="80"/></a></li>

        <li><a href="../index"><?php echo $nav['li0']; ?></a></li>
        <li><a href="shop?filter=*"><?php echo $nav['li1']; ?></a></li>

        <?php if (!isset($_COOKIE['id']) and !isset($_SESSION['id'])): ?>
          <li><a href="login"><?php echo $nav['li2']; ?></a></li>          
          <li><a href="logon"><?php echo $nav['li3']; ?></a></li>
        <?php else: ?>
          <li><a href="cart"><?php echo $nav['li4']; ?></a></li>
          <li><a href="account"><?php echo $nav['li5']; ?></a></li>

          <?php if ($_COOKIE['isAdmin'] == true or $_SESSION['isAdmin'] == true): ?>
            <li><a href="adminPanel"><?php echo $nav['li6']; ?></a></li>
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
