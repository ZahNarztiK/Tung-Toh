<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

$__DEFAULT_INIT_RESPONSE = [
	"message_code" => "-1",
	"message_numeric_code" => "-1",
	"message_description" => "",
	"data" => []
];
$GLOBALS['RESPONSE_SUCCESS_CODE'] = "00";
$GLOBALS['RESPONSE_ERROR_CODE'] = [
	"JSON" => "03",
	"INFO" => "04",
	"PHP" => "09",
	"PDO" => "10",
	"DB_NODATA" => "14",
	"DB_DUPLICATED" => "15",
	"DB_FAILED" => "19",
	"AC_LOW" => "90",
	"AC_LOGGED" => "99"
];
$GLOBALS['__RESPONSE'] = $__DEFAULT_INIT_RESPONSE;



function reject($message_code_prefix, $message_code, $message){
	set_message($message_code_prefix, $message_code, "Error: $message");
	die(json_encode($GLOBALS['__RESPONSE']));
}

function success($message_code_prefix, $message){
	set_message($message_code_prefix, $GLOBALS['RESPONSE_SUCCESS_CODE'], "Success: $message");
	die(json_encode($GLOBALS['__RESPONSE']));
}

function clear_response(){
	$GLOBALS['__RESPONSE'] = $__DEFAULT_INIT_RESPONSE;
}

function set_message($message_code, $message_numeric_code, $message_desc){
	$GLOBALS['__RESPONSE']['message_code'] = $message_code;
	$GLOBALS['__RESPONSE']['message_numeric_code'] = $message_numeric_code;
	$GLOBALS['__RESPONSE']['message_description'] = $message_desc;
}

function set_response($info){
	$GLOBALS['__RESPONSE']['data'] = $info + $GLOBALS['__RESPONSE']['data'];
}

?>
