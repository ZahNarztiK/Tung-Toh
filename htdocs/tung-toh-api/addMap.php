<?

session_start();

$_IN_SITE = true;
require_once("../../inc/init_response_func.php");

if(!isset($_SESSION['member_id'])){
	reject("IT99", "Login gon ai sus!!!");
}

require_once("../../inc/map_func.php");

$rs = addMap($_POST);
set_response($rs);
success("IT", "Ow pai!");

?>