<?php
	error_reporting(E_ALL);
	define( 'DEBUG', 2 );

	session_start();

	// les libraries de DB et de formz
	require 'include/powa_include.php';

	echo '<?xml version="1.0" encoding="iso-8859-15"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" />
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
