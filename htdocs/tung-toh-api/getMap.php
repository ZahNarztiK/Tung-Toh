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
	$map_id = 0;
	if(isset($_GET['map_id']) && !is_nan($_GET['map_id'])){
		$map_id = $_GET['map_id'];
	}
	
	if($map_id == 0){
		if(isset($_GET['place_id']) && !is_nan($_GET['place_id'])){
			$place_id = $_GET['place_id'];
			$rs = getMapList($place_id);
			set_response($rs);
			success("Ow pai!");
		}
		reject("Not enough input.");
	}	
	else{	
		$rs = getMap($map_id);
		set_response($rs);
		success("Ow pai!");
	}
}





function getMap($map_id){
	try{
		global $db_pdo;

		$stmt = $db_pdo->prepare("SELECT * FROM map WHERE map_id = :map_id");
		$stmt->bindParam(':map_id', $map_id);
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

function getMapList($place_id){
	try{
		global $db_pdo;

		$stmt = $db_pdo->prepare("SELECT map_id FROM map WHERE place_id = :place_id");
		$stmt->bindParam(':place_id', $place_id);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject("No info.");
		}
		
		$rs = [
			"map_id_list" => $stmt->fetchAll(PDO::FETCH_COLUMN, 0)
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($e->getMessage());
	}
}

?>