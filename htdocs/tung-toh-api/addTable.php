<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/table_func.php");

access_check($__TABLE_PREFIX, $__ACCESS_ADMIN, true);

$rs = addTable($_POST['data']);
set_response($rs);
success($__TABLE_PREFIX, "Add hai la!");

?>