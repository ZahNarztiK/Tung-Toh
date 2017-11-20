<?

session_start();
$logged = isset($_SESSION['member_id']);

session_unset();
session_destroy();

$_IN_SITE = true;
require_once("../../inc/init_response_func.php");

set_response([
	"logged_out" => $logged
]);
success("ML", "Session cleared!");

?>