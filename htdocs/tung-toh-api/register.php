<?

session_start();

$_IN_SITE = true;

$_FUNC = "add_member";
require_once("../../inc/init_login_func.php");





function add_member(){
	try{
		global $DB_PDO;

		$email = $_POST['email'];
		$password = md5($_POST['password']);

		$stmt = $DB_PDO->prepare("SELECT member_id FROM member WHERE email = :email");
		$stmt->bindParam(':email', $email);
		$stmt->execute();

		if($stmt->rowCount() > 0){
			reject("MR15", "Mee samak ma laew wa' sorry ;p");
		}

		$stmt = $DB_PDO->prepare("INSERT INTO member (email, password, session_id) VALUES (:email, :password, :session_id)");
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':password', $password);
		$stmt->bindParam(':session_id', $session_id);
		$session_id = md5($email.$password);
		$stmt->execute();

		$member_id = $DB_PDO->lastInsertId();
		if($member_id == 0){
			reject("MR19", "Registration failed.");
		}
		
		$rs =	[
					"member_id" => $member_id,
					"email" => $email,
					"session_id" => $session_id
				];

		set_session($rs);
		set_login_response();
		success("MR", "Samuk dai la!");

		return $rs;
	}
	catch(PDOException $e){
		reject("MR10", $e->getMessage());
	}
}

function info_check(){
	if(!isset($_POST['email']) || !isset($_POST['password'])){
		reject("MR04", "Kor moon mai krob, ai kuy!!!");
	}

	if(!valid_email($_POST['email'])){
		reject("MR04", "Email pid, ai kwai");
	}

	if(!valid_password($_POST['email'])){
		reject("MR04", "Mai me pass, ai har");
	}
}

?>