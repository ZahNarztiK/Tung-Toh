<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

$__FORGET_PREFIX = "MF";



function forgetPassword(){
	global $__FORGET_PREFIX;
	$prefix = $__FORGET_PREFIX;

	init_send_response();

	try{
		global $DB_PDO;

		
		forget_infoCheck();

		$email = $_POST['email'];
		

		$stmt = $DB_PDO->prepare("SELECT member_id FROM member WHERE email = :email LIMIT 1");
		$stmt->bindParam(':email', $email);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "MAI MEE EMAIL NEE WOIII, kuy eiei!!!");
		}
		$member_id = $stmt->fetchColumn();


		$code = md5($email.time()).time();
		$code_expired = date("Y-m-d H:i:s", strtotime("+1 week"));
		

		$stmt = $DB_PDO->prepare("UPDATE member SET forget_code = :forget_code, forget_code_expired = :forget_code_expired WHERE member_id = :member_id");
		$stmt->bindParam(':forget_code', $code);
		$stmt->bindParam(':forget_code_expired', $code_expired);
		$stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
		$stmt->execute();


		//send email + verification code


		$rs = [
			"status" => true,
			"temp_info" => [		//will be removed
				"email" => $email,
				"code" => $code
			]
		];
		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function forget_infoCheck(){
	global $__FORGET_PREFIX;
	$prefix = $__FORGET_PREFIX;

	if(!isset($_POST['email']) || !valid_email($_POST['email'])){
		reject($prefix, "04", "Song email ma dd noi.");
	}
}

?>
