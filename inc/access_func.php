<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}
if(!isset($_SESSION)){
	session_start();
}

require_once("../../inc/init_response_func.php");

$__ACCESS_CONSTANT = [
	"LOGGEDIN" => 0,
	"VERIFIED" => 1,
	"ADMIN" => 9
];

function access_check($prefix, $previlege = 0, $DataRequired = false){
	if(!isset($_SESSION['verified'])){
		reject($prefix, "99", "Login gon ai sus!!!");
	}
	if($_SESSION['verified'] < $previlege){
		reject($prefix, "90", "Access denied, eiei olo.");
	}
	if($DataRequired && !isset($_POST['data'])){
		reject($prefix, "04", "No data KUYKUYKUYKUY!!!");
	}
}

?>
