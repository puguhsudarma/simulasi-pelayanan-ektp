<?php
//fungsi generate bilangan prima
function bilangan_prima($n = 100){
	$prima = array();
	$k = 0;
	for($i=1;$i<=$n;$i++){
		$a = 0;
		for($j=1;$j<=$i;$j++){
			if($i % $j == 0){
				$a++;
			}
		}
		if($a == 2){
			$prima[$k] = $i;
			$k++;
		}
	}
	return $prima;
}

//fungsi untuk generate parameter lcg
function generate_Parameter_LCG(){
	//deklarasi variabel
	$lcg = array(array());

	//generate bilangan K untuk A
	$k = array (rand(0,25), rand(0,25), rand(0,25));
	$lcg['A'][0] = 1+(4*$k[0]);
	$lcg['A'][1] = 1+(4*$k[1]);
	$lcg['A'][2] = 1+(4*$k[2]);
	//generate bilangan prima untuk C	
	$prima = bilangan_prima();
	$rand_prima = array_rand($prima, 3);
	$lcg['C'][0] = $prima[$rand_prima[0]];
	$lcg['C'][1] = $prima[$rand_prima[1]];
	$lcg['C'][2] = $prima[$rand_prima[2]];
	//generate bilangan b untuk M
	$b = rand(2,10);
	$lcg['M'] = pow(2, $b);
	//generate aturan untuk Z0
	$MinMax = array(
				"Min" => array(
							min($lcg['A'][0], $lcg['C'][0], $lcg['M']),
							min($lcg['A'][1], $lcg['C'][1], $lcg['M']),
							min($lcg['A'][2], $lcg['C'][2], $lcg['M'])
							),
				"Max" => array(
							max($lcg['A'][0], $lcg['C'][0], $lcg['M']),
							max($lcg['A'][1], $lcg['C'][1], $lcg['M']),
							max($lcg['A'][2], $lcg['C'][2], $lcg['M'])
							)
				);
	$lcg['Z'][0] = rand($MinMax["Min"][0], $MinMax["Max"][0]);
	$lcg['Z'][1] = rand($MinMax["Min"][1], $MinMax["Max"][1]);
	$lcg['Z'][2] = rand($MinMax["Min"][2], $MinMax["Max"][2]);
	//return value
	return $lcg;
}

//fungsi untuk dua angka dibelakang koma
function dua_angka($x){
	return number_format($x, 2);
}

//fungsi untuk membuat angka acak discrete
function generate_LCG_Discrete($a, $c, $m, $z){
	return ((($a*$z)+$c)%$m);
}

//fungsi untuk membuat angka acak continue
function generate_LCG_Continue($z, $m){
	return round($z/$m, 2);
}

//fungsi untuk mendapatkan data probabilitas antar kedatangan
function get_P_Antar_Kedatangan($koneksi){
	$query = mysqli_query($koneksi, "SELECT * FROM p_antar_kedatangan");
	while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
		$data[] = $row;
	}
	mysqli_free_result($query);
	return $data;
}

//fungsi untuk mendapatkan data probabilitas proses 1
function get_P_Proses_1($koneksi){
	$query = mysqli_query($koneksi, "SELECT * FROM p_proses_1");
	while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
		$data[] = $row;
	}
	mysqli_free_result($query);
	return $data;
}

//fungsi untuk mendapatkan data probabilitas proses 2
function get_P_Proses_2($koneksi){
	$query = mysqli_query($koneksi, "SELECT * FROM p_proses_2");
	while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
		$data[] = $row;
	}
	mysqli_free_result($query);
	return $data;
}
?>