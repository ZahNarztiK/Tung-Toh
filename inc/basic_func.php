<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/basic/init_response_func.php");
require_once("../../inc/basic/db_connect.php");
require_once("../../inc/basic/access_func.php");

function isFloat($num){
	return (is_numeric($num) && is_float($num + 0));
}

function isInt($num){
	return (is_numeric($num) && is_int($num + 0));
}

function prepDate(&$date){
	if(notPositiveInt($date)){
		return false;
	}

	$date = date("Y-m-d H:i:s", $event['date']);
	return true;
}

function prepStr(&$str, $required){
	$str = trim($str);
	if($required && $str == ""){
		return false;
	}
	else{
		return true;
	}
}

function isPositiveInt($num){
	return (isInt($num) && ($num > 0));
}

function notPositiveInt($num){
	return !isPositiveInt($num);
}

function isTimeStamp($timestamp){
	$pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
	return preg_match($pattern, $timestamp);
}

function prepareJSON($prefix, $data, $required_data = [], $default_data = [], $reject = true){
	if(is_string($data)){
		$data = json_decode($data, true);
	}
	if(!is_array($data)){
		if($reject){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['JSON'], "JSON Error");
		}
		else{
			return null;
		}
	}

	if(!empty($required_data)){
		$verified = screenData($data, $required_data);
		if(!$verified){
			if($reject){
				reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "DATA KAK SHIP HAI KWAIIII!!!!!");
			}
			else{
				return null;
			}
		}
	}

	return $data + $default_data;
}

function screenData(&$data, $required_data){
	foreach($required_data as $mode => $var){
		$required = preg_match("/^.*(\*)$/", $mode);
		if(preg_match("/^(.*[^\*])\*?$/", $mode, $rs)){
			$mode_s = $rs[1];
		}
		else{
			$mode_s = "";
		}

		switch($mode_s){
			case "int":
				$func = "isInt";
				break;

			case "+int":
				$func = "isPositiveInt";
				break;

			case "float":
				$func = "isFloat";
				break;

			case "str":
				$func = "prepStr";
				break;

			case "date":
				$func = "prepDate";

			case "email":
				$func = "valid_email";

			case "password":
				$func = "valid_password";

			case "image":
				$func = "valid_imageURL";

			default:
				continue 2;
				break;
		}

		if(gettype($var) == "array"){
			foreach($var as $key){
				if(!__screenDataCheck($data, $key, $required, $func)){
					return false;
				}
			}
		}
		else{
			if(!__screenDataCheck($data, $var, $required, $func)){
				return false;
			}
		}
		
	}
	return true;
}

function __screenDataCheck(&$data, $key, $required, $func){
	if(isset($data[$key])){
		if(!$func($data[$key], $required)){
			return false;
		}
	}
	elseif($required){
		return false;
	}
	return true;
}

function valid_email($email){
	$pattern = "/^[\w-]+(\.[\w-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/";
	return preg_match($pattern, $email);
}

function valid_imageURL($url){	//incompleted
	$pattern = "/^[\w-]+(\.[\w-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/";
	return preg_match($pattern, $url);
}

function valid_password($password){	//incompleted
	return $password != "";
}

function valid_session_id($session_id){
	return $session_id != "";
}

function valid_tel($tel){	//incompleted
	$pattern = "/^\d{9,10})$/";
	return preg_match($pattern, $tel);
}

?>
