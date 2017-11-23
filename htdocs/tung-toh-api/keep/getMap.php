<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/map_func.php");

access_check($__MAP_PREFIX);

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
