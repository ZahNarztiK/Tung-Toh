<?

session_start();

$_IN_SITE = true;
require_once("../../inc/map_func.php");

if(!isset($_SESSION['member_id'])){
	reject($__MAP_PREFIX, "99", "Login gon ai sus!!!");
}
if(!isset($_POST['data'])){
	reject($__MAP_PREFIX, "04", "No data KUYKUYKUYKUY!!!");
}

$rs = addMap($_POST['data']);
set_response($rs);
success($__MAP_PREFIX, "Ow pai!");

?>
