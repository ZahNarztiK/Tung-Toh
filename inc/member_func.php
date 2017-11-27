<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}
if(!isset($_SESSION)){
	session_start();
}

require_once("../../inc/db_connect.php");
require_once("../../inc/init_response_func.php");
require_once("../../inc/profile_func.php");

require_once("../../inc/member_subfunc/register.php");
require_once("../../inc/member_subfunc/verifySession.php");
require_once("../../inc/member_subfunc/login.php");
require_once("../../inc/member_subfunc/logout.php");


function init_login_response(){
	set_response([
		"verified" => false
	]);
}

function get_login_response(){
	$default = [
		"verified" => true,
		"session_id" => $_SESSION['session_id']
	];

	$info = [
		"info" => getProfile($_SESSION['member_id'])
	];
	
	return ($info + $default);
}

function run_RLS_set($func){
	init_login_response();
	$rs = $func();
	set_session($rs);

	$rs = get_login_response();
	
	return $rs;
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