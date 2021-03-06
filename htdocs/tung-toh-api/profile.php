<?

session_start();

$_IN_SITE = true;
require_once("../../inc/basic_func.php");
require_once("../../inc/profile_func.php");


if(!isset($_GET['method'])){
	reject($GLOBALS['PROFILE_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "No method");
}

switch($_GET['method']){
	case "edit":
		$data = access_check($GLOBALS['PROFILE_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['LOGGEDIN'], true);

		$rs = editProfile($data);
		
		$success_msg = "Edit laew woi~";
		break;
		
	case "get":
		access_check($GLOBALS['PROFILE_PREFIX']);

		if(screenData($_GET, [ "+int*" => "member_id" ])){
			$member_id = $_GET['member_id'];
		}
		else{
			$member_id = $_SESSION['member_id'];
		}

		$rs = getProfile($member_id);

		$success_msg = "Ow pai!";
		break;

	default:
		reject($GLOBALS['PROFILE_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success($GLOBALS['PROFILE_PREFIX'], $success_msg);

?>
