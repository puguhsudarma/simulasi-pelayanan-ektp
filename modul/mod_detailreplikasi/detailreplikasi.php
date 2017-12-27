<?php
$Connection = MysqlConnectionOpen();
//ambil nilai get
	$id = isset($_GET['id']) ? $_GET['id'] : "";
//get all data simulasi	
	$data_simulasi = array();
	$sql = "SELECT * FROM hasil_simulasi WHERE id_replikasi = ".$id.";";
	$query = mysqli_query($Connection, $sql);
	while($row = mysqli_fetch_array($query, MYSQLI_NUM)){
		$data_simulasi[] = $row;
	}
	mysqli_free_result($query);
//get all data replikasi
	$data_replikasi = array();
	$sql = "SELECT * FROM replikasi ORDER BY id_replikasi DESC";
	$query = mysqli_query($Connection, $sql);
	while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
		$data_replikasi[] = $row;
	}
	mysqli_free_result($query);
//get detail replikasi
	$sql = "SELECT * FROM replikasi WHERE id_replikasi = ".$id." LIMIT 1;";
	$query = mysqli_query($Connection, $sql);
	$row = mysqli_fetch_array($query, MYSQLI_NUM);
	$data_detailreplikasi = $row;
	mysqli_free_result($query);
MysqlConnectionClose($Connection);
?>
<!--
	FORM ==================================================
-->
<form>
<div class='row'>
<div class='col-sm-3 col-md-3'>
	<div class='panel panel-default'>
		<div class='panel-heading'>Setting Simulasi</div>
		<div class='panel-body'>
			<div class='form-group'>
				<label for='LamaPelayananPerhari'>Lama Pelayanan Perhari</label>
				<div class='input-group'>
					<input type='text' class='form-control' name='LamaPelayananPerhari' id='LamaPelayananPerhari' placeholder='Lama Pelayanan' value='<?php echo $data_detailreplikasi[2]; ?>' readonly='readonly' />
					<div class='input-group-addon'>Jam</div>
				</div>
			</div>
			<div class='form-group'>
				<label for='LamaSimulasi'>Lama Simulasi</label>
				<div class='input-group'>
					<input type='text' class='form-control' name='LamaSimulasi' id='LamaSimulasi' placeholder='Lama Simulasi' value='<?php echo $data_detailreplikasi[3]; ?>' readonly='readonly' />
					<div class='input-group-addon'>Hari</div>
				</div>
			</div>
			<div class='form-group'>
				<label for='JumlahLoket'>Jumlah Loket</label>
				<div class='input-group'>
					<input type='text' class='form-control' name='JumlahLoket' id='JumlahLoket' placeholder='Jumlah Loket' value='<?php echo $data_detailreplikasi[4]; ?>' readonly='readonly' />
					<div class='input-group-addon'>Hari</div>
				</div>
			</div>
			<div class='form-group'>
				<a href='<?php echo base_url("index.php?page=prosessimulasi")?>' class='btn btn-primary'>Kembali</a>
			</div>
		</div>
	</div>
</div>
	
<div class='col-sm-4 col-md-4'>
	<div class='panel panel-default'>
		<div class='panel-heading'>Setting Parameter LCG</div>
		<div class='panel-body'>
			<div class='row'>
				<div class='col-sm-4 col-md-4'>
					<div class='form-group'>
						<label for='a1'>A[1]</label>
						<input type='text' class='form-control' name='a1' id='a1' placeholder='A[1]' value='<?php echo $data_detailreplikasi[5]; ?>' readonly='readonly' />
					</div>

					<div class='form-group'>
						<label for='c1'>C[1]</label>
						<input type='text' class='form-control' name='c1' id='c1' placeholder='C[1]' value='<?php echo $data_detailreplikasi[6]; ?>' readonly='readonly' />
					</div>

					<div class='form-group'>
						<label for='z10'>Z[1][0]</label>
						<input type='text' class='form-control' name='z10' id='z10' placeholder='Z[1][0]' value='<?php echo $data_detailreplikasi[7]; ?>' readonly='readonly' />
					</div>
				</div>
				<div class='col-sm-4 col-md-4'>
					<div class='form-group'>
						<label for='a2'>A[2]</label>
						<input type='text' class='form-control' name='a2' id='a2' placeholder='A[2]' value='<?php echo $data_detailreplikasi[8]; ?>' readonly='readonly' />
					</div>

					<div class='form-group'>
						<label for='c2'>C[2]</label>
						<input type='text' class='form-control' name='c2' id='c2' placeholder='C[2]' value='<?php echo $data_detailreplikasi[9]; ?>' readonly='readonly' />
					</div>

					<div class='form-group'>
						<label for='z20'>Z[2][0]</label>
						<input type='text' class='form-control' name='z20' id='z20' placeholder='Z[2][0]' value='<?php echo $data_detailreplikasi[10]; ?>' readonly='readonly' />
					</div>
				</div>
				<div class='col-sm-4 col-md-4'>
					<div class='form-group'>
						<label for='a3'>A[3]</label>
						<input type='text' class='form-control' name='a3' id='a3' placeholder='A[3]' value='<?php echo $data_detailreplikasi[11]; ?>' readonly='readonly' />
					</div>

					<div class='form-group'>
						<label for='c3'>C[3]</label>
						<input type='text' class='form-control' name='c3' id='c3' placeholder='C[3]' value='<?php echo $data_detailreplikasi[12]; ?>' readonly='readonly' />
					</div>

					<div class='form-group'>
						<label for='z30'>Z[3][0]</label>
						<input type='text' class='form-control' name='z30' id='z30' placeholder='Z[3][0]' value='<?php echo $data_detailreplikasi[13]; ?>' readonly='readonly' />
					</div>
				</div>
			</div>
			<div class='row'>
				<div class='col-sm-12 col-md-12'>
					<div class='form-group'>
						<label for='m'>M</label>
						<input type='text' class='form-control' name='m' id='m' placeholder='M' value='<?php echo $data_detailreplikasi[14]; ?>' readonly='readonly' />
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class='col-sm-3 col-md-3'>
	<div class='panel panel-default'>
		<div class='panel-heading'>Hasil Data Simulasi</div>
		<div class='panel-body'>
			<div class='form-group'>
				<label for='JumlahPelanggan'>Rata - Rata Jumlah Pelanggan</label>
				<div class='input-group'>
					<input type='text' class='form-control' name='JumlahPelanggan' id='JumlahPelanggan' value='<?php echo $data_detailreplikasi[15]; ?>' readonly='readonly' />
					<div class='input-group-addon'>Orang / Hari</div>
				</div>
			</div>

			<div class='form-group'>
				<label for='RataRataWaktuTunggu'>Rata - Rata Waktu Tunggu</label>
				<div class='input-group'>
					<input type='text' class='form-control' name='RataRataWaktuTunggu' id='RataRataWaktuTunggu' value='<?php echo $data_detailreplikasi[16]; ?>' readonly='readonly' />
					<div class='input-group-addon'>Menit / Orang</div>
				</div>
			</div>

			<div class='form-group'>
				<label for='RataRataWaktuPelayanan'>Rata - Rata Waktu Pelayanan</label>
				<div class='input-group'>
					<input type='text' class='form-control' name='RataRataWaktuPelayanan' id='RataRataWaktuPelayanan' value='<?php echo $data_detailreplikasi[17]; ?>' readonly='readonly' />
					<div class='input-group-addon'>Menit / Orang</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class='col-sm-2 col-md-2'>
	<div class='panel panel-default'>
		<div class='panel-heading'>Replikasi Simulasi</div>
		<div class='panel-body data-table'>
			<table class='table table-condensed table-hover'>
<?php
	foreach ($data_replikasi as $replikasi){
	echo "
			<tr><td><a href='".base_url("index.php?page=detailreplikasi&id=".$replikasi['id_replikasi'])."'>Replikasi ".$replikasi['replikasi_ke']."</a></td></tr>	
	";
	}
?>
			</table>
		</div>
	</div>
</div>
</div>
</form>
<!--
	DATA SIMULASI ==================================================
-->
<div class='row'>
<div class='col-sm-12 col-md-12 data-table'>
	<table class='table table-condensed table-hover table-bordered'>
		<tr>
			<th>Replikasi</th>
			<th>Hari ke-</th>
			<th>Warga ke-</th>
			<th>Waktu Kedatangan</th>
			<th>Antar Kedatangan</th>
			<th>Tunggu 1</th>
			<th>Loket Ke</th>
			<th>Awal Proses 1</th>
			<th>Lama Proses 1</th>
			<th>Selesai proses 1</th>
			<th>Tunggu 2</th>
			<th>Awal Proses 2</th>
			<th>Lama Proses 2</th>
			<th>Selesai Proses 2</th>
			<th>Total Waktu Pelayanan</th>
			<th>Total Waktu Tunggu</th>
		</tr>
<?php
	foreach ($data_simulasi as $Simulasi) {
	echo "
		<tr>
			<td>".$Simulasi[1]."</td>
			<td>".$Simulasi[2]."</td>
			<td>".$Simulasi[3]."</td>
			<td>".$Simulasi[4]."</td>
			<td>".$Simulasi[5]."</td>
			<td>".$Simulasi[6]."</td>
			<td>".$Simulasi[7]."</td>
			<td>".$Simulasi[8]."</td>
			<td>".$Simulasi[9]."</td>
			<td>".$Simulasi[10]."</td>
			<td>".$Simulasi[11]."</td>
			<td>".$Simulasi[12]."</td>
			<td>".$Simulasi[13]."</td>
			<td>".$Simulasi[14]."</td>
			<td>".$Simulasi[15]."</td>
			<td>".$Simulasi[16]."</td>
		</tr>
	";
	}
?>
	</table>
</div>
</div>