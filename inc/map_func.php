<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/db_connect.php");
require_once("../../inc/init_response_func.php");
require_once("../../inc/basic_func.php");
require_once("../../inc/table_func.php");

$__MAP_PREFIX = "IM";
$__MAP_DEFAULT = [
	//"width" => 0,
	//"height" => 0,
	"name" => "",
	"info" => "",
	"bg_image" => ""
];
$__MAP_INFO_QUERY = "map_id, place_id, width, height, name, info, bg_image";



function addMap($map_raw){
	global $__MAP_PREFIX;
	$prefix = $__MAP_PREFIX;

	try{
		global $DB_PDO;

		$map = prepareMapData($map_raw);


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

function editMap($map_raw){
	global $__MAP_PREFIX;
	$prefix = $__MAP_PREFIX;

	try{
		global $DB_PDO;

		$map = prepareMapData($map_raw, true);


		$stmt = $DB_PDO->prepare("SELECT map_id FROM map WHERE map_id = :map_id LIMIT 1");
		$stmt->bindParam(':map_id', $map['map_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Map not found.");
		}


		$stmt = $DB_PDO->prepare("UPDATE map SET width = :width, height = :height, name = :name, info = :info, bg_image = :bg_image WHERE map_id = :map_id");
		$stmt->bindParam(':width', $map['width'], PDO::PARAM_INT);
		$stmt->bindParam(':height', $map['height'], PDO::PARAM_INT);
		$stmt->bindParam(':name', $map['name']);
		$stmt->bindParam(':info', $map['info']);
		$stmt->bindParam(':bg_image', $map['bg_image']);
		$stmt->bindParam(':map_id', $map['map_id'], PDO::PARAM_INT);
		$stmt->execute();
		

		$rs = [
			"map_id" => $map['map_id']
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function getMap($map_id, $getAll = false, $event_id = null){
	global $__MAP_PREFIX, $__MAP_INFO_QUERY;
	$prefix = $__MAP_PREFIX;

	$is_event_param = isPositiveInt($event_id);

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT $__MAP_INFO_QUERY FROM map WHERE map_id = :map_id LIMIT 1");
		$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Map not found.");
		}
		
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);


		if($getAll){
			if($is_event_param){
				$rs['table'] = getTableList($rs['map_id'], $event_id);
			}
			else{
				$rs['table'] = getTableList($rs['map_id']);
			}
		}

		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function getMapList($place_id, $getAll = false, $event_id = null){
	global $__MAP_PREFIX, $__MAP_INFO_QUERY;
	$prefix = $__MAP_PREFIX;

	$is_event_param = isPositiveInt($event_id);

	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("SELECT place_id FROM place WHERE place_id = :place_id LIMIT 1");
		$stmt->bindParam(':place_id', $place_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Location not found.");
		}


		$stmt = $DB_PDO->prepare("SELECT $__MAP_INFO_QUERY FROM map WHERE place_id = :place_id");
		$stmt->bindParam(':place_id', $place_id, PDO::PARAM_INT);
		$stmt->execute();

		$n = $stmt->rowCount();
		$map_list = $stmt->fetchAll(PDO::FETCH_CLASS);


		if($getAll){
			if($is_event_param){
				foreach($map_list as &$map){
					$map->table = getTableList($map->map_id, $event_id);
				}
			}
			else{
				foreach($map_list as &$map){
					$map->table = getTableList($map->map_id);
				}
			}
		}

		
		$rs = [
			"quantity" => $n,
			"map_list" => $map_list
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function removeMap($map_id){
	global $__MAP_PREFIX;
	$prefix = $__MAP_PREFIX;

	$table_deleted = removeTableList("map_id", $map_id);

	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("DELETE FROM map WHERE map_id = :map_id");
		$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Map not found.");
		}
		
		$rs = [
			"map_id" => $map_id,
			"table_deleted" => $table_deleted
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function removeMapList($place_id){
	global $__MAP_PREFIX;
	$prefix = $__MAP_PREFIX;

	$table_deleted = removeTableList("place_id", $place_id);

	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("DELETE FROM map WHERE place_id = :place_id");
		$stmt->bindParam(":place_id", $place_id, PDO::PARAM_INT);
		$stmt->execute();
				
		$rs = [
			"place_id" => $place_id,
			"quantity" => $stmt->rowCount(),
			"table_deleted" => $table_deleted
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function prepareMapData($map_raw, $isEdit = false){
	global $__MAP_PREFIX, $__MAP_DEFAULT;

	$error = [];
	$prefix = $__MAP_PREFIX;

	$map = prepareJSON($prefix, $map_raw, $__MAP_DEFAULT);


	if($isEdit && (!isset($map['map_id']) || notPositiveInt($map['map_id']))){
		$error[] = "Map ID";
	}
	if(!$isEdit && (!isset($map['place_id']) || notPositiveInt($map['place_id']))){
		$error[] = "Place ID";
	}
	if(!isset($map['width']) || notPositiveInt($map['width'])){
		$error[] = "Width";
	}
	if(!isset($map['height']) || notPositiveInt($map['height'])){
		$error[] = "Height";
	}

	if(!empty($error)){
		reject($prefix, "04", "Error parameter(s) - ".implode(", ", $error));
	}


	//if(!isset($map['width']) || notPositiveInt($map['width'])){
	//	$map['width'] = $__MAP_DEFAULT['width'];
	//}
	//if(!isset($map['height']) || notPositiveInt($map['height'])){
	//	$map['height'] = $__MAP_DEFAULT['height'];
	//}
	$map['name'] = trim($map['name']);
	$map['info'] = trim($map['info']);
	$map['bg_image'] = trim($map['bg_image']);


	return $map;
}

?>
