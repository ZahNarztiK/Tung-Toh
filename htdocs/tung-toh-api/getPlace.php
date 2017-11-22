<?

session_start();

$_IN_SITE = true;
require_once("../../inc/place_func.php");

if(!isset($_SESSION['member_id'])){
	reject($__PLACE_PREFIX, "99", "Login gon ai sus!!!");
}
if(!isset($_GET['place_id']) || is_nan($_GET['place_id']) || $_GET['place_id'] <= 0){
	reject($__PLACE_PREFIX, "04", "Place ID????");
}

$rs = getPlace($_GET['place_id']);
set_response($rs);
success($__PLACE_PREFIX, "Ow pai!");

?>
