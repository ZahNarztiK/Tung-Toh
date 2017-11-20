<?

if(!isset($_SESSION))
	session_start();

$execute = !isset($in_site);
if($execute && !isset($_SESSION['member_id'])){
	reject("Login gon ai sus!!!");
}

$in_site = true;
require_once("../../inc/init_response_func.php");
require_once("../../inc/db_connect.php");

if($execute){
	$place_id = 0;
	if(isset($_POST['place_id']) && !is_nan($_POST['place_id']))
		$place_id = $_POST['place_id'];
	
	if($place_id == 0){
		reject("Global GET disabled.");
	}	
	else{	
		$rs = getPlace($place_id);
		set_response($rs);
		success("Ow pai!");
	}
}





function getPlace($place_id){
	try{
		global $db_pdo;

		$stmt = $db_pdo->prepare("SELECT * FROM place WHERE place_id = :place_id");
		$stmt->bindParam(':place_id', $place_id);
		$stmt->execute();
		
		if($stmt->rowCount() == 0)
			reject("No info.");
		
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);

		return $rs;
	}
	catch(PDOException $e){
		reject($e->getMessage());
	}
}

?>