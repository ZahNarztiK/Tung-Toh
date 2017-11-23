<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");

if(!isset($_GET['method'])){
	reject("M", "04", "No method");
}

switch($_GET['method']){
	case "register":
		require_once("../../inc/register.php");
		break;
	case "login":
		require_once("../../inc/login.php");
		break;
	case "verifySession":
		require_once("../../inc/verifySession.php");
		break;
	case "logout":
		require_once("../../inc/logout.php");
		break;
	default:
		reject("M", "04", "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success("M", $success_msg);

?>
