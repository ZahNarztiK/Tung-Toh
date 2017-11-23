<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/profile_func.php");

access_check($__PROFILE_PREFIX);

$member_id = $_SESSION['member_id'];
if(isset($_GET['member_id']) && !is_nan($_GET['member_id'])){
	$member_id = $_GET['member_id'];
}

$rs = getProfile($member_id);
set_response($rs);
success($__PROFILE_PREFIX, "Ow pai!");

?>
