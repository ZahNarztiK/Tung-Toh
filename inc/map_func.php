<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/db_connect.php");
require_once("../../inc/init_response_func.php");
require_once("../../inc/basic_func.php");
require_once("../../inc/table_func.php");

$GLOBALS['MAP_PREFIX'] = "IM";
$__MAP_DEFAULT = [
	"name" => "",
	"info" => "",
	"bg_image" => ""
];
$__MAP_INFO_QUERY = "map_id, place_id, width, height, name, info, bg_image";



function addMap($map_raw){
	$prefix = $GLOBALS['MAP_PREFIX'];

	try{
		global $DB_PDO;

		$map = prepareMapData($map_raw);


		$stmt = $DB_PDO->prepare("SELECT place_id FROM place WHERE place_id = :place_id LIMIT 1");
		$stmt->bindParam(':place_id', $map['place_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Location not found.");
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
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_FAILED'], "Map add failed.");
		}

		$rs = [
			"map_id" => $map_id
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function editMap($map_raw){
	$prefix = $GLOBALS['MAP_PREFIX'];

	try{
		global $DB_PDO;

		$map = prepareMapData($map_raw, true);


		$stmt = $DB_PDO->prepare("SELECT map_id FROM map WHERE map_id = :map_id LIMIT 1");
		$stmt->bindParam(':map_id', $map['map_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Map not found.");
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
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function getMap($map_id, $getAll = false, $event_id = null){
	global $__MAP_INFO_QUERY;
	$prefix = $GLOBALS['MAP_PREFIX'];

	$is_event_param = isPositiveInt($event_id);

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT $__MAP_INFO_QUERY FROM map WHERE map_id = :map_id LIMIT 1");
		$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Map not found.");
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
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function getMapList($place_id, $getAll = false, $event_id = null){
	global $__MAP_INFO_QUERY;
	$prefix = $GLOBALS['MAP_PREFIX'];

	$is_event_param = isPositiveInt($event_id);

	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("SELECT place_id FROM place WHERE place_id = :place_id LIMIT 1");
		$stmt->bindParam(':place_id', $place_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Location not found.");
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
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function removeMap($map_id){
	$prefix = $GLOBALS['MAP_PREFIX'];

	$table_deleted = removeTableList("map_id", $map_id);

	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("DELETE FROM map WHERE map_id = :map_id");
		$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Map not found.");
		}
		
		$rs = [
			"map_id" => $map_id,
			"table_deleted" => $table_deleted
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function removeMapList($place_id){
	$prefix = $GLOBALS['MAP_PREFIX'];

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
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function prepareMapData($map_raw, $isEdit = false){
	global $__MAP_DEFAULT;
	$prefix = $GLOBALS['MAP_PREFIX'];

	$required_data = [
		"+int*" => [ ($isEdit ? "map_id" : "place_id"), "width", "height" ],
		"str" => [ "name", "info", "bg_image" ]
	];

	$map = prepareJSON($prefix, $map_raw, $required_data, $__MAP_DEFAULT);

	return $map;
}

?>
