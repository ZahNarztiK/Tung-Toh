<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}
if(!isset($_SESSION)){
	session_start();
}

$logged = isset($_SESSION['verified']);

session_unset();
session_destroy();

require_once("../../inc/init_response_func.php");

set_response([
	"logged_out" => $logged
]);
success("ML", "Session cleared!");

?>