<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

$__DEFAULT_INIT_RESPONSE = [
	"message_code" => "-1",
	"message_description" => "",
	"data" => []
];
$__RESPONSE = $__DEFAULT_INIT_RESPONSE;



function reject($message_code_prefix, $message_code, $message){
	global $__RESPONSE;
	set_message($message_code_prefix.$message_code, "Error: $message");
	die(json_encode($__RESPONSE));
}

function success($message_code_prefix, $message){
	global $__RESPONSE;
	set_message($message_code_prefix."00", "Success: $message");
	die(json_encode($__RESPONSE));
}

function clear_response(){
	global $__RESPONSE, $__DEFAULT_INIT_RESPONSE;
	$__RESPONSE = $__DEFAULT_INIT_RESPONSE;
}

function set_message($message_code, $message_desc){
	global $__RESPONSE;
	$__RESPONSE['message_code'] = $message_code;
	$__RESPONSE['message_description'] = $message_desc;
}

function set_response($info){
	global $__RESPONSE;
	$__RESPONSE['data'] = $info + $__RESPONSE['data'];
}

?>