<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/map_func.php");

access_check($__MAP_PREFIX, $__ACCESS_ADMIN, true);

$rs = editMap($_POST['data']);
set_response($rs);
success($__MAP_PREFIX, "Edit laew woi~");

?>
