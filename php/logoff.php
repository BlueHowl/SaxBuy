<?php
  	session_start();
  	session_destroy();

  	unset($_COOKIE['id']);
  	unset($_COOKIE['mail']);
  	unset($_COOKIE['name']);
  	unset($_COOKIE['surname']);
  	unset($_COOKIE['isAdmin']);

  	setcookie('id', '', -1, '/');
	setcookie('mail', '', -1, '/');
	setcookie('name', '', -1, '/');
	setcookie('surname', '', -1, '/');
	setcookie('isAdmin', '', -1, '/');

  	header('location: http://bartque.alwaysdata.net');
  ﻿	//exit;
?>