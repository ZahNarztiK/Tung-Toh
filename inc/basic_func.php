<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

function isPositiveInt($num){
	return (!is_nan($num) && $num > 0);
}

function notPositiveInt($num){
	return (is_nan($num) || $num <= 0);
}

function isTimeStamp($timestamp){
	$pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
	return preg_match($pattern, $timestamp);
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

function valid_email($email){
	$pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
	return preg_match($pattern, $email);
}

function valid_imageURL($url){
	$pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
	return preg_match($pattern, $url);
}

function valid_password($password){
	return $password != "";
}

function valid_session_id($session_id){
	return $session_id != "";
}

function valid_tel($tel){
	$pattern = "/^\d{9,10})$/";
	return preg_match($pattern, $email);
}

?>
