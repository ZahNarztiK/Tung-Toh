<?

session_start();

$_IN_SITE = true;
require_once("../../inc/access_func.php");
require_once("../../inc/place_func.php");

access_check($__PLACE_PREFIX, true, true, true);

$rs = addPlace($_POST['data']);
set_response($rs);
success($__PLACE_PREFIX, "Ow pai!");

?>
