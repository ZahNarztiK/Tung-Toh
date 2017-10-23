<?

session_start();

$in_site = true;

$func = "add_member";
require_once("../../inc/init_login_func.php");





function add_member(){
	try{
		global $db_pdo;
		$email = $_POST['email'];
		$password = md5($_POST['password']);

		$stmt = $db_pdo->prepare("SELECT member_id FROM member WHERE email = :email");
		$stmt->bindParam(':email', $email);
		$stmt->execute();
		if($stmt->rowCount()>0)
			reject("Mee samak ma laew wa' sorry ;p");

		$stmt = $db_pdo->prepare("INSERT INTO member (email, password, session_id) VALUES (:email, :password, :session_id)");
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':password', $password);
		$stmt->bindParam(':session_id', $session_id);
		$session_id = md5($email.$password);
		$stmt->execute();

		$stmt = $db_pdo->prepare("SELECT member_id FROM member WHERE email = :email");
		$stmt->bindParam(':email', $email);
		$stmt->execute();
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		$rs =	[
					"email" => $email,
					"session_id" => $session_id
				]
				+ $rs;

		set_session($rs);
		set_login_response();
		success("Samuk dai la!");

		return $rs;
	}
	catch(PDOException $e){
		reject($e->getMessage());
	}
}

function info_check(){
	global $response;

	if(!isset($_POST['email'])||!isset($_POST['password']))
		reject("Kor moon mai krob, ai kuy!!!");

	if(!valid_email($_POST['email']))
		reject("Email pid, ai kwai");

	if(!valid_password($_POST['email']))
		reject("Mai me pass, ai har");
}

?>