<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/db_connect.php");
require_once("../../inc/init_response_func.php");
require_once("../../inc/basic_func.php");
require_once("../../inc/map_func.php");
require_once("../../inc/event_func.php");

$GLOBALS['TABLE_PREFIX'] = "IT";
$__TABLE_DEFAULT = [
	"code" => "",
	"x" => 0,
	"y" => 0,
	"rotation" => 0,
	"table_type_id" => 0
];
$__TABLE_QUERY = [];
$__TABLE_QUERY['info'] =
	"table_id, map_id, place_id, code, X(location) as x, Y(location) as y, rotation, table_type_id";
$__TABLE_QUERY['info_db'] =
	"table_id, map_id, place_id, code, location, rotation, table_type_id";
$__TABLE_QUERY['event_info'] = 
	"current_booking_ID, event_id, ".$__TABLE_QUERY["info"].", booking_id, active";



function addTable($table_raw){
	$prefix = $GLOBALS['TABLE_PREFIX'];

	try{
		global $DB_PDO;

		$table = prepareTableData($table_raw);


		$stmt = $DB_PDO->prepare("SELECT place_id FROM map WHERE map_id = :map_id LIMIT 1");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Location not found.");
		}
		$table['place_id'] = $stmt->fetchColumn();


		$stmt = $DB_PDO->prepare("SELECT code FROM `table` WHERE map_id = :map_id AND code = :code LIMIT 1");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->bindParam(':code', $table['code']);
		$stmt->execute();
		
		if($stmt->rowCount() > 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_DUPLICATED'], "Duplicated table code.");
		}


		$stmt = $DB_PDO->prepare("INSERT INTO `table` (map_id, place_id, code, location, rotation, table_type_id) VALUES (:map_id, :place_id, :code, POINT(:x, :y), :rotation, :table_type_id)");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->bindParam(':place_id', $table['place_id'], PDO::PARAM_INT);
		$stmt->bindParam(':code', $table['code']);
		$stmt->bindParam(':x', $table['x'], PDO::PARAM_INT);
		$stmt->bindParam(':y', $table['y'], PDO::PARAM_INT);
		$stmt->bindParam(':rotation', $table['rotation'], PDO::PARAM_INT);
		$stmt->bindParam(':table_type_id', $table['table_type_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		$table_id = $DB_PDO->lastInsertId();
		if($table_id == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_FAILED'], "Table add failed.");
		}

		$rs = [
			"table_id" => $table_id
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function editTable($table_raw, $event_id = null, $event_id_data = false){
	$prefix = $GLOBALS['TABLE_PREFIX'];

	try{
		global $DB_PDO;

		$table = prepareTableData($table_raw, true);

		
		if($event_id_data){
			$is_event_param = ((isset($table['event_id']) && isPositiveInt($table['event_id'])));
			if(!$is_event_param){
				reject($GLOBALS['EVENT_PREFIX'], $GLOBALS['RESPONSE_ERROR_CODE']['INFO'], "Event ID KAK KAK");
			}
			$event_id = $table['event_id'];
		}
		else{
			$is_event_param = isPositiveInt($event_id);
		}

		if($is_event_param){
			$main_db = "current_booking";
			$main_cond = "event_id = :event_id AND";
		}
		else{
			$main_db = "table";
			$main_cond = "";
		}


		$stmt = $DB_PDO->prepare("SELECT map_id FROM `$main_db` WHERE $main_cond table_id = :table_id LIMIT 1");
		if($is_event_param){
			$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
		}
		$stmt->bindParam(':table_id', $table['table_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Table not found.");
		}
		$table['map_id'] = $stmt->fetchColumn();


		/*$stmt = $DB_PDO->prepare("SELECT code FROM `$main_db` WHERE $main_cond map_id = :map_id AND code = :code AND table_id != :table_id LIMIT 1");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->bindParam(':code', $table['code']);
		if($is_event_param){
			$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
		}
		$stmt->bindParam(':table_id', $table['table_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() > 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_DUPLICATED'], "Duplicated table code.");
		}*/


		$stmt = $DB_PDO->prepare("UPDATE `$main_db` SET location = POINT(:x, :y), rotation = :rotation, table_type_id = :table_type_id WHERE $main_cond table_id = :table_id");
		//$stmt->bindParam(':code', $table['code']);
		$stmt->bindParam(':x', $table['x'], PDO::PARAM_INT);
		$stmt->bindParam(':y', $table['y'], PDO::PARAM_INT);
		$stmt->bindParam(':rotation', $table['rotation'], PDO::PARAM_INT);
		$stmt->bindParam(':table_type_id', $table['table_type_id'], PDO::PARAM_INT);
		if($is_event_param){
			$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
		}
		$stmt->bindParam(':table_id', $table['table_id'], PDO::PARAM_INT);
		$stmt->execute();
		

		$rs = [
			"table_id" => $table['table_id']
		];
		if($is_event_param){
			$rs['event_id'] = $event_id;
		}

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function getTable($table_id, $event_id = null){
	global  $__TABLE_QUERY;
	$prefix = $GLOBALS['TABLE_PREFIX'];
	$query = $__TABLE_QUERY['info'];

	$is_event_param = isPositiveInt($event_id);
	if($is_event_param){
		$main_db = "current_booking";
	}
	else{
		$main_db = "table";
	}

	try{
		global $DB_PDO;


		if($is_event_param){
			$stmt = $DB_PDO->prepare("SELECT event_id FROM event WHERE event_id = :event_id LIMIT 1");
			$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
			$stmt->execute();

			if($stmt->rowCount() == 0){
				reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Event not found.");
			}
		}


		$stmt = $DB_PDO->prepare("SELECT $query FROM `table` WHERE table_id = :table_id LIMIT 1");
		$stmt->bindParam(':table_id', $table_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Table not found.");
		}
		
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function getTableList($map_id, $event_id = null){
	global $__TABLE_QUERY;
	$prefix = $GLOBALS['TABLE_PREFIX'];

	$is_event_param = isPositiveInt($event_id);
	if($is_event_param){
		$main_key = "Event";
		$main_id = "event_id";
		$main_param_id = $event_id;
		$main_db = "current_booking";
		$main_cond = "event_id = :event_id AND";
		$query = $__TABLE_QUERY["event_info"];
	}
	else{
		$main_key = "Map";
		$main_id = "map_id";
		$main_param_id = $map_id;
		$main_db = "table";
		$main_cond = "";
		$query = $__TABLE_QUERY["info"];
	}

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT $main_id FROM $main_key WHERE $main_id = :$main_id LIMIT 1");
		$stmt->bindParam(':'.$main_id, $main_param_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "$main_key not found.");
		}


		$stmt = $DB_PDO->prepare("SELECT $query FROM `$main_db` WHERE $main_cond map_id = :map_id");
		$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
		if($is_event_param){
			$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
		}
		$stmt->execute();
				
		$rs = [
			"quantity" => $stmt->rowCount(),
			"table_list" => $stmt->fetchAll(PDO::FETCH_CLASS)
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function removeTable($table_id){
	$prefix = $GLOBALS['TABLE_PREFIX'];

	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("DELETE FROM `table` WHERE table_id = :table_id");
		$stmt->bindParam(':table_id', $table_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Table not found.");
		}
		
		$rs = [
			"table_id" => $table_id
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function removeTableList($identifier, $identifier_id){
	$prefix = $GLOBALS['TABLE_PREFIX'];
	$identifier_list = ["map_id", "place_id", "event_id"];

	if(!in_array($identifier, $identifier_list) || notPositiveInt($identifier_id)){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PHP'], "Identifier error.");
	}

	$is_event_param = ($identifier == "event_id");
	if($is_event_param){
		$main_db = "current_booking";
	}
	else{
		$main_db = "table";
	}

	try{
		global $DB_PDO;

		
		$stmt = $DB_PDO->prepare("DELETE FROM `$main_db` WHERE $identifier = :$identifier");
		$stmt->bindParam(":$identifier", $identifier_id, PDO::PARAM_INT);
		$stmt->execute();
		
		$rs = [
			"$identifier" => $identifier_id,
			"quantity" => $stmt->rowCount()
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function setupTableToEvent($event_id, $place_id){
	global $__TABLE_QUERY;
	$prefix = $GLOBALS['TABLE_PREFIX'];

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT map_id FROM map WHERE place_id = :place_id");
		$stmt->bindParam(':place_id', $place_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['DB_NODATA'], "Map not found.");
		}
		$map_ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

		foreach($map_ids as $map_id){
			$stmt = $DB_PDO->prepare("INSERT INTO current_booking (".$__TABLE_QUERY["info_db"].", event_id) SELECT ".$__TABLE_QUERY["info_db"].", :event_id FROM `table` WHERE map_id = :map_id");
			$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
			$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
			$stmt->execute();
		}


		$rs = [
			"event_id" => $event_id,
			"place_id" => $place_id,
			"map_ids" => $map_ids
		];


		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, $GLOBALS['RESPONSE_ERROR_CODE']['PDO'], $e->getMessage());
	}
}

function prepareTableData($table_raw, $isEdit = false, $reject = true){
	global $__TABLE_DEFAULT;
	$prefix = $GLOBALS['TABLE_PREFIX'];

	$required_data = [
		"+int*" => [ ($isEdit ? "table_id" : "map_id") ],
		"int" => [ "x", "y", "rotation" ],
		"+int" => [ "table_type_id" ]
	];
	if(!$isEdit){
		$required_data['str*'] = [ "code" ];
	}

	$table = prepareJSON($prefix, $table_raw, $required_data, $__TABLE_DEFAULT, $reject);

	return $table;
}

?>
