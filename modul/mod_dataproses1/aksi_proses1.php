<?php
include "../../config/library_config.php";
$Connection = MysqlConnectionOpen();
switch($_GET['act']){
	case 'tambah':
		$input = array(
			$_POST['Proses1'],
			$_POST['Probabilitas'],
			$_POST['ProbabilitasKumulatif'],
			$_POST['BatasBawah'],
			$_POST['BatasAtas']
			);
		$sql = "INSERT INTO p_proses_1 VALUES(
													NULL,
													".$input[0].",
													".$input[1].",
													".$input[2].",
													".$input[3].",
													".$input[4]."
													);";
		$query = mysqli_query($Connection, $sql);
		break;
	case 'update':
		$input = array(
			$_POST['ID'],
			$_POST['Proses1'],
			$_POST['Probabilitas'],
			$_POST['ProbabilitasKumulatif'],
			$_POST['BatasBawah'],
			$_POST['BatasAtas']
			);
		$sql = "UPDATE p_proses_1 SET 	proses_1 = ".$input[1].",
												probabilitas = ".$input[2].",
												probabilitas_kumulatif = ".$input[3].",
												batas_bawah = ".$input[4].",
												batas_atas = ".$input[5]."
										WHERE 	id = ".$input[0]."
										LIMIT 1;
										";
		$query = mysqli_query($Connection, $sql); //or die(mysqli_error($Connection));
		break;
	case 'delete':
		$id = $_GET['id'];
		$sql = "DELETE FROM p_proses_1 WHERE id = ".$id." LIMIT 1;";
		$query = mysqli_query($Connection, $sql);
		break;
}
MysqlConnectionClose($Connection);
redirect(base_url("index.php?page=dataproses1"));
?>
