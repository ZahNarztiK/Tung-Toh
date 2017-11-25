<?

if(!isset($_SESSION)){
	session_start();
}

$execute = !isset($in_site);
if($execute && !isset($_SESSION['member_id'])){
	reject("Login gon ai sus!!!");
}

$in_site = true;
require_once("../../inc/init_response_func.php");
require_once("../../inc/db_connect.php");

if($execute){
	$event_id = 0;
	if(isset($_GET['event_id']) && !is_nan($_GET['event_id'])){
		$event_id = $_GET['event_id'];
	}
	
	if($event_id == 0){
		reject("Global GET disabled.");
	}
	else{
		$rs = getEvent($event_id);
		set_response($rs);
		success("Ow pai!");
	}
}





function getEvent($event_id){
	try{
		global $db_pdo;

		$stmt = $db_pdo->prepare("SELECT * FROM event WHERE event_id = :event_id");
		$stmt->bindParam(':event_id', $event_id);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject("No info.");
		}
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);

		return $rs;
	}
	catch(PDOException $e){
		reject($e->getMessage());
	}
}

?>