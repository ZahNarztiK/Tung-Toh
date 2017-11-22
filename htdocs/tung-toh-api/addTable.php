<?

session_start();

$_IN_SITE = true;
require_once("../../inc/table_func.php");

if(!isset($_SESSION['member_id'])){
	reject($__TABLE_PREFIX, "99", "Login gon ai sus!!!");
}
if(!isset($_POST['data'])){
	reject($__TABLE_PREFIX, "04", "No data KUYKUYKUYKUY!!!");
}

$rs = addTable($_POST['data']);
set_response($rs);
success($__TABLE_PREFIX, "Ow pai!");

?>