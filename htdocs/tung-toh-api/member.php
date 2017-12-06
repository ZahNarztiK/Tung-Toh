<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/member_func.php");


if(!isset($_GET['method'])){
	reject($GLOBALS['MEMBER_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "No method");
}

switch($_GET['method']){
	case "register":
		$GLOBALS['MEMBER_PREFIX'] = $GLOBALS['REGISTER_PREFIX'];

		$data = data_check($GLOBALS['MEMBER_PREFIX']);
		$rs = run_RLS_set("add_member", $data);
		
		$success_msg = "Samuk dai la!";
		break;
	case "login":
		$GLOBALS['MEMBER_PREFIX'] = $GLOBALS['LOGIN_PREFIX'];

		$data = data_check($GLOBALS['MEMBER_PREFIX']);
		$rs = run_RLS_set("login", $data);

		$success_msg = "Login dai la!";
		break;
	case "verifySession":
		$GLOBALS['MEMBER_PREFIX'] = $GLOBALS['VERIFYSESSION_PREFIX'];

		$data = data_check($GLOBALS['MEMBER_PREFIX']);
		$rs = run_RLS_set("verifySession", $data);

		$success_msg = "Login dai la!";
		break;
	case "logout":
		$GLOBALS['MEMBER_PREFIX'] = $GLOBALS['LOGOUT_PREFIX'];

		$rs = logout();

		$success_msg = "Session cleared!";
		break;
	case "forgetpwd":
		$GLOBALS['MEMBER_PREFIX'] = $GLOBALS['FORGET_PREFIX'];

		$data = data_check($GLOBALS['MEMBER_PREFIX']);
		$rs = forgetPassword($data);

		$success_msg = "Arn mail duay";
		break;
	case "resetpwd":
		$GLOBALS['MEMBER_PREFIX'] = $GLOBALS['RESETPWD_PREFIX'];
		
		$data = data_check($GLOBALS['MEMBER_PREFIX']);
		$rs = resetPassword($data);

		$success_msg = "Ahhhh yeahh~~~~";
		break;
	default:
		reject($GLOBALS['MEMBER_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success($GLOBALS['MEMBER_PREFIX'], $success_msg);

?>
