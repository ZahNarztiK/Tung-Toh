<?

session_start();

$_IN_SITE = true;

$_FUNC = "login";
require_once("../../inc/init_login_func.php");





function login(){
	try{
		global $DB_PDO;

		$email = $_POST['email'];
		$password = md5($_POST['password']);
		
		$stmt = $DB_PDO->prepare("SELECT member_id, session_id FROM member WHERE email = :email and password = :password");
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':password', $password);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject("ML14", "Login pid, ai kuy!!!");
		}
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		$rs['email'] = $email;

		if($rs["session_id"] == ""){
			$stmt = $DB_PDO->prepare("UPDATE member SET session_id = :session_id WHERE member_id = :member_id");
			$stmt->bindParam(':member_id', $rs['member_id']);
			$stmt->bindParam(':session_id', $rs['session_id']);
			$rs['session_id'] = md5($email.$password);
			$stmt->execute();
		}
		
		set_session($rs);
		set_login_response();
		success("ML", "Login dai la!");

		return $rs;
	}
	catch(PDOException $e){
		reject("ML10", $e->getMessage());
	}
}

function info_check(){
	if(!isset($_POST['email']) || !isset($_POST['password'])){
		reject("ML04", "Kor moon mai krob, ai kuy!!!");
	}

	if(!valid_email($_POST['email'])){
		reject("ML04", "Email pid, ai kwai");
	}

	if(!valid_password($_POST['email'])){
		reject("ML04", "Mai me pass, ai har");
	}
}

?>