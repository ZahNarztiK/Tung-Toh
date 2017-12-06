<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

$GLOBALS['LOGOUT_PREFIX'] = "ML";


function logout(){
	$logged = isset($_SESSION['verified']);

	session_unset();
	session_destroy();

	
	$rs = [
		"logged_out" => $logged
	];


	return $rs;
}

?>
