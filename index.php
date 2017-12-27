<?php
include "config/library_config.php";
include "page/exclude.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php printf(title()); ?></title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel='stylesheet' type='text/css' />
	<link href="assets/style/main.css" rel="stylesheet" type="text/css" />
    <noscript><meta http-equiv="refresh" content="0; URL=page/js.html" /></noscript>
</head>
<!--
	Body Program //============================================
-->
<body>
<?php menu(); ?>
<div id="container">
	<h1><?php printf(title()); ?></h1>
	<div id="body"><?php content(); ?></div>
	<p class="footer">
		Created By <strong>Kelompok 2</strong>. All Rights Reserved. 2015
	</p>
</div>
<script src="assets/js/jquery-1.8.2.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>
<!--
	Body Program //============================================
-->
</html>