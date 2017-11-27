<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/profile_func.php");


if(!isset($_GET['method'])){
	reject($__PROFILE_PREFIX, "04", "No method");
}

switch($_GET['method']){
	case "get":
		access_check($__PROFILE_PREFIX);

		$member_id = $_SESSION['member_id'];
		if(isset($_GET['member_id']) && isPositiveInt($_GET['member_id'])){
			$member_id = $_GET['member_id'];
		}

		$rs = getProfile($member_id);

		$success_msg = "Ow pai!";
		break;
	//case "remove":
	//	access_check($__PROFILE_PREFIX, $__ACCESS_CONSTANT['ADMIN']);

	//	if(!isset($_GET['member_id']) || is_nan($_GET['member_id']) || $_GET['member_id'] <= 0){
	//		reject($__PROFILE_PREFIX, "04", "Member ID????");
	//	}

	//	$rs = removeProfile($_GET['member_id']);

	//	$success_msg = "Lob la na jaa!";
	//	break;
	default:
		reject($__PROFILE_PREFIX, "04", "Method KUY RAI SUS!!?!??!?");
		break;
}

set_response($rs);
success($__PROFILE_PREFIX, $success_msg);

?>
