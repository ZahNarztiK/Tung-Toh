<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

$GLOBALS['REGISTER_PREFIX'] = "MR";



function add_member($member_raw){
	$prefix = $GLOBALS['REGISTER_PREFIX'];

	try{
		global $DB_PDO;

		
		$member = register_infoCheck($member_raw);

		$email = $member['email'];
		$password = md5($member['password']);


		$stmt = $DB_PDO->prepare("SELECT member_id FROM member WHERE email = :email LIMIT 1");
		$stmt->bindParam(':email', $email);
		$stmt->execute();

		if($stmt->rowCount() > 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_DUPLICATED'], "Mee samak ma laew wa' sorry ;p");
		}


		$stmt = $DB_PDO->prepare("INSERT INTO member (email, password, session_id) VALUES (:email, :password, :session_id)");
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':password', $password);
		$stmt->bindParam(':session_id', $session_id);
		$session_id = md5($email.$password);
		$stmt->execute();

		$member_id = $DB_PDO->lastInsertId();
		if($member_id == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_FAILED'], "Registration failed.");
		}
		

		$rs =	[
					"member_id" => $member_id,
					"email" => $email,
					"session_id" => $session_id,
					"status" => 0
				];

		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function register_infoCheck($member_raw){
	$prefix = $GLOBALS['REGISTER_PREFIX'];

	$member = prepareJSON($prefix, $member_raw);


	if(!isset($member['email']) || !isset($member['password'])){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Kor moon mai krob, ai kuy!!!");
	}

	if(!valid_email($member['email'])){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Email pid, ai kwai");
	}

	if(!valid_password($member['password'])){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Mai me pass, ai har");
	}


	return $member;
}

?>
