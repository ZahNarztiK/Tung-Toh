<?

session_start();

$in_site = true;

$func = "login";
require_once("../../inc/init_login_func.php");





function login(){
	try{
		global $db_pdo;
		$email = $_POST['email'];
		$password = md5($_POST['password']);
		
		$stmt = $db_pdo->prepare("SELECT member_id, session_id FROM member WHERE email = :email and password = :password");
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':password', $password);
		$stmt->execute();
		if($stmt->rowCount()==0)
			reject("Login pid, ai kuy!!!");
		
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		$rs['email'] = $email;

		if($rs["session_id"] == ""){
			$stmt = $db_pdo->prepare("UPDATE member SET session_id = :session_id WHERE member_id = :member_id");
			$stmt->bindParam(':member_id', $rs['member_id']);
			$stmt->bindParam(':session_id', $rs['session_id']);
			$rs['session_id'] = md5($email.$password);
			$stmt->execute();
		}
		
		set_session($rs);
		set_login_response();
		success("Login dai la!");

		return $rs;
	}
	catch(PDOException $e){
		reject($e->getMessage());
	}
}

function info_check(){
	if(!isset($_POST['email'])||!isset($_POST['password']))
		reject("Kor moon mai krob, ai kuy!!!");

	if(!valid_email($_POST['email']))
		reject("Email pid, ai kwai");

	if(!valid_password($_POST['email']))
		reject("Mai me pass, ai har");
}

?>