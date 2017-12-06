<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/profile_func.php");


if(!isset($_GET['method'])){
	reject($GLOBALS['PROFILE_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "No method");
}

switch($_GET['method']){
	case "edit":
		access_check($GLOBALS['PROFILE_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['LOGGEDIN'], true);

		$data = data_check($GLOBALS['PROFILE_PREFIX']);
		$rs = editProfile($data);
		
		$success_msg = "Edit laew woi~";
		break;
	case "get":
		access_check($GLOBALS['PROFILE_PREFIX']);

		$member_id = $_SESSION['member_id'];
		if(isset($_GET['member_id']) && isPositiveInt($_GET['member_id'])){
			$member_id = $_GET['member_id'];
		}

		$rs = getProfile($member_id);

		$success_msg = "Ow pai!";
		break;
	//case "remove":
	//	access_check($GLOBALS['PROFILE_PREFIX'], $GLOBALS['ACCESS_CONSTANT']['ADMIN']);

	//	if(!isset($_GET['member_id']) || is_nan($_GET['member_id']) || $_GET['member_id'] <= 0){
	//		reject($GLOBALS['PROFILE_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Member ID????");
	//	}

	//	$rs = removeProfile($_GET['member_id']);

	//	$success_msg = "Lob la na jaa!";
	//	break;
	default:
		reject($GLOBALS['PROFILE_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success($GLOBALS['PROFILE_PREFIX'], $success_msg);

?>
