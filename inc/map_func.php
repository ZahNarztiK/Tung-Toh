<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/db_connect.php");
require_once("../../inc/init_response_func.php");
require_once("../../inc/json_func.php");

$__MAP_PREFIX = "IM";
$__MAP_DEFAULT = [
	//"width" => 0,
	//"height" => 0,
	"name" => "",
	"info" => "",
	"bg_image" => ""
];



function addMap($map_raw){
	global $__MAP_PREFIX;
	$prefix = $__MAP_PREFIX;

	try{
		global $DB_PDO;

		$map = prepareData($map_raw);


		$stmt = $DB_PDO->prepare("SELECT place_id FROM place WHERE place_id = :place_id LIMIT 1");
		$stmt->bindParam(':place_id', $map['place_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Location not found.");
		}


		$stmt = $DB_PDO->prepare("INSERT INTO map (place_id, width, height, name, info, bg_image) VALUES (:place_id, :width, :height, :name, info, :bg_image)");
		$stmt->bindParam(':place_id', $map['place_id'], PDO::PARAM_INT);
		$stmt->bindParam(':width', $map['width'], PDO::PARAM_INT);
		$stmt->bindParam(':height', $map['height'], PDO::PARAM_INT);
		$stmt->bindParam(':name', $map['name']);
		$stmt->bindParam(':info', $map['info']);
		$stmt->bindParam(':bg_image', $map['bg_image']);
		$stmt->execute();
		
		$map_id = $DB_PDO->lastInsertId();
		if($map_id == 0){
			reject($prefix, "19", "Map add failed.");
		}

		$rs = [
			"map_id" => $map_id
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function getMap($map_id){
	global $__MAP_PREFIX;
	$prefix = $__MAP_PREFIX;

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT map_id, place_id, width, height, name, info, bg_image FROM map WHERE map_id = :map_id LIMIT 1");
		$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "No info.");
		}
		
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function getMapList($place_id){
	global $__MAP_PREFIX;
	$prefix = $__MAP_PREFIX;

	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("SELECT map_id, place_id, width, height, name, info, bg_image FROM map WHERE place_id = :place_id");
		$stmt->bindParam(':place_id', $place_id, PDO::PARAM_INT);
		$stmt->execute();
		
		$n = $stmt->rowCount();
		if($n == 0){
			reject($prefix, "14", "No info.");
		}
		
		$rs = [
			"quantity" => $n,
			"map_list" => $stmt->fetchAll(PDO::FETCH_CLASS)
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function prepareData($map_raw){
	global $__MAP_PREFIX, $__MAP_DEFAULT;

	$error = [];
	$prefix = $__MAP_PREFIX;

	$map = prepareJSON($prefix, $map_raw, $__MAP_DEFAULT);


	if(!isset($map['place_id']) || is_nan($map['place_id']) || $map['place_id'] <= 0){
		$error[] = "Place ID";
	}
	if(!isset($map['width']) || is_nan($map['width']) || $map['width'] <= 0){
		$error[] = "Width";
	}
	if(!isset($map['height']) || is_nan($map['height']) || $map['height'] <= 0){
		$error[] = "Height";
	}

	if(!empty($error)){
		reject($prefix, "04", "Error parameter(s) - ".implode(", ", $error));
	}


	//if(!isset($map['width']) || is_nan($map['width']) || $map['width'] < 0){
	//	$map['width'] = $__MAP_DEFAULT['width'];
	//}
	//if(!isset($map['height']) || is_nan($map['height']) || $map['height'] < 0){
	//	$map['height'] = $__MAP_DEFAULT['height'];
	//}
	$map['name'] = trim($map['name']);
	$map['info'] = trim($map['info']);
	$map['bg_image'] = trim($map['bg_image']);


	return $map;
}

?>
