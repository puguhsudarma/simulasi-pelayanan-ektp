<?php
function title(){
	$title = isset($_GET['page']) ? $_GET['page'] : "";

	switch($title){
		case "dataantarkedatangan"	: $title = "Data Probabilitas Antar Kedatangan";		break;
		case "dataproses1"			: $title = "Data Probabilitas Proses 1 (Registrasi)";	break;
		case "dataproses2"			: $title = "Data Probabilitas Proses 2 (Record Data)";	break;
		case "prosessimulasi"		: $title = "Data Proses Simulasi";						break;
		case "detailreplikasi"		: $title = "Data Detail Replikasi Simulasi";			break;
		default 					: $title = "Program Simulasi Kelompok 2";				break;
	}

	return $title;
}

function content(){ 
	$mod = isset($_GET['page']) ? $_GET['page'] : "";

	switch($mod){
		case "" 					: include "modul/mod_home/home.php"; 								break;
		case "dataantarkedatangan"	: include "modul/mod_dataantarkedatangan/dataantarkedatangan.php"; 	break;
		case "dataproses1"			: include "modul/mod_dataproses1/dataproses1.php";					break;
		case "dataproses2"			: include "modul/mod_dataproses2/dataproses2.php";					break;
		case "prosessimulasi"		: include "modul/mod_prosessimulasi/prosessimulasi.php";			break;
		case "detailreplikasi"		: include "modul/mod_detailreplikasi/detailreplikasi.php";			break;
		default						: include "modul/mod_warning/error.php";							break;
	}
}

function menu(){
	echo "
	<nav class='navbar navbar-default navbar-fixed-top'>
		<div class='container-fluid'>
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class='navbar-header'>
				<button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#bs-example-navbar-collapse-1' aria-expanded='false'>
					<span class='sr-only'>Toggle navigation</span>
					<span class='icon-bar'></span>
					<span class='icon-bar'></span>
					<span class='icon-bar'></span>
				</button>
				<a class='navbar-brand' href='#'>PROGRAM SIMULASI</a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class='collapse navbar-collapse' id='bs-example-navbar-collapse-1'>
				<ul class='nav navbar-nav'>
					<li><a href='".base_url()."'><span class='glyphicon glyphicon-home'></span> Home</a></li>
					<li class='dropdown'>
						<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'><span class='glyphicon glyphicon-th-large'></span> Data Probabilitas <span class='caret'></span></a>
						<ul class='dropdown-menu'>
							<li><a href='".base_url('index.php?page=dataantarkedatangan')."'><span class='glyphicon glyphicon-tree-conifer'></span> Antar Kedatangan</a></li>
							<li><a href='".base_url('index.php?page=dataproses1')."'><span class='glyphicon glyphicon-tree-conifer'></span> Proses 1 (Registrasi)</a></li>
							<li><a href='".base_url('index.php?page=dataproses2')."'><span class='glyphicon glyphicon-list-alt'></span> Proses 2 (Record Data)</a></li>
						</ul>
					</li>
					<li><a href='".base_url('index.php?page=prosessimulasi')."'><span class='glyphicon glyphicon-th-large'></span> Data Proses Simulasi</a></li>
				</ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>
	";
}
?>