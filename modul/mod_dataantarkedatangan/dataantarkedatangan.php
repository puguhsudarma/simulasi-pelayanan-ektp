<?php
$Connection = MysqlConnectionOpen();
$action = isset($_GET['act']) ? $_GET['act'] : "";
//get all data probabilitas
	$data_probabilitas = array();
	$sql = "SELECT * FROM p_antar_kedatangan";
	$query = mysqli_query($Connection, $sql);
	while($row = mysqli_fetch_array($query, MYSQLI_NUM)){
		$data_probabilitas[] = $row;
	}
	mysqli_free_result($query);
//get data update
	if($action == "update"){
		$id = $_GET['id'];
		$sql = "SELECT * FROM p_antar_kedatangan WHERE id=".$id." LIMIT 1;";
		$query = mysqli_query($Connection, $sql);
		while($row = mysqli_fetch_array($query, MYSQLI_NUM)){
			$dataDetail = $row;
		}
		mysqli_free_result($query);
	}
MysqlConnectionClose($Connection);
?>

<div class='row'>
	<div class='col-sm-3 col-md-3'>
<?php
	if($action == "update"){
	echo "
		<div class='panel panel-default'>
			<div class='panel-heading'>Update Data</div>
			<div class='panel-body'>
				<form action='".base_url("modul/mod_dataantarkedatangan/aksi_dataantarkedatangan.php?act=update")."' method='POST'>
					<div class='form-group'>
						<label for='ID'>ID</label>
						<input type='text' class='form-control' id='ID' name='ID' value='".$dataDetail[0]."' readonly='readonly' />
					</div>
					<div class='form-group'>
						<label for='AntarKedatangan'>Antar Kedatangan</label>
						<input type='text' class='form-control' id='AntarKedatangan' name='AntarKedatangan' value='".$dataDetail[1]."' />
					</div>

					<div class='form-group'>
						<label for='Probabilitas'>Probabilitas</label>
						<input type='text' class='form-control' id='Probabilitas' name='Probabilitas' value='".$dataDetail[2]."' />
					</div>

					<div class='form-group'>
						<label for='ProbabilitasKumulatif'>Probabilitas Kumulatif</label>
						<input type='text' class='form-control' id='ProbabilitasKumulatif' name='ProbabilitasKumulatif' value='".$dataDetail[3]."' />
					</div>

					<div class='form-group'>
						<label for='BatasBawah'>Batas Bawah</label>
						<input type='text' class='form-control' id='BatasBawah' name='BatasBawah' value='".$dataDetail[4]."' />
					</div>

					<div class='form-group'>
						<label for='BatasAtas'>Batas Atas</label>
						<input type='text' class='form-control' id='BatasAtas' name='BatasAtas' value='".$dataDetail[5]."' />
					</div>

					<div class='form-group'>
						<input type='submit' class='btn btn-primary' name='Submit' value='OK' />
						&nbsp;
						<input type='reset' class='btn btn-default' name='Reset' value='Reset' />
						&nbsp;
						<a href='".base_url("index.php?page=dataantarkedatangan")."' class='btn btn-default'>Kembali</a>
					</div>
				</form>
			</div>
		</div>
	";
	} else {
	echo "
		<div class='panel panel-default'>
			<div class='panel-heading'>Tambah Data</div>
			<div class='panel-body'>
				<form action='".base_url("modul/mod_dataantarkedatangan/aksi_dataantarkedatangan.php?act=tambah")."' method='POST'>
					<div class='form-group'>
						<label for='AntarKedatangan'>Antar Kedatangan</label>
						<input type='text' class='form-control' id='AntarKedatangan' name='AntarKedatangan' />
					</div>

					<div class='form-group'>
						<label for='Probabilitas'>Probabilitas</label>
						<input type='text' class='form-control' id='Probabilitas' name='Probabilitas' />
					</div>

					<div class='form-group'>
						<label for='ProbabilitasKumulatif'>Probabilitas Kumulatif</label>
						<input type='text' class='form-control' id='ProbabilitasKumulatif' name='ProbabilitasKumulatif' />
					</div>

					<div class='form-group'>
						<label for='BatasBawah'>Batas Bawah</label>
						<input type='text' class='form-control' id='BatasBawah' name='BatasBawah' />
					</div>

					<div class='form-group'>
						<label for='BatasAtas'>Batas Atas</label>
						<input type='text' class='form-control' id='BatasAtas' name='BatasAtas' />
					</div>

					<div class='form-group'>
						<input type='submit' class='btn btn-primary' name='Submit' value='OK' />
						&nbsp;
						<input type='reset' class='btn btn-default' name='Reset' value='Reset' /> 
					</div>
				</form>
			</div>
		</div>
	";
	}
?>
	</div>
	<div class='col-sm-9 col-md-9'>
		<table class='table table-condensed table-bordered'>
			<tr>
				<th>ID</th>
				<th>Antar Kedatangan</th>
				<th>Probabilitas</th>
				<th>Probabilitas Kumulatif</th>
				<th>Batas Bawah</th>
				<th>Batas Atas</th>
				<th colspan='2'>Aksi</th>
			</tr>
<?php
	foreach($data_probabilitas as $data){
		echo "
			<tr>
				<td>".$data[0]."</td>
				<td>".$data[1]."</td>
				<td>".$data[2]."</td>
				<td>".number_format($data[3],3)."</td>
				<td>".number_format($data[4],3)."</td>
				<td>".number_format($data[5],3)."</td>
				<td align='center'>
					<a href='".base_url("index.php?page=dataantarkedatangan&act=update&id=".$data[0])."'>
						<span class='glyphicon glyphicon-edit'></span>
						<span class='sr-only'>Update</span>
					</a>
				</td>
				<td align='center'>
					<a href='".base_url("modul/mod_dataantarkedatangan/aksi_dataantarkedatangan.php?act=delete&id=".$data[0])."' onclick='return confirm(\"Hapus Data ?\");'>
						<span class='glyphicon glyphicon-trash'></span>
						<span class='sr-only'>Delete</span>
					</a>
				</td>
			</tr>
		";
	}
?>
		</table>
	</div>
</div>












