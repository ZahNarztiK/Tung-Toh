<?

session_start();

$_IN_SITE = true;
require_once("../../inc/init_response_func.php");

if(!isset($_SESSION['member_id'])){
	reject("IT99", "Login gon ai sus!!!");
}

require_once("../../inc/table_func.php");

$rs = addTable($_POST);
set_response($rs);
success("IT", "Ow pai!");

?>