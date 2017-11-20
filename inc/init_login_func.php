<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}
if(!isset($_FUNC)){
	die("No function!!");
}

require_once("../../inc/init_response_func.php");
set_response([
	"verified" => false
]);

info_check();
require_once("../../inc/db_connect.php");
require_once("../../inc/profile_func.php");
$_FUNC();





function set_login_response(){
	$default = [
		"verified" => true,
		"session_id" => $_SESSION['session_id']
	];

	$info = [
		"info" => getProfile($_SESSION['member_id'])
	];
	
	set_response($info + $default);
}

function set_session($info){
	$_SESSION = $info + $_SESSION;
}

function valid_email($email){
	$pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
	return preg_match($pattern, $email);
}

function valid_password($password){
	return $password != "";
}

function valid_session_id($session_id){
	return $session_id != "";
}

?>