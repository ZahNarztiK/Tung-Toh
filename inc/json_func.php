<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

function prepareJSON($prefix, $data, $default_data = []){
	if(is_string($data)){
		$data = json_decode($data, true);
	}
	if(!is_array($data)){
		reject($prefix, "04", "JSON Error");
	}

	return $data + $default_data;
}

?>
