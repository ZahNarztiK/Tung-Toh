<?

session_start();

$_IN_SITE = true;

$_FUNC = "verifySession";
require_once("../../inc/init_login_func.php");





function verifySession(){
	try{
		global $DB_PDO;
		
		$session_id = $_POST['session_id'];
		
		
		$stmt = $DB_PDO->prepare("SELECT TOP 1 member_id, email FROM member WHERE (session_id = :session_id)");
		$stmt->bindParam(':session_id', $session_id);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject("MS11", "Login session pid, ai kuy!!!");
		}
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		$rs['session_id'] = $session_id;
		
		
		set_session($rs);
		set_login_response();
		success("MS", "Login dai la!");
		
		return $rs;
	}
	catch(PDOException $e){
		reject("MS10", $e->getMessage());
	}
}

function info_check(){
	if(!isset($_POST['session_id'])){
		reject("MS04", "Session la', ai juy??");
	}
}

?>