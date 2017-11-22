<?

session_start();

$_IN_SITE = true;
require_once("../../inc/table_func.php");

if(!isset($_SESSION['member_id'])){
	reject($__TABLE_PREFIX, "99", "Login gon ai sus!!!");
}

if(isset($_GET['table_id']) && !is_nan($_GET['table_id']) && $_GET['table_id'] > 0){
	$rs = getTable($_GET['table_id']);
}
elseif (isset($_GET['map_id']) && !is_nan($_GET['map_id']) && $_GET['map_id'] > 0) {
	$rs = getTableList($_GET['map_id']);
}
else{
	reject($__TABLE_PREFIX, "04", "Table/Map ID????");
}

set_response($rs);
success($__TABLE_PREFIX, "Ow pai!");

?>
