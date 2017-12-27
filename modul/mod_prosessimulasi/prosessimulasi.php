<?php
	$Connection = MysqlConnectionOpen();
//get data simulasi	
	//Pagination data
	//var bantuan
	$perpage = 1000;
	$nopage  = isset($_GET['datapage']) ? intval($_GET['datapage']) : 1;
	$calc	 = $perpage*$nopage;
	$start 	 = $calc-$perpage;
	$data_simulasi = array();
	//Request data
	$sql = "SELECT * FROM `hasil_simulasi` LIMIT ".$start.", ".$perpage.";";
	$query = mysqli_query($Connection, $sql);
	$rowsdata = mysqli_num_rows($query);
	while($row = mysqli_fetch_array($query, MYSQLI_NUM)){
		$data_simulasi[] = $row;
	}
	mysqli_free_result($query);
	//Make link pagination
	if(isset($nopage)){
		$result = mysqli_query($Connection, "SELECT COUNT(*) AS `total` FROM `hasil_simulasi`;");
		$rowsdata = mysqli_num_rows($result);
		if($rowsdata){
			$rs = mysqli_fetch_assoc($result);
			$total = $rs['total'];
		}
		mysqli_free_result($result);
		$totalpages = ceil($total/$perpage);
		$links_tag = "";

		$tag_open = "<ul class='pagination'>";
		$links_tag .= $tag_open;
		for($i=1;$i<=$totalpages;$i++){
			if($i!=$nopage){
				$link = "<li><a href='".base_url("index.php?page=prosessimulasi&datapage=".$i)."'>".$i."</a></li>";
			} else {
				$link = "<li class='disabled'><a href='#'>".$i."</a></li>";
			}
			$links_tag .= $link;
		}
		$tag_close = "</ul>";
		$links_tag .= $tag_close;
	}
//get data replikasi
	$data_replikasi = array();
	$sql = "SELECT * FROM replikasi ORDER BY id_replikasi DESC";
	$query = mysqli_query($Connection, $sql);
	while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
		$data_replikasi[] = $row;
	}
	mysqli_free_result($query);
//get kesimpulan pass by session
	session_start();
	if($_SESSION){
		$KesimpulanSimulasi = array(
								"RataRataWarga" => $_SESSION['RataRataWarga'],
								"RataRataWaktuPelayanan" => $_SESSION['RataRataWaktuPelayanan'],
								"RataRataWaktuTunggu" => $_SESSION['RataRataWaktuTunggu']							
								);
		$Simulasi 			= array(
								"LamaPelayanan"	=> $_SESSION['LamaPelayanan'],
								"LamaSimulasi"	=> $_SESSION['LamaSimulasi'],
								"JumlahLoket"	=> $_SESSION['JumlahLoket']
								);
		unset($_SESSION);
	} else {
		$KesimpulanSimulasi = array(
								"RataRataWarga" => "",
								"RataRataWaktuPelayanan" => "",
								"RataRataWaktuTunggu" => ""				
								);
		$Simulasi 			= array(
								"LamaPelayanan"	=> "",
								"LamaSimulasi"	=> "",
								"JumlahLoket"	=> ""
								);
	}
	session_destroy();
	MysqlConnectionClose($Connection);
?>
<!--
	FORM ==================================================
-->
<form action='<?php echo base_url('modul/mod_prosessimulasi/aksi_prosessimulasi.php'); ?>' method='POST'>
<div class='row'>
<div class='col-sm-3 col-md-3'>
	<div class='panel panel-default'>
		<div class='panel-heading'>Setting Simulasi</div>
		<div class='panel-body'>
			<div class='form-group'>
				<label for='LamaPelayananPerhari'>Lama Pelayanan Perhari</label>
				<div class='input-group'>
					<input type='text' class='form-control' name='LamaPelayananPerhari' id='LamaPelayananPerhari' placeholder='Lama Pelayanan' value='<?php echo $Simulasi['LamaPelayanan']; ?>' />
					<div class='input-group-addon'>Jam</div>
				</div>
			</div>
			<div class='form-group'>
				<label for='LamaSimulasi'>Lama Simulasi</label>
				<div class='input-group'>
					<input type='text' class='form-control' name='LamaSimulasi' id='LamaSimulasi' placeholder='Lama Simulasi' value='<?php echo $Simulasi['LamaSimulasi']; ?>' />
					<div class='input-group-addon'>Hari</div>
				</div>
			</div>
			<div class='form-group'>
				<label for='JumlahLoket'>Jumlah Loket</label>
				<select class='form-control' id='JumlahLoket' name='JumlahLoket'>
<?php
	if($Simulasi['JumlahLoket'] == ""){
		echo "
			<option value='1' selected='selected'>1 Loket</option>
			<option value='2'>2 Loket</option>
		";
	} else {
		if($Simulasi['JumlahLoket'] == 1){
		echo "
			<option value='1' selected='selected'>1 Loket</option>
			<option value='2'>2 Loket</option>	
		";
		} else {
		echo "
			<option value='1'>1 Loket</option>
			<option value='2' selected='selected'>2 Loket</option>
		";
		}
	}
?>
				</select>
			</div>
			<div class='form-group'>
				<input type='submit' value='Jalankan Simulasi' name='submitsimulasi' class='btn btn-primary' />
				<input type='reset' value='Reset' name='reset' class='btn btn-default' />
			</div>

			<div class='form-group'>
				<a href='<?php echo base_url("modul/mod_prosessimulasi/aksi_prosessimulasi.php?hapus=1"); ?>' class='btn btn-danger'>Kosongkan Hasil Simulasi</a>
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
						<input type='text' class='form-control' name='a1' id='a1' placeholder='A[1]' />
					</div>

					<div class='form-group'>
						<label for='c1'>C[1]</label>
						<input type='text' class='form-control' name='c1' id='c1' placeholder='C[1]' />
					</div>

					<div class='form-group'>
						<label for='z10'>Z[1][0]</label>
						<input type='text' class='form-control' name='z10' id='z10' placeholder='Z[1][0]' />
					</div>
				</div>
				<div class='col-sm-4 col-md-4'>
					<div class='form-group'>
						<label for='a2'>A[2]</label>
						<input type='text' class='form-control' name='a2' id='a2' placeholder='A[2]' />
					</div>

					<div class='form-group'>
						<label for='c2'>C[2]</label>
						<input type='text' class='form-control' name='c2' id='c2' placeholder='C[2]' />
					</div>

					<div class='form-group'>
						<label for='z20'>Z[2][0]</label>
						<input type='text' class='form-control' name='z20' id='z20' placeholder='Z[2][0]' />
					</div>
				</div>
				<div class='col-sm-4 col-md-4'>
					<div class='form-group'>
						<label for='a3'>A[3]</label>
						<input type='text' class='form-control' name='a3' id='a3' placeholder='A[3]' />
					</div>

					<div class='form-group'>
						<label for='c3'>C[3]</label>
						<input type='text' class='form-control' name='c3' id='c3' placeholder='C[3]' />
					</div>

					<div class='form-group'>
						<label for='z30'>Z[3][0]</label>
						<input type='text' class='form-control' name='z30' id='z30' placeholder='Z[3][0]' />
					</div>
				</div>
			</div>
			<div class='row'>
				<div class='col-sm-12 col-md-12'>
					<div class='form-group'>
						<label for='m'>M</label>
						<input type='text' class='form-control' name='m' id='m' placeholder='M' />
					</div>
					<div class='form-group'>
						<div class='checkbox'>
							<label>
								<input type='checkbox' value='1' name='LcgOtomatis' /> Otomatis Generate Parameter
							</label>
						</div>
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
					<input type='text' class='form-control' name='JumlahPelanggan' id='JumlahPelanggan' value='<?php echo $KesimpulanSimulasi['RataRataWarga']; ?>' readonly='readonly' />
					<div class='input-group-addon'>Orang / Hari</div>
				</div>
			</div>

			<div class='form-group'>
				<label for='RataRataWaktuTunggu'>Rata - Rata Waktu Tunggu</label>
				<div class='input-group'>
					<input type='text' class='form-control' name='RataRataWaktuTunggu' id='RataRataWaktuTunggu' value='<?php echo $KesimpulanSimulasi['RataRataWaktuPelayanan']; ?>' readonly='readonly' />
					<div class='input-group-addon'>Menit / Orang</div>
				</div>
			</div>

			<div class='form-group'>
				<label for='RataRataWaktuPelayanan'>Rata - Rata Waktu Pelayanan</label>
				<div class='input-group'>
					<input type='text' class='form-control' name='RataRataWaktuPelayanan' id='RataRataWaktuPelayanan' value='<?php echo $KesimpulanSimulasi['RataRataWaktuTunggu']; ?>' readonly='readonly' />
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
<div class='col-sm-12 col-md-12'>
<?php
	if(isset($nopage)){
		echo $links_tag;
	}
?>
</div>
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