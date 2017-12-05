<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

$__VERIFYSESSION_PREFIX = "MS";



function verifySession($session_raw){
	global $__VERIFYSESSION_PREFIX;
	$prefix = $__VERIFYSESSION_PREFIX;

	try{
		global $DB_PDO;
		

		$session = verifySession_infoCheck($session_raw);

		$session_id = $session['session_id'];
		

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

function verifySession_infoCheck($session_raw){
	global $__VERIFYSESSION_PREFIX;
	$prefix = $__VERIFYSESSION_PREFIX;

	$session = prepareJSON($prefix, $session_raw);


	if(!isset($session['session_id'])){
		reject($prefix, "04", "Session la', ai juy??");
	}


	return $session;
}

?>
