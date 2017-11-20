<?

session_start();

$_IN_SITE = true;
require_once("../../inc/init_response_func.php");

if(!isset($_SESSION['member_id'])){
	reject("IP99", "Login gon ai sus!!!");
}

require_once("../../inc/profile_func.php");

$member_id = $_SESSION['member_id'];
if(isset($_GET['member_id']) && !is_nan($_GET['member_id'])){
	$member_id = $_GET['member_id'];
}

$rs = getProfile($member_id);
set_response($rs);
success("IP", "Ow pai!");

?>