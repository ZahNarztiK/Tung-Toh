<?

if(isset($in_site)){
	
}
else{
	session_start();
	$response = [];
	if(!isset($_SESSION['member_id']))
		reject("Login gon ai sus!!!");

	$in_site = true;

	require_once("../inc/db_connect.php");
	$response = getProfile();
	success("Login dai la!");
}






function getProfile(){
	try{
		global $db_pdo, $response;
		$member_id = $_SESSION['member_id'];

		$stmt = $db_pdo->prepare("SELECT firstname, lastname, level, points, profile_image, email FROM member WHERE member_id = :member_id");
		$stmt->bindParam(':member_id', $member_id);
		$stmt->execute();
		
		if($stmt->rowCount()==0)
			reject("No info.");
		
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $rs;
	}
	catch(PDOException $e){
		reject($e->getMessage());
	}
}

function reject($message){
	$response['message'] = "Error: $message";
	die(json_encode($response));
}

function success($message){
	global $response;
	/*$response =	[
					"verified" => true,
					"message" => $message,
					"session_id" => $_SESSION['session_id']
				] + $response;*/
	echo json_encode($response);
}

?>