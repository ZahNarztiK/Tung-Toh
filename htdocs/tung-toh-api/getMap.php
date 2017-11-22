<?

session_start();

$_IN_SITE = true;
require_once("../../inc/map_func.php");

if(!isset($_SESSION['member_id'])){
	reject($__MAP_PREFIX, "99", "Login gon ai sus!!!");
}

if(isset($_GET['map_id']) && !is_nan($_GET['map_id']) && $_GET['map_id'] > 0){
	$rs = getMap($_GET['map_id']);
}
elseif (isset($_GET['place_id']) && !is_nan($_GET['place_id']) && $_GET['place_id'] > 0) {
	$rs = getMapList($_GET['place_id']);
}
else{
	reject($__MAP_PREFIX, "04", "Map/Place ID????");
}

set_response($rs);
success($__MAP_PREFIX, "Ow pai!");

?>
