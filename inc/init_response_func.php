<?

if(!isset($in_site))
	die("Access denied ai sus!!!");

$response = [];

function reject($message){
	global $response;
	set_message("Error: $message");
	die(json_encode($response));
}

function success($message){
	global $response;
	set_message($message);
	echo json_encode($response);
}

function clear_response(){
	global $response;
	$response = [];
}

function set_message($message){
	global $response;
	$response['message'] = $message;
}

function set_response($info){
	global $response;
	$response =	$info + $response;
}

?>