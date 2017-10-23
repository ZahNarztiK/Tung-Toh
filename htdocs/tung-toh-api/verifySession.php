<?
session_start();

$in_site = true;
$response = [
	"verified" => false,
	"message" => ""
];

info_check();
require_once("../inc/db_connect.php");
$info = verifySession();
set_session($info);
success("Login dai la!");





function info_check(){
	if(!isset($_POST['session_id']))
		reject("Session la', ai juy??");
}

function verifySession(){
	try{
		global $db_pdo, $response;
		$session_id = $_POST['session_id'];

		$stmt = $db_pdo->prepare("SELECT member_id, email FROM member WHERE (session_id = :session_id)");
		$stmt->bindParam(':session_id', $session_id);
		$stmt->execute();
		if($stmt->rowCount()==0)
			reject("Login session pid, ai kuy!!!");
		
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		$rs['session_id'] = $session_id;
		
		return $rs;
	}
	catch(PDOException $e){
		reject($e->getMessage());
	}
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

function valid_session_id($session_id){
	return $session_id != "";
}

?>