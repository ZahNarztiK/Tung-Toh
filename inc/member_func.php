<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}
if(!isset($_SESSION)){
	session_start();
}

require_once("../../inc/db_connect.php");
require_once("../../inc/init_response_func.php");
require_once("../../inc/basic_func.php");
require_once("../../inc/profile_func.php");

require_once("../../inc/member_subfunc/register.php");
require_once("../../inc/member_subfunc/verifySession.php");
require_once("../../inc/member_subfunc/login.php");
require_once("../../inc/member_subfunc/logout.php");
require_once("../../inc/member_subfunc/forgetPassword.php");
require_once("../../inc/member_subfunc/resetPassword.php");

$__MEMBER_DEFAULT_PREFIX = "MX";
$GLOBALS['MEMBER_PREFIX'] = $__MEMBER_DEFAULT_PREFIX;


function init_login_response(){
	set_response([
		"verified" => false
	]);
}

function init_send_response($status = false){
	set_response([
		"status" => $status
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

function run_RLS_set($func, $data){
	init_login_response();
	$rs = $func($data);
	set_session($rs);

	$rs = get_login_response();
	
	return $rs;
}

function set_session($info){
	$_SESSION = $info + $_SESSION;
}

?>