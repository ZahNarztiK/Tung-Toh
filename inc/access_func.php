<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}
if(!isset($_SESSION)){
	session_start();
}

require_once("../../inc/db_connect.php");
require_once("../../inc/init_response_func.php");

$__ACCESS_LOGGEDIN = 0;
$__ACCESS_VERIFIED = 1;
$__ACCESS_OWNER = 2;
$__ACCESS_ADMIN = 9;

function access_check($prefix, $previlege = 0, $DataRequired = false, $options = []){
	if(!isset($_SESSION['verified'])){
		reject($prefix, "99", "Login gon ai sus!!!");
	}
	if($_SESSION['verified'] < $previlege){
		reject($prefix, "90", "Access denied, eiei olo.");
	}
	if($DataRequired && !isset($_POST['data'])){
		reject($prefix, "04", "No data KUYKUYKUYKUY!!!");
	}

	if($_SESSION['verified'] < $__ACCESS_ADMIN){
		foreach($options as $option) {
			switch($option){
				case "self":
					if($_SESSION['member_id'] != $previlege){
						reject($prefix, "90", "Access denied, eiei olo.");
					}
					break;
				case "owner":
					# code...
					break;
				default:
					# code...
					break;
			}
		}
	}
}

?>
