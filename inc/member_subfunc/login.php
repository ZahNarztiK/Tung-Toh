<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

$GLOBALS['LOGIN_PREFIX'] = "ML";



function login($login_raw){
	$prefix = $GLOBALS['LOGIN_PREFIX'];

	try{
		global $DB_PDO;

		
		$login = login_infoCheck($login_raw);

		$email = $login['email'];
		$password = md5($login['password']);
		

		$stmt = $DB_PDO->prepare("SELECT member_id, email, session_id, status FROM member WHERE email = :email AND password = :password LIMIT 1");
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':password', $password);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Login pid, ai kuy!!!");
		}
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);


		if($rs["session_id"] == ""){
			$stmt = $DB_PDO->prepare("UPDATE member SET session_id = :session_id WHERE member_id = :member_id");
			$stmt->bindParam(':member_id', $rs['member_id'], PDO::PARAM_INT);
			$stmt->bindParam(':session_id', $rs['session_id']);
			$rs['session_id'] = md5($email.$password);
			$stmt->execute();
		}
		
		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function login_infoCheck($login_raw){
	$prefix = $GLOBALS['LOGIN_PREFIX'];

	$login = prepareJSON($prefix, $login_raw);


	if(!isset($login['email']) || !isset($login['password'])){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Kor moon mai krob, ai kuy!!!");
	}

	if(!valid_email($login['email'])){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Email pid, ai kwai");
	}

	if(!valid_password($login['password'])){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Mai me pass, ai har");
	}


	return $login;
}

?>
