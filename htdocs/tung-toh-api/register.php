<?
session_start();

$in_site = true;
$response = [
	"verified" => false,
	"message" => ""
];

info_check();
require_once("../inc/db_connect.php");
$info = add_member();
set_session($info);
success("Samuk dai la!");





function add_member(){
	try{
		global $db_pdo, $response;
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

function reject($message){
	global $response;

	$response['message'] = "Error: $message";
	die(json_encode($response));
}

function set_session($info){
	$_SESSION = $info + $_SESSION;
}

function success($message){
	global $response;
	$response =	[
					"verified" => true,
					"message" => $message,
					"session_id" => $_SESSION['session_id']
				] + $response;
	echo json_encode($response);
}

function valid_email($email){
	$pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
	return preg_match($pattern, $email);
}

function valid_password($password){
	return $password != "";
}

?>