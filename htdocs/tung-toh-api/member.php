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
		$rs = run_RLS_set("add_member");

		$__MEMBER_PREFIX = $__REGISTER_PREFIX;
		$success_msg = "Samuk dai la!";
		break;
	case "login":
		$rs = run_RLS_set("login");

		$__MEMBER_PREFIX = $__LOGIN_PREFIX;
		$success_msg = "Login dai la!";
		break;
	case "verifySession":
		$rs = run_RLS_set("verifySession");

		$__MEMBER_PREFIX = $__VERIFYSESSION_PREFIX;
		$success_msg = "Login dai la!";
		break;
	case "logout":
		$rs = logout();

		$__MEMBER_PREFIX = $__LOGIN_PREFIX;
		$success_msg = "Session cleared!";
		break;
	case "forget":
		$rs = forgetPassword();

		$__MEMBER_PREFIX = $__FORGET_PREFIX;
		$success_msg = "Arn mail duay";
		break;
	case "resetpwd":
		$rs = resetPassword();

		$__MEMBER_PREFIX = $__RESETPWD_PREFIX;
		$success_msg = "Ahhhh yeahh~~~~";
		break;
	default:
		reject($__MEMBER_PREFIX, "04", "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success($__MEMBER_PREFIX, $success_msg);

?>
