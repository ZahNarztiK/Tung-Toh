<?

session_start();

$_IN_SITE = true;

$__REGISTER_PREFIX = "MR";

$_FUNC = "add_member";
require_once("../../inc/init_login_func.php");





function add_member(){
	global $__REGISTER_PREFIX;
	$prefix = $__REGISTER_PREFIX;

	try{
		global $DB_PDO;

		$email = $_POST['email'];
		$password = md5($_POST['password']);


		$stmt = $DB_PDO->prepare("SELECT member_id FROM member WHERE email = :email LIMIT 1");
		$stmt->bindParam(':email', $email);
		$stmt->execute();

		if($stmt->rowCount() > 0){
			reject($prefix, "15", "Mee samak ma laew wa' sorry ;p");
		}


		$stmt = $DB_PDO->prepare("INSERT INTO member (email, password, session_id) VALUES (:email, :password, :session_id)");
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':password', $password);
		$stmt->bindParam(':session_id', $session_id);
		$session_id = md5($email.$password);
		$stmt->execute();

		$member_id = $DB_PDO->lastInsertId();
		if($member_id == 0){
			reject($prefix, "19", "Registration failed.");
		}
		

		$rs =	[
					"member_id" => $member_id,
					"email" => $email,
					"session_id" => $session_id,
					"verified" => 0
				];

		set_session($rs);
		set_login_response();
		success($prefix, "Samuk dai la!");

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function info_check(){
	global $__REGISTER_PREFIX;
	$prefix = $__REGISTER_PREFIX;

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