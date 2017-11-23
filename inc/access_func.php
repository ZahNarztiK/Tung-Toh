<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}
if(!isset($_SESSION)){
	session_start();
}

require_once("../../inc/init_response_func.php");

function access_check($prefix, $isLogin = true, $isAdmin = false, $DataRequired = false){
	if($isLogin && !isset($_SESSION['member_id'])){
		reject($prefix, "99", "Login gon ai sus!!!");
	}
	if($isAdmin && (!isset($_SESSION['verified']) || $_SESSION['verified'] < 2)){
		reject($prefix, "99", "Access denied, eiei olo.");
	}
	if($DataRequired && !isset($_POST['data'])){
		reject($prefix, "04", "No data KUYKUYKUYKUY!!!");
	}
}

?>
