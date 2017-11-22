<?

session_start();

$_IN_SITE = true;

$__LOGIN_PREFIX = "ML";

$_FUNC = "login";
require_once("../../inc/init_login_func.php");





function login(){
	global $__LOGIN_PREFIX;
	$prefix = $__LOGIN_PREFIX;

	try{
		global $DB_PDO;

		$email = $_POST['email'];
		$password = md5($_POST['password']);
		

		$stmt = $DB_PDO->prepare("SELECT member_id, email, session_id FROM member WHERE email = :email and password = :password LIMIT 1");
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':password', $password);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Login pid, ai kuy!!!");
		}
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);


		if($rs["session_id"] == ""){
			$stmt = $DB_PDO->prepare("UPDATE member SET session_id = :session_id WHERE member_id = :member_id");
			$stmt->bindParam(':member_id', $rs['member_id'], PDO::PARAM_INT);
			$stmt->bindParam(':session_id', $rs['session_id']);
			$rs['session_id'] = md5($email.$password);
			$stmt->execute();
		}
		
		
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
	global $__LOGIN_PREFIX;
	$prefix = $__LOGIN_PREFIX;

	if(!isset($_POST['email']) || !isset($_POST['password'])){
		reject($prefix, "04", "Kor moon mai krob, ai kuy!!!");
	}

	if(!valid_email($_POST['email'])){
		reject($prefix, "04", "Email pid, ai kwai");
	}

	if(!valid_password($_POST['email'])){
		reject($prefix, "04", "Mai me pass, ai har");
	}
}

?>