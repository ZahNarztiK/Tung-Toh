<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/PHPMailer/init_PHPMailer.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$GLOBALS['FORGET_PREFIX'] = "MF";

$__FORGET_EMAIL = [
	"mode" => "ncp",
	"ncp" => [
		"server" => "mail.nc-production.net",
		"port" => 25,
		"user" => "tung-toh@nc-production.net",
		"password" => "6284629Kuy"
	],
	"gmail" => [
		"server" => "smtp.gmail.com",
		"port" => 587,
		"user" => "tungtoh.app@gmail.com",
		"password" => "6284629Kuy"
	],
	"sender_name" => "Tung-Toh Team",
	"sender_email" => "tung-toh-noreply@nc-production.net",
	"topic" => "[Tung-Toh] Reset Password",
	"apiprefix" => "http://nc-production.net/tung-toh/kuyaraisukyang.php?"
];

$__FORGET_SMS = [];



function forgetPassword($forgetpwd_raw){
	global $__FORGET_EMAIL;
	$prefix = $GLOBALS['FORGET_PREFIX'];

	init_send_response();

	try{
		global $DB_PDO;

		
		$forgetpwd = forget_infoCheck($forgetpwd_raw);

		$email = $forgetpwd['email'];
		

		$stmt = $DB_PDO->prepare("SELECT member_id, firstname, lastname FROM member WHERE email = :email LIMIT 1");
		$stmt->bindParam(':email', $email);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "MAI MEE EMAIL NEE WOIII, kuy eiei!!!");
		}
		$info = $stmt->fetch(PDO::FETCH_ASSOC);


		$code_expired = date("Y-m-d H:i:s", strtotime("+1 week"));
		$hash = md5($email);
		$hash .= md5($hash.time());
		$hash .= md5($code_expired.$hash);
		$code = base_convert($hash, 16, 36);
		

		$stmt = $DB_PDO->prepare("UPDATE member SET forget_code = :forget_code, forget_code_expired = :forget_code_expired WHERE member_id = :member_id");
		$stmt->bindParam(':forget_code', $code);
		$stmt->bindParam(':forget_code_expired', $code_expired);
		$stmt->bindParam(':member_id', $info['member_id'], PDO::PARAM_INT);
		$stmt->execute();
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}


	if($info['firstname'] != ""){
		$name = $info['firstname'];
		if($info['lastname'] != ""){
			$name .= $info['lastname'];
		}
	}
	else{
		$name = split("@", $email)[0];
	}

	$apiparam = [
		"email=$email",
		"code=$code"
	];
	$api = $__FORGET_EMAIL['apiprefix'].implode("&", $apiparam);

	$emailMSG = [];
	$emailMSG[] = "D $name,";
	$emailMSG[] = "";
	$emailMSG[] = "God di sus";
	$emailMSG[] = "v";
	$emailMSG[] = "v";
	$emailMSG[] = "v";
	$emailMSG[] = "<a href=\"$api\"><img src=\"cid:lorsus\" alt=\"yeah\"></a>";
	$msg = implode("</br>", $emailMSG);
	

	$mail = new PHPMailer(true);
	try {
		$mail->isSMTP();
		$mail->SMTPDebug = 0;

		
		$mail->Host = $__FORGET_EMAIL[$__FORGET_EMAIL['mode']]['server'];
		$mail->Port = $__FORGET_EMAIL[$__FORGET_EMAIL['mode']]['port'];

		$mail->SMTPAuth = true;
		$mail->Username = $__FORGET_EMAIL[$__FORGET_EMAIL['mode']]['user'];
		$mail->Password = $__FORGET_EMAIL[$__FORGET_EMAIL['mode']]['password'];;
		$mail->SMTPSecure = "tls";
		$mail->SMTPOptions = [
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			]
		];
		
		$mail->setFrom($__FORGET_EMAIL['sender_email'], $__FORGET_EMAIL['sender_name']);
		$mail->addReplyTo($__FORGET_EMAIL['sender_email'], $__FORGET_EMAIL['sender_name']);
		$mail->addAddress($email, $name);

		$mail->Subject = $__FORGET_EMAIL['topic'];
		$mail->isHTML(true);
		$mail->Body = $msg;

		$mail->addEmbeddedImage("../../inc/email/eiei.jpg", "lorsus");
		
		$status = $mail->send();

		if(!$status){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_FAILED'], $mail->ErrorInfo);
		}

		$rs = [
			"status" => true
		];

		return $rs;
	}catch(Exception $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_FAILED'], $e->getMessage());
	}catch(\Exception $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_FAILED'], $e->getMessage());
	}
}

function forget_infoCheck($forgetpwd_raw){
	$prefix = $GLOBALS['FORGET_PREFIX'];

	$forgetpwd = prepareJSON($prefix, $forgetpwd_raw);


	if(!isset($forgetpwd['email']) || !valid_email($forgetpwd['email'])){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Song email ma dd noi.");
	}


	return $forgetpwd;
}

?>
