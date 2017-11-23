<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/place_func.php");

access_check($__PLACE_PREFIX, $__ACCESS_ADMIN);

if(!isset($_GET['place_id']) || is_nan($_GET['place_id']) || $_GET['place_id'] <= 0){
	reject($__PLACE_PREFIX, "04", "Place ID????");
}

$rs = removePlace($_GET['place_id']);
set_response($rs);
success($__PLACE_PREFIX, "Lob la na jaa!");

?>
