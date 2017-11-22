<?

session_start();

$_IN_SITE = true;
require_once("../../inc/place_func.php");

if(!isset($_SESSION['member_id'])){
	reject($__PLACE_PREFIX, "99", "Login gon ai sus!!!");
}
if(!isset($_POST['data'])){
	reject($__PLACE_PREFIX, "04", "No data KUYKUYKUYKUY!!!");
}

$rs = addPlace($_POST['data']);
set_response($rs);
success($__PLACE_PREFIX, "Ow pai!");

?>
