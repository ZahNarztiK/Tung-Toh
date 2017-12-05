<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

$__RESETPWD_PREFIX = "MP";



function resetPassword($resetpwd_raw){
	global $__RESETPWD_PREFIX;
	$prefix = $__RESETPWD_PREFIX;

	init_send_response(0);

	try{
		global $DB_PDO;

		
		$resetpwd = resetPwd_infoCheck($resetpwd_raw);

		$email = $resetpwd['email'];
		$code = $resetpwd['code'];
		$now = date("Y-m-d H:i:s");
		echo $now;
		
		$stmt = $DB_PDO->prepare("SELECT member_id FROM member WHERE email = :email AND forget_code = :forget_code AND forget_code_expired >= :forget_code_expired LIMIT 1");
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':forget_code', $code);
		$stmt->bindParam(':forget_code_expired', $now);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "MAI HAI PLEAN WOI FUCKFUCKFUCK!!!!");
		}


		if(!isset($resetpwd['password'])){
			$status = 1;
		}
		else{
			$member_id = $stmt->fetchColumn();

			$password = md5($resetpwd['password']);
			$session_id = md5($email.$password);
			
			$stmt = $DB_PDO->prepare("UPDATE member SET password = :password, forget_code = '', forget_code_expired = NULL, session_id = :session_id WHERE member_id = :member_id");
			$stmt->bindParam(':password', $password);
			$stmt->bindParam(':session_id', $session_id);
			$stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
			$stmt->execute();

			$status = 2;
		}


		$rs = [
			"status" => $status
		];
		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function resetPwd_infoCheck($resetpwd_raw){
	global $__RESETPWD_PREFIX;
	$prefix = $__RESETPWD_PREFIX;

	$resetpwd = prepareJSON($prefix, $resetpwd_raw);

	
	if(!isset($resetpwd['email']) || !valid_email($resetpwd['email'])){
		reject($prefix, "04", "Song email ma dd noi.");
	}
	if(!isset($resetpwd['code']) || $resetpwd['code'] == ""){
		reject($prefix, "04", "Code la kub ai sas?");
	}
	if(isset($resetpwd['password']) && !valid_password($resetpwd['password'])){
		reject($prefix, "04", "Password kak sus.");
	}


	return $resetpwd;
}

?>
