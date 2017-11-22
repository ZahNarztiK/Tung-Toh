<?

session_start();

$_IN_SITE = true;

$__VERIFYSESSION_PREFIX = "MS";

$_FUNC = "verifySession";
require_once("../../inc/init_login_func.php");





function verifySession(){
	global $__VERIFYSESSION_PREFIX;
	$prefix = $__VERIFYSESSION_PREFIX;

	try{
		global $DB_PDO;
		
		$session_id = $_POST['session_id'];
		
		
		$stmt = $DB_PDO->prepare("SELECT member_id, email FROM member WHERE (session_id = :session_id) LIMIT 1");
		$stmt->bindParam(':session_id', $session_id);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "11", "Login session pid, ai kuy!!!");
		}
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		$rs['session_id'] = $session_id;
		
		
		set_session($rs);
		set_login_response();
		success($prefix, "Login dai la!");
		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function info_check(){
	global $__VERIFYSESSION_PREFIX;
	$prefix = $__VERIFYSESSION_PREFIX;

	if(!isset($_POST['session_id'])){
		reject($prefix, "04", "Session la', ai juy??");
	}
}

?>