<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/table_func.php");

access_check($__TABLE_PREFIX, $__ACCESS_ADMIN);

if(isset($_GET['table_id']) && !is_nan($_GET['table_id']) && $_GET['table_id'] > 0){
	$rs = removeTable($_GET['table_id']);
}
elseif (isset($_GET['map_id']) && !is_nan($_GET['map_id']) && $_GET['map_id'] > 0) {
	$rs = removeTableList("map_id", $_GET['map_id']);
}
elseif (isset($_GET['place_id']) && !is_nan($_GET['place_id']) && $_GET['place_id'] > 0) {
	$rs = removeTableList("place_id", $_GET['place_id']);
}
else{
	reject($__TABLE_PREFIX, "04", "Table/Map ID/Place ID????");
}

set_response($rs);
success($__TABLE_PREFIX, "Lob la na jaa!");

?>
