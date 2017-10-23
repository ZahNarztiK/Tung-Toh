<?

session_start();

$in_site = true;

$func = "verifySession";
require_once("../../inc/init_login_func.php");





function verifySession(){
	try{
		global $db_pdo;
		$session_id = $_POST['session_id'];

		$stmt = $db_pdo->prepare("SELECT member_id, email FROM member WHERE (session_id = :session_id)");
		$stmt->bindParam(':session_id', $session_id);
		$stmt->execute();
		if($stmt->rowCount()==0)
			reject("Login session pid, ai kuy!!!");
		
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		$rs['session_id'] = $session_id;
		
		set_session($rs);
		set_login_response();
		success("Login dai la!");

		return $rs;
	}
	catch(PDOException $e){
		reject($e->getMessage());
	}
}

function info_check(){
	if(!isset($_POST['session_id']))
		reject("Session la', ai juy??");
}

?>