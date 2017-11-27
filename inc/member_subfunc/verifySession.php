<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

$__VERIFYSESSION_PREFIX = "MS";



function verifySession(){
	global $__VERIFYSESSION_PREFIX;
	$prefix = $__VERIFYSESSION_PREFIX;

	try{
		global $DB_PDO;
		

		verifySession_infoCheck();

		$session_id = $_POST['session_id'];
		

		$stmt = $DB_PDO->prepare("SELECT member_id, email, verified FROM member WHERE (session_id = :session_id) LIMIT 1");
		$stmt->bindParam(':session_id', $session_id);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "11", "Login session pid, ai kuy!!!");
		}
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		$rs['session_id'] = $session_id;
		
		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function verifySession_infoCheck(){
	global $__VERIFYSESSION_PREFIX;
	$prefix = $__VERIFYSESSION_PREFIX;

	if(!isset($_POST['session_id'])){
		reject($prefix, "04", "Session la', ai juy??");
	}
}

?>
