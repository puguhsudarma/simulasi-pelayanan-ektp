<?php
include "../../config/library_config.php";
include "fungsi_prosessimulasi.php";
$Connection = MysqlConnectionOpen();
/*
----------------------------------------------------------
*/
if(isset($_POST['submitsimulasi'])){
//Fetch Database
	$probabilitas_antar_kedatangan	= get_P_Antar_Kedatangan($Connection);
	$probabilitas_proses_1			= get_P_Proses_1($Connection);
	$probabilitas_proses_2			= get_P_Proses_2($Connection);

//Get Parameter
	//setting simulasi
	$LamaPelayanan	= $_POST['LamaPelayananPerhari']*60*60;
	$LamaSimulasi	= $_POST['LamaSimulasi'];
	$JumlahLoket	= $_POST['JumlahLoket'];

	//cek lcg kosong
	$CekLcgValid = 1;
	if(
		$_POST['a1']  == "" ||
		$_POST['a2']  == "" ||
		$_POST['a3']  == "" ||
		$_POST['c1']  == "" ||
		$_POST['c2']  == "" ||
		$_POST['c3']  == "" ||
		$_POST['m']   == "" ||
		$_POST['z10'] == "" ||
		$_POST['z20'] == "" ||
		$_POST['z30'] == ""
		){
		$CekLcgValid = 0;
	}

	//setting parameter lcg
	if(!isset($_POST['LcgOtomatis']) && $CekLcgValid == 1){
	//parameter lcg input manual
		$SettingLCG		= array(
							"A"	 => array(
										1 => $_POST['a1'],
										2 => $_POST['a2'],
										3 => $_POST['a3']
										),
							"C"	 => array(
										1 => $_POST['c1'],
										2 => $_POST['c2'],
										3 => $_POST['c3']
										),
							"M"	 => $_POST['m']
							);
		$Z[1][0] = $_POST['z10'];
		$Z[2][0] = $_POST['z20'];
		$Z[3][0] = $_POST['z30'];
	} else {
	//parameter lcg generate otomatis
		$lcg = generate_Parameter_LCG();
		$SettingLCG		= array(
							"A"	 => array(
										1 => $lcg['A'][0],
										2 => $lcg['A'][1],
										3 => $lcg['A'][2]
										),
							"C"	 => array(
										1 => $lcg['C'][0],
										2 => $lcg['C'][1],
										3 => $lcg['C'][2]
										),
							"M"	 => $lcg['M']
							);
		$Z[1][0] = $lcg['Z'][0];
		$Z[2][0] = $lcg['Z'][0];
		$Z[3][0] = $lcg['Z'][0];
	}
	
//Deklarasi Variabel simulasi
	$HariKe 							= array(array(array()));
	$BanyakWargaPerhari 				= array();
	$RataRataLamaWaktuPelayananPerhari	= array();
	$RataRataLamaWaktuTungguPerhari		= array();
	$KesimpulanSimulasi					= array(
											"RataRataWaktuTunggu" 		=> 0,
											"RataRataWaktuPelayanan" 	=> 0,
											"RataRataWarga" 			=> 0
											);
	$SqlInsertPartQuery					= array();

//Deklarasi Variabel bantuan
	$SelectedAntarKedatangan;
	$SelectedProses1;
	$SelectedProses2;
	$count 		= 1;
	$CountSql	= 0;

//Validasi Parameter
	if($LamaPelayanan == "" && $LamaSimulasi == ""){
		redirect(base_url("index.php?page=prosessimulasi"));
		exit();
	}

//proses simulasi dimulai
	if($JumlahLoket == 1){
	/* *
	 * Proses Simulasi Untuk 1 Loket
	 * ---------------------------------------------------------------------------------------------------------------------------------------
	 */
		for($i=1;$i<=$LamaSimulasi;$i++){
		//variabel kontrol perulangan	
			$k 							= 1;
			$TotalWaktuPelayananPerhari = 0;
			$TotalWaktuTungguPerhari	= 0;
			do{
			//LCG
				$Z[1][$count] 		= generate_LCG_Discrete($SettingLCG['A'][1], $SettingLCG['C'][1], $SettingLCG['M'], $Z[1][$count-1]);
				$Z[2][$count] 		= generate_LCG_Discrete($SettingLCG['A'][2], $SettingLCG['C'][2], $SettingLCG['M'], $Z[2][$count-1]);
				$Z[3][$count] 		= generate_LCG_Discrete($SettingLCG['A'][3], $SettingLCG['C'][3], $SettingLCG['M'], $Z[3][$count-1]);
				
				$UAntarKedatangan	= generate_LCG_Continue($Z[1][$count], $SettingLCG['M']);
				$UProses1			= generate_LCG_Continue($Z[2][$count], $SettingLCG['M']);
				$UProses2			= generate_LCG_Continue($Z[3][$count], $SettingLCG['M']);
			
			//Pencocokkan LCG dengan batas probabilitas
				foreach($probabilitas_antar_kedatangan as $key){
					if($UAntarKedatangan <= $key['probabilitas_kumulatif']){
						$SelectedAntarKedatangan = $key['antar_kedatangan'];
						break;
					}
				}

				foreach($probabilitas_proses_1 as $key){
					if($UProses1 <= $key['probabilitas_kumulatif']){
						$SelectedProses1 = $key['proses_1'];
						break;
					}
				}

				foreach($probabilitas_proses_2 as $key){
					if($UProses2 <= $key['probabilitas_kumulatif']){
						$SelectedProses2 = $key['proses_2'];
						break;
					}
				}

			//Simulasi antrian
			//Orang ke - 1	
				if($k==1){
				//Antar Kedatangan	
					$HariKe[$i][$k]['orang_ke']					= $k;
					$HariKe[$i][$k]['waktu_kedatangan']			= 0;
					$HariKe[$i][$k]['antar_kedatangan']			= 0;
				//loket ke
					$HariKe[$i][$k]['loket_ke']					= 1;
				//proses 1
					$HariKe[$i][$k]['mulai_proses_1']			= 0;
					$HariKe[$i][$k]['lama_proses_1']			= $SelectedProses1;
					$HariKe[$i][$k]['akhir_proses_1']			= $HariKe[$i][$k]['mulai_proses_1'] + $HariKe[$i][$k]['lama_proses_1'];
					$HariKe[$i][$k]['waktu_tunggu_1']			= $HariKe[$i][$k]['mulai_proses_1'] - $HariKe[$i][$k]['waktu_kedatangan'];
				//proses 2	
					$HariKe[$i][$k]['mulai_proses_2']			= $HariKe[$i][$k]['akhir_proses_1'];
					$HariKe[$i][$k]['lama_proses_2']			= $SelectedProses2;
					$HariKe[$i][$k]['akhir_proses_2']			= $HariKe[$i][$k]['mulai_proses_2'] + $HariKe[$i][$k]['lama_proses_2'];
					$HariKe[$i][$k]['waktu_tunggu_2']			= $HariKe[$i][$k]['mulai_proses_2'] - $HariKe[$i][$k]['akhir_proses_1'];
				//total tunggu dan lama pelayanan
					$HariKe[$i][$k]['total_tunggu']				= $HariKe[$i][$k]['waktu_tunggu_1'] + $HariKe[$i][$k]['waktu_tunggu_2'];
					$HariKe[$i][$k]['total_lama_pelayanan']		= $HariKe[$i][$k]['lama_proses_1'] + $HariKe[$i][$k]['lama_proses_2']; 
			//Orang ke - 2	
				} else {
				//Antar kedatangan
					$HariKe[$i][$k]['orang_ke']					= $k;
					$HariKe[$i][$k]['waktu_kedatangan']			= $HariKe[$i][$k-1]['waktu_kedatangan'] + $SelectedAntarKedatangan;
					$HariKe[$i][$k]['antar_kedatangan']			= $SelectedAntarKedatangan;
				//loket ke
					$HariKe[$i][$k]['loket_ke']					= 1;
				//proses 1
					if($HariKe[$i][$k]['waktu_kedatangan'] >= $HariKe[$i][$k-1]['mulai_proses_1'] && $HariKe[$i][$k]['waktu_kedatangan'] <= $HariKe[$i][$k-1]['akhir_proses_1']){
						$HariKe[$i][$k]['mulai_proses_1'] 		= $HariKe[$i][$k-1]['akhir_proses_1'];
					} else if($HariKe[$i][$k]['waktu_kedatangan'] >= $HariKe[$i][$k-1]['akhir_proses_1']){
						$HariKe[$i][$k]['mulai_proses_1'] 		= $HariKe[$i][$k]['waktu_kedatangan'];
					} else if($HariKe[$i][$k]['waktu_kedatangan'] < $HariKe[$i][$k-1]['mulai_proses_1'] && $HariKe[$i][$k]['waktu_kedatangan'] < $HariKe[$i][$k-1]['akhir_proses_1']){
						$HariKe[$i][$k]['mulai_proses_1'] 		= $HariKe[$i][$k-1]['akhir_proses_1'];
					}
					$HariKe[$i][$k]['lama_proses_1']			= $SelectedProses1;
					$HariKe[$i][$k]['akhir_proses_1']			= $HariKe[$i][$k]['mulai_proses_1'] + $HariKe[$i][$k]['lama_proses_1'];
					$HariKe[$i][$k]['waktu_tunggu_1']			= $HariKe[$i][$k]['mulai_proses_1'] - $HariKe[$i][$k]['waktu_kedatangan'];
				//proses 2
					if($HariKe[$i][$k]['akhir_proses_1'] <= $HariKe[$i][$k-1]['akhir_proses_2']){
						$HariKe[$i][$k]['mulai_proses_2'] 		= $HariKe[$i][$k-1]['akhir_proses_2'];
					} else if($HariKe[$i][$k]['akhir_proses_1'] > $HariKe[$i][$k-1]['akhir_proses_2']){
						$HariKe[$i][$k]['mulai_proses_2'] 		= $HariKe[$i][$k]['akhir_proses_1'];
					}
					$HariKe[$i][$k]['lama_proses_2']			= $SelectedProses2;
					$HariKe[$i][$k]['akhir_proses_2']			= $HariKe[$i][$k]['mulai_proses_2'] + $HariKe[$i][$k]['lama_proses_2'];
					$HariKe[$i][$k]['waktu_tunggu_2']			= $HariKe[$i][$k]['mulai_proses_2'] - $HariKe[$i][$k]['akhir_proses_1'];
				//total tunggu dan lama pelayanan
					$HariKe[$i][$k]['total_tunggu']				= $HariKe[$i][$k]['waktu_tunggu_1'] + $HariKe[$i][$k]['waktu_tunggu_2'];
					$HariKe[$i][$k]['total_lama_pelayanan']		= $HariKe[$i][$k]['lama_proses_1'] + $HariKe[$i][$k]['lama_proses_2'];
				}
			//Total semua waktu pelayanan dan waktu tunggu
				$TotalWaktuPelayananPerhari += $HariKe[$i][$k]['total_lama_pelayanan'];
				$TotalWaktuTungguPerhari	+= $HariKe[$i][$k]['total_tunggu'];
			//Sql Query Part
				$SqlInsertPartQuery[$CountSql] = "(
													NULL,
													%d,
													".$i.",
													".$k.",
													".dua_angka($HariKe[$i][$k]['waktu_kedatangan']/60).",
													".dua_angka($HariKe[$i][$k]['antar_kedatangan']/60).",
													".dua_angka($HariKe[$i][$k]['waktu_tunggu_1']/60).",
													".$HariKe[$i][$k]['loket_ke'].",
													".dua_angka($HariKe[$i][$k]['mulai_proses_1']/60).",
													".dua_angka($HariKe[$i][$k]['lama_proses_1']/60).",
													".dua_angka($HariKe[$i][$k]['akhir_proses_1']/60).",
													".dua_angka($HariKe[$i][$k]['waktu_tunggu_2']/60).",
													".dua_angka($HariKe[$i][$k]['mulai_proses_2']/60).",
													".dua_angka($HariKe[$i][$k]['lama_proses_2']/60).",
													".dua_angka($HariKe[$i][$k]['akhir_proses_2']/60).",
													".dua_angka($HariKe[$i][$k]['total_lama_pelayanan']/60).",
													".dua_angka($HariKe[$i][$k]['total_tunggu']/60)."
												)";
			//Increment Variabel Control
				$k++;
				$count++;
				$CountSql++;
			}while($TotalWaktuPelayananPerhari <= $LamaPelayanan);
		//Hitung total dan rata rata
			$BanyakWargaPerhari[$i]					= $k-1;
			$RataRataLamaWaktuPelayananPerhari[$i]	= $TotalWaktuPelayananPerhari/($k-1);
			$RataRataLamaWaktuTungguPerhari[$i]		= $TotalWaktuTungguPerhari/($k-1);
		}
	} else if($JumlahLoket == 2){
	/* *
	 * Proses Simulasi Untuk 2 Loket
	 * ---------------------------------------------------------------------------------------------------------------------------------------
	 */
		for($i=1;$i<=$LamaSimulasi;$i++){
		//variabel kontrol perulangan	
			$k 							= 1;
			$TotalWaktuPelayananPerhari = 0;
			$TotalWaktuTungguPerhari	= 0;
			$LoketMaksAkhirWaktuProses 	= array(
											1=> array(
													1 => 0,
													2 => 0
													),
											2=>array(
													1 => 0,
													2 => 0
													)
											);
			$WaktuLoket					= array(1=>0, 2=>0);
			do{
			//LCG
				$Z[1][$count] 		= generate_LCG_Discrete($SettingLCG['A'][1], $SettingLCG['C'][1], $SettingLCG['M'], $Z[1][$count-1]);
				$Z[2][$count] 		= generate_LCG_Discrete($SettingLCG['A'][2], $SettingLCG['C'][2], $SettingLCG['M'], $Z[2][$count-1]);
				$Z[3][$count] 		= generate_LCG_Discrete($SettingLCG['A'][3], $SettingLCG['C'][3], $SettingLCG['M'], $Z[3][$count-1]);
				
				$UAntarKedatangan	= generate_LCG_Continue($Z[1][$count], $SettingLCG['M']);
				$UProses1			= generate_LCG_Continue($Z[2][$count], $SettingLCG['M']);
				$UProses2			= generate_LCG_Continue($Z[3][$count], $SettingLCG['M']);
			
			//Pencocokkan LCG dengan batas probabilitas
				foreach($probabilitas_antar_kedatangan as $key){
					if($UAntarKedatangan <= $key['probabilitas_kumulatif']){
						$SelectedAntarKedatangan = $key['antar_kedatangan'];
						break;
					}
				}

				foreach($probabilitas_proses_1 as $key){
					if($UProses1 <= $key['probabilitas_kumulatif']){
						$SelectedProses1 = $key['proses_1'];
						break;
					}
				}

				foreach($probabilitas_proses_2 as $key){
					if($UProses2 <= $key['probabilitas_kumulatif']){
						$SelectedProses2 = $key['proses_2'];
						break;
					}
				}
			
			//Mulai simulasi dengan datangnya orang
			//Orang ke - 1
				if($k==1){
				//Antar Kedatangan	
					$HariKe[$i][$k]['orang_ke']					= $k;
					$HariKe[$i][$k]['waktu_kedatangan']			= 0;
					$HariKe[$i][$k]['antar_kedatangan']			= 0;
				//loket ke
					$HariKe[$i][$k]['loket_ke']					= 1;
				//proses 1
					$HariKe[$i][$k]['mulai_proses_1']			= 0;
					$HariKe[$i][$k]['lama_proses_1']			= $SelectedProses1;
					$HariKe[$i][$k]['akhir_proses_1']			= $HariKe[$i][$k]['mulai_proses_1'] + $HariKe[$i][$k]['lama_proses_1'];
					$HariKe[$i][$k]['waktu_tunggu_1']			= $HariKe[$i][$k]['mulai_proses_1'] - $HariKe[$i][$k]['waktu_kedatangan'];
				//proses 2	
					$HariKe[$i][$k]['mulai_proses_2']			= $HariKe[$i][$k]['akhir_proses_1'];
					$HariKe[$i][$k]['lama_proses_2']			= $SelectedProses2;
					$HariKe[$i][$k]['akhir_proses_2']			= $HariKe[$i][$k]['mulai_proses_2'] + $HariKe[$i][$k]['lama_proses_2'];
					$HariKe[$i][$k]['waktu_tunggu_2']			= $HariKe[$i][$k]['mulai_proses_2'] - $HariKe[$i][$k]['akhir_proses_1'];
				//total tunggu dan lama pelayanan
					$HariKe[$i][$k]['total_tunggu']				= $HariKe[$i][$k]['waktu_tunggu_1'] + $HariKe[$i][$k]['waktu_tunggu_2'];
					$HariKe[$i][$k]['total_lama_pelayanan']		= $HariKe[$i][$k]['lama_proses_1'] + $HariKe[$i][$k]['lama_proses_2'];
				//Var Bantuan
					$LoketMaksAkhirWaktuProses[1][1]				= $HariKe[$i][$k]['akhir_proses_1'];
					$LoketMaksAkhirWaktuProses[1][2]				= $HariKe[$i][$k]['akhir_proses_2'];
			//orang ke - 2
				} else if($k==2){
				//Antar kedatangan
					$HariKe[$i][$k]['orang_ke']					= $k;
					$HariKe[$i][$k]['waktu_kedatangan']			= $HariKe[$i][$k-1]['waktu_kedatangan'] + $SelectedAntarKedatangan;
					$HariKe[$i][$k]['antar_kedatangan']			= $SelectedAntarKedatangan;
				//loket ke dan mulai proses 1
					if($HariKe[$i][$k]['waktu_kedatangan'] < $HariKe[$i][$k-1]['akhir_proses_1']){
						$HariKe[$i][$k]['loket_ke'] 			= 2;
					} else {
						$HariKe[$i][$k]['loket_ke'] 			= 1;
					}
				//proses 1
					$HariKe[$i][$k]['mulai_proses_1'] 			= $HariKe[$i][$k]['waktu_kedatangan'];
					$HariKe[$i][$k]['lama_proses_1']			= $SelectedProses1;
					$HariKe[$i][$k]['akhir_proses_1']			= $HariKe[$i][$k]['mulai_proses_1'] + $HariKe[$i][$k]['lama_proses_1'];
					$HariKe[$i][$k]['waktu_tunggu_1']			= $HariKe[$i][$k]['mulai_proses_1'] - $HariKe[$i][$k]['waktu_kedatangan'];
				//proses 2
					if($HariKe[$i][$k]['loket_ke'] == 1){
						if($HariKe[$i][$k]['akhir_proses_1'] <= $HariKe[$i][$k-1]['akhir_proses_2']){
							$HariKe[$i][$k]['mulai_proses_2'] 	= $HariKe[$i][$k-1]['akhir_proses_2'];
						} else if($HariKe[$i][$k]['akhir_proses_1'] > $HariKe[$i][$k-1]['akhir_proses_2']){
							$HariKe[$i][$k]['mulai_proses_2'] 	= $HariKe[$i][$k]['akhir_proses_1'];
						}
					} else if($HariKe[$i][$k]['loket_ke'] == 2){
						$HariKe[$i][$k]['mulai_proses_2']		= $HariKe[$i][$k]['akhir_proses_1'];
					}
					$HariKe[$i][$k]['lama_proses_2']			= $SelectedProses2;
					$HariKe[$i][$k]['akhir_proses_2']			= $HariKe[$i][$k]['mulai_proses_2'] + $HariKe[$i][$k]['lama_proses_2'];
					$HariKe[$i][$k]['waktu_tunggu_2']			= $HariKe[$i][$k]['mulai_proses_2'] - $HariKe[$i][$k]['akhir_proses_1'];
				//total tunggu dan lama pelayanan
					$HariKe[$i][$k]['total_tunggu']				= $HariKe[$i][$k]['waktu_tunggu_1'] + $HariKe[$i][$k]['waktu_tunggu_2'];
					$HariKe[$i][$k]['total_lama_pelayanan']		= $HariKe[$i][$k]['lama_proses_1'] + $HariKe[$i][$k]['lama_proses_2'];
				//var bantuan
					if($HariKe[$i][$k]['loket_ke'] == 1){
						if($LoketMaksAkhirWaktuProses[1][1] < $HariKe[$i][$k]['akhir_proses_1']){
							$LoketMaksAkhirWaktuProses[1][1] = $HariKe[$i][$k]['akhir_proses_1'];
						}
						if($LoketMaksAkhirWaktuProses[1][2] < $HariKe[$i][$k]['akhir_proses_2']){
							$LoketMaksAkhirWaktuProses[1][2] = $HariKe[$i][$k]['akhir_proses_2'];
						}
					} else if($HariKe[$i][$k]['loket_ke'] == 2){
						if($LoketMaksAkhirWaktuProses[2][1] < $HariKe[$i][$k]['akhir_proses_1']){
							$LoketMaksAkhirWaktuProses[2][1] = $HariKe[$i][$k]['akhir_proses_1'];
						}
						if($LoketMaksAkhirWaktuProses[2][2] < $HariKe[$i][$k]['akhir_proses_2']){
							$LoketMaksAkhirWaktuProses[2][2] = $HariKe[$i][$k]['akhir_proses_2'];
						}
					}
			//orang ke > 2
				} else {
				//Antar kedatangan
					$HariKe[$i][$k]['orang_ke']					= $k;
					$HariKe[$i][$k]['waktu_kedatangan']			= $HariKe[$i][$k-1]['waktu_kedatangan'] + $SelectedAntarKedatangan;
					$HariKe[$i][$k]['antar_kedatangan']			= $SelectedAntarKedatangan;
				//loket ke dan mulai proses 1
					//loket 1
					if($HariKe[$i][$k]['waktu_kedatangan'] >= $LoketMaksAkhirWaktuProses[1][1] && $HariKe[$i][$k]['waktu_kedatangan'] < $LoketMaksAkhirWaktuProses[2][1]){
						$HariKe[$i][$k]['loket_ke'] = 1;
						$HariKe[$i][$k]['mulai_proses_1'] = $HariKe[$i][$k]['waktu_kedatangan'];
					//loket 2
					} else if($HariKe[$i][$k]['waktu_kedatangan'] < $LoketMaksAkhirWaktuProses[1][1] && $HariKe[$i][$k]['waktu_kedatangan'] >= $LoketMaksAkhirWaktuProses[2][1]){
						$HariKe[$i][$k]['loket_ke'] = 2;
						$HariKe[$i][$k]['mulai_proses_1'] = $HariKe[$i][$k]['waktu_kedatangan'];
					//loket semua penuh
					} else if($HariKe[$i][$k]['waktu_kedatangan'] < $LoketMaksAkhirWaktuProses[1][1] && $HariKe[$i][$k]['waktu_kedatangan'] < $LoketMaksAkhirWaktuProses[2][1]){
						if($LoketMaksAkhirWaktuProses[1][1] <= $LoketMaksAkhirWaktuProses[2][1]){
							$HariKe[$i][$k]['loket_ke'] = 1;
							$HariKe[$i][$k]['mulai_proses_1'] = $LoketMaksAkhirWaktuProses[1][1];
						} else {
							$HariKe[$i][$k]['loket_ke'] = 2;
							$HariKe[$i][$k]['mulai_proses_1'] = $LoketMaksAkhirWaktuProses[2][1];
						}
					//loket semua kosong
					} else if($HariKe[$i][$k]['waktu_kedatangan'] >= $LoketMaksAkhirWaktuProses[1][1] && $HariKe[$i][$k]['waktu_kedatangan'] >= $LoketMaksAkhirWaktuProses[2][1]){
						if(rand(1,2) == 1){
							$HariKe[$i][$k]['loket_ke'] = 1;
							$HariKe[$i][$k]['mulai_proses_1'] = $HariKe[$i][$k]['waktu_kedatangan'];
						} else {
							$HariKe[$i][$k]['loket_ke'] = 2;
							$HariKe[$i][$k]['mulai_proses_1'] = $HariKe[$i][$k]['waktu_kedatangan'];
						}
					}
				//proses 1
					$HariKe[$i][$k]['lama_proses_1'] = $SelectedProses1;
					$HariKe[$i][$k]['akhir_proses_1'] = $HariKe[$i][$k]['mulai_proses_1'] + $HariKe[$i][$k]['lama_proses_1'];
					$HariKe[$i][$k]['waktu_tunggu_1'] = $HariKe[$i][$k]['mulai_proses_1'] - $HariKe[$i][$k]['waktu_kedatangan'];
				//proses 2
					if($HariKe[$i][$k]['loket_ke'] == 1){
						if($HariKe[$i][$k]['akhir_proses_1'] <= $LoketMaksAkhirWaktuProses[1][2]){
							$HariKe[$i][$k]['mulai_proses_2'] = $LoketMaksAkhirWaktuProses[1][2];
						} else if($HariKe[$i][$k]['akhir_proses_1'] > $LoketMaksAkhirWaktuProses[1][2]){
							$HariKe[$i][$k]['mulai_proses_2'] = $HariKe[$i][$k]['akhir_proses_1'];
						}
					} else if($HariKe[$i][$k]['loket_ke'] == 2){
						if($HariKe[$i][$k]['akhir_proses_1'] <= $LoketMaksAkhirWaktuProses[2][2]){
							$HariKe[$i][$k]['mulai_proses_2'] = $LoketMaksAkhirWaktuProses[2][2];
						} else if($HariKe[$i][$k]['akhir_proses_1'] > $LoketMaksAkhirWaktuProses[2][2]){
							$HariKe[$i][$k]['mulai_proses_2'] = $HariKe[$i][$k]['akhir_proses_1'];
						}
					}
					$HariKe[$i][$k]['lama_proses_2']		= $SelectedProses2;
					$HariKe[$i][$k]['akhir_proses_2']		= $HariKe[$i][$k]['mulai_proses_2'] + $HariKe[$i][$k]['lama_proses_2'];
					$HariKe[$i][$k]['waktu_tunggu_2']		= $HariKe[$i][$k]['mulai_proses_2'] - $HariKe[$i][$k]['akhir_proses_1'];
				//total tunggu dan lama pelayanan
					$HariKe[$i][$k]['total_tunggu'] = $HariKe[$i][$k]['waktu_tunggu_1'] + $HariKe[$i][$k]['waktu_tunggu_2'];
					$HariKe[$i][$k]['total_lama_pelayanan'] = $HariKe[$i][$k]['lama_proses_1'] + $HariKe[$i][$k]['lama_proses_2'];
				//var bantuan
					if($HariKe[$i][$k]['loket_ke'] == 1){
						if($LoketMaksAkhirWaktuProses[1][1] < $HariKe[$i][$k]['akhir_proses_1']){
							$LoketMaksAkhirWaktuProses[1][1] = $HariKe[$i][$k]['akhir_proses_1'];
						}
						if($LoketMaksAkhirWaktuProses[1][2] < $HariKe[$i][$k]['akhir_proses_2']){
							$LoketMaksAkhirWaktuProses[1][2] = $HariKe[$i][$k]['akhir_proses_2'];
						}
					} else if($HariKe[$i][$k]['loket_ke'] == 2){
						if($LoketMaksAkhirWaktuProses[2][1] < $HariKe[$i][$k]['akhir_proses_1']){
							$LoketMaksAkhirWaktuProses[2][1] = $HariKe[$i][$k]['akhir_proses_1'];
						}
						if($LoketMaksAkhirWaktuProses[2][2] < $HariKe[$i][$k]['akhir_proses_2']){
							$LoketMaksAkhirWaktuProses[2][2] = $HariKe[$i][$k]['akhir_proses_2'];
						}
					}
				}

			//Total semua waktu pelayanan
				if($HariKe[$i][$k]['loket_ke'] == 1){
					$WaktuLoket[1] += $HariKe[$i][$k]['total_lama_pelayanan'];
				} else if($HariKe[$i][$k]['loket_ke'] == 2){
					$WaktuLoket[2] += $HariKe[$i][$k]['total_lama_pelayanan'];
				}

				if($WaktuLoket[1] > $WaktuLoket[2]){
					$TotalWaktuPelayananPerhari = $WaktuLoket[1];
				} else {
					$TotalWaktuPelayananPerhari = $WaktuLoket[2];
				}
			//Total semua waktu tunggu
				$TotalWaktuTungguPerhari	+= $HariKe[$i][$k]['total_tunggu'];
			//Sql Query Part
				$SqlInsertPartQuery[$CountSql] = "(
													NULL,
													%d,
													".$i.",
													".$k.",
													".dua_angka($HariKe[$i][$k]['waktu_kedatangan']/60).",
													".dua_angka($HariKe[$i][$k]['antar_kedatangan']/60).",
													".dua_angka($HariKe[$i][$k]['waktu_tunggu_1']/60).",
													".$HariKe[$i][$k]['loket_ke'].",
													".dua_angka($HariKe[$i][$k]['mulai_proses_1']/60).",
													".dua_angka($HariKe[$i][$k]['lama_proses_1']/60).",
													".dua_angka($HariKe[$i][$k]['akhir_proses_1']/60).",
													".dua_angka($HariKe[$i][$k]['waktu_tunggu_2']/60).",
													".dua_angka($HariKe[$i][$k]['mulai_proses_2']/60).",
													".dua_angka($HariKe[$i][$k]['lama_proses_2']/60).",
													".dua_angka($HariKe[$i][$k]['akhir_proses_2']/60).",
													".dua_angka($HariKe[$i][$k]['total_lama_pelayanan']/60).",
													".dua_angka($HariKe[$i][$k]['total_tunggu']/60)."
												)";
			//Increment Variabel Control
				$k++;
				$count++;
				$CountSql++;
			}while($TotalWaktuPelayananPerhari <= $LamaPelayanan);
		//Hitung total dan rata rata
			$BanyakWargaPerhari[$i]					= $k-1;
			$RataRataLamaWaktuPelayananPerhari[$i]	= $TotalWaktuPelayananPerhari/($k-1);
			$RataRataLamaWaktuTungguPerhari[$i]		= $TotalWaktuTungguPerhari/($k-1);
		}
	}
/*
 * ---------------------------------------------------------------------------------------------------------------------------------------------
 */
//Kesimpulan Simulasi
	//rata - rata banyak warga
	foreach($BanyakWargaPerhari as $data){
		$KesimpulanSimulasi['RataRataWarga'] += $data;
	}
	$KesimpulanSimulasi['RataRataWarga'] = $KesimpulanSimulasi['RataRataWarga']/$LamaSimulasi;
	$KesimpulanSimulasi['RataRataWarga'] = number_format($KesimpulanSimulasi['RataRataWarga'],0);
	//rata - rata lama waktu pelayanan
	foreach ($RataRataLamaWaktuPelayananPerhari as $data) {
		$KesimpulanSimulasi['RataRataWaktuPelayanan'] += $data;	
	}
	$KesimpulanSimulasi['RataRataWaktuPelayanan'] = ($KesimpulanSimulasi['RataRataWaktuPelayanan']/$LamaSimulasi)/60;
	$KesimpulanSimulasi['RataRataWaktuPelayanan'] = dua_angka($KesimpulanSimulasi['RataRataWaktuPelayanan']);
	//rata - rata lama waktu tunggu
	foreach ($RataRataLamaWaktuTungguPerhari as $data) {
		$KesimpulanSimulasi['RataRataWaktuTunggu'] += $data;
	}
	$KesimpulanSimulasi['RataRataWaktuTunggu'] = ($KesimpulanSimulasi['RataRataWaktuTunggu']/$LamaSimulasi)/60;
	$KesimpulanSimulasi['RataRataWaktuTunggu'] = dua_angka($KesimpulanSimulasi['RataRataWaktuTunggu']);

//Kesimpulan simpan pada session untuk di pass ke tampilan
	session_start();
	$_SESSION = array(
					"RataRataWarga"				=> $KesimpulanSimulasi['RataRataWarga'],
					"RataRataWaktuPelayanan"	=> $KesimpulanSimulasi['RataRataWaktuPelayanan'],
					"RataRataWaktuTunggu"		=> $KesimpulanSimulasi['RataRataWaktuTunggu'],
					"LamaPelayanan"				=> $LamaPelayanan/3600,
					"LamaSimulasi"				=> $LamaSimulasi,
					"JumlahLoket"				=> $JumlahLoket
					);

//Simpan Hasil Simulasi ke Database
	//Simpan ke tabel replikasi
	$query = mysqli_query($Connection, "SELECT MAX(replikasi_ke) AS new_replikasi FROM replikasi LIMIT 1");
	$row   = mysqli_fetch_array($query, MYSQLI_ASSOC);
	$new   = $row['new_replikasi']+1;
	mysqli_fetch_array($query);
	$sql_insert_replikasi = "INSERT INTO `replikasi` VALUES(
														NULL,
														".$new.",
														".($LamaPelayanan/3600).",
														".$LamaSimulasi.",
														".$JumlahLoket.",

														".$SettingLCG['A'][1].",
														".$SettingLCG['C'][1].",
														".$Z[1][0].",

														".$SettingLCG['A'][2].",
														".$SettingLCG['C'][2].",
														".$Z[2][0].",
														
														".$SettingLCG['A'][3].",
														".$SettingLCG['C'][3].",
														".$Z[3][0].",
														
														".$SettingLCG['M'].",

														".$KesimpulanSimulasi['RataRataWarga'].",
														".$KesimpulanSimulasi['RataRataWaktuPelayanan'].",
														".$KesimpulanSimulasi['RataRataWaktuTunggu']."
														);";
	
	$query = mysqli_query($Connection, $sql_insert_replikasi) or die(mysqli_error($Connection));
	$query = mysqli_query($Connection, "SELECT id_replikasi FROM replikasi ORDER BY id_replikasi DESC LIMIT 1;");
	$fetch = mysqli_fetch_array($query, MYSQLI_ASSOC);
	$id_replikasi = $fetch['id_replikasi'];
	mysqli_free_result($query);

	//Simpan ke tabel hasil simulasi
	$sql_insert_hasil_simulasi = "INSERT INTO `hasil_simulasi` VALUES ".implode(",", $SqlInsertPartQuery).";";
	$sql_insert_hasil_simulasi = str_replace("%d", $id_replikasi, $sql_insert_hasil_simulasi);
	$query = mysqli_query($Connection, $sql_insert_hasil_simulasi) or die (mysqli_error($Connection));
}

if(isset($_GET['hapus'])){
	if($_GET['hapus'] == 1){
		$sql_truncate  = "TRUNCATE `replikasi`;";
		$sql_truncate .= "TRUNCATE `hasil_simulasi`;";

		$query = mysqli_multi_query($Connection, $sql_truncate);
	}
}
MysqlConnectionClose($Connection);
redirect(base_url("index.php?page=prosessimulasi"));
?>