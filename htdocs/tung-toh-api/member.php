<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/member_func.php");


$__MEMBER_DEFAULT_PREFIX = "MX";
$__MEMBER_PREFIX = $__MEMBER_DEFAULT_PREFIX;


if(!isset($_GET['method'])){
	reject($__MEMBER_PREFIX, "04", "No method");
}

switch($_GET['method']){
	case "register":
		$__MEMBER_PREFIX = $__REGISTER_PREFIX;

		$data = data_check($__MEMBER_PREFIX);
		$rs = run_RLS_set("add_member", $data);
		
		$success_msg = "Samuk dai la!";
		break;
	case "login":
		$__MEMBER_PREFIX = $__LOGIN_PREFIX;

		$data = data_check($__MEMBER_PREFIX);
		$rs = run_RLS_set("login", $data);

		$success_msg = "Login dai la!";
		break;
	case "verifySession":
		$__MEMBER_PREFIX = $__VERIFYSESSION_PREFIX;

		$data = data_check($__MEMBER_PREFIX);
		$rs = run_RLS_set("verifySession", $data);

		$success_msg = "Login dai la!";
		break;
	case "logout":
		$__MEMBER_PREFIX = $__LOGIN_PREFIX;

		$rs = logout();

		$success_msg = "Session cleared!";
		break;
	case "forgetpwd":
		$__MEMBER_PREFIX = $__FORGET_PREFIX;

		$data = data_check($__MEMBER_PREFIX);
		$rs = forgetPassword($data);

		$success_msg = "Arn mail duay";
		break;
	case "resetpwd":
		$__MEMBER_PREFIX = $__RESETPWD_PREFIX;
		
		$data = data_check($__MEMBER_PREFIX);
		$rs = resetPassword($data);

		$success_msg = "Ahhhh yeahh~~~~";
		break;
	default:
		reject($__MEMBER_PREFIX, "04", "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success($__MEMBER_PREFIX, $success_msg);

?>
