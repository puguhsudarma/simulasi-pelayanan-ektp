<?php
//function untuk membuka koneksi ke database mysql
function MysqlConnectionOpen(){
	$setting_mysql = array(
					"host"		=> "localhost",
					"username"	=> "root",
					"password"	=> "",
					"database"	=> "simulasi_ektp"
				);

	$Connection = @mysqli_connect($setting_mysql['host'],$setting_mysql['username'],$setting_mysql['password'], $setting_mysql['database']);
	
	if(!$Connection){
		printf("<pre>	Error 				: Unable to connect to MySQL.<br />");
		printf("	Debugging error number 		: %d<br />", mysqli_connect_errno());
		printf("	Debugging error 		: %s<br /></pre>", mysqli_connect_error());
		exit;
	}

	return $Connection;
}

//function untuk menutup koneksi ke database mysql
function MysqlConnectionClose($Connection){
	if(!$Connection){
		return 0;
	} else {
		mysqli_close($Connection);
	}
}

//function untuk menentukan url root dari website
function base_url($string = ""){
	if($string == ""){
		$url = 'http://'.$_SERVER['SERVER_NAME']."/simulasi-pelayanan-ektp/";	
	} else {
		$url = 'http://'.$_SERVER['SERVER_NAME']."/simulasi-pelayanan-ektp/".$string;
	}
	
	return $url;
}

//fungsi untuk berpindah halaman
function redirect($url){
    echo "
    	<script type='text/javascript'>
    		window.location = '".$url."';
    	</script>
    ";
}
?>
