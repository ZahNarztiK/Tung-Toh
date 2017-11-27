<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/db_connect.php");
require_once("../../inc/init_response_func.php");
require_once("../../inc/basic_func.php");

$__TABLE_PREFIX = "IT";
$__TABLE_DEFAULT = [
	"code" => "",
	"x" => 0,
	"y" => 0,
	"rotation" => 0,
	"table_type_id" => 0
];
$__TABLE_INFO_QUERY = "table_id, map_id, place_id, code, X(location) as x, Y(location) as y, rotation, table_type_id";



function addTable($table_raw){
	global $__TABLE_PREFIX;
	$prefix = $__TABLE_PREFIX;

	try{
		global $DB_PDO;

		$table = prepareTableData($table_raw);


		$stmt = $DB_PDO->prepare("SELECT place_id FROM map WHERE map_id = :map_id LIMIT 1");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Location not found.");
		}
		$table['place_id'] = $stmt->fetchColumn();


		$stmt = $DB_PDO->prepare("SELECT code FROM `table` WHERE map_id = :map_id AND code = :code LIMIT 1");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->bindParam(':code', $table['code']);
		$stmt->execute();
		
		if($stmt->rowCount() > 0){
			reject($prefix, "15", "Duplicated table code.");
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
			reject($prefix, "19", "Table add failed.");
		}

		$rs = [
			"table_id" => $table_id
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function editTable($table_raw){
	global $__TABLE_PREFIX;
	$prefix = $__TABLE_PREFIX;

	try{
		global $DB_PDO;

		$table = prepareTableData($table_raw, true);


		$stmt = $DB_PDO->prepare("SELECT table_id FROM `table` WHERE table_id = :table_id LIMIT 1");
		$stmt->bindParam(':table_id', $table['table_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Table not found.");
		}


		$stmt = $DB_PDO->prepare("SELECT code FROM `table` WHERE map_id = :map_id AND code = :code AND table_id != :table_id LIMIT 1");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->bindParam(':code', $table['code']);
		$stmt->bindParam(':table_id', $table['table_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() > 0){
			reject($prefix, "15", "Duplicated table code.");
		}


		$stmt = $DB_PDO->prepare("UPDATE `table` SET code = :code, location = POINT(:x, :y), rotation = :rotation table_type_id = :table_type_id WHERE table_id = :table_id");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->bindParam(':place_id', $table['place_id'], PDO::PARAM_INT);
		$stmt->bindParam(':code', $table['code']);
		$stmt->bindParam(':x', $table['x'], PDO::PARAM_INT);
		$stmt->bindParam(':y', $table['y'], PDO::PARAM_INT);
		$stmt->bindParam(':rotation', $table['rotation'], PDO::PARAM_INT);
		$stmt->bindParam(':table_type_id', $table['table_type_id'], PDO::PARAM_INT);
		$stmt->bindParam(':table_id', $table['table_id'], PDO::PARAM_INT);
		$stmt->execute();
		

		$rs = [
			"table_id" => $table['table_id']
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function getTable($table_id){
	global $__TABLE_PREFIX, $__TABLE_INFO_QUERY;
	$prefix = $__TABLE_PREFIX;

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT $__TABLE_INFO_QUERY FROM `table` WHERE table_id = :table_id LIMIT 1");
		$stmt->bindParam(':table_id', $table_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Table not found.");
		}
		
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function getTableList($map_id){
	global $__TABLE_PREFIX, $__TABLE_INFO_QUERY;
	$prefix = $__TABLE_PREFIX;

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT map_id FROM map WHERE map_id = :map_id LIMIT 1");
		$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Map not found.");
		}


		$stmt = $DB_PDO->prepare("SELECT $__TABLE_INFO_QUERY FROM `table` WHERE map_id = :map_id");
		$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
		$stmt->execute();
				
		$rs = [
			"quantity" => $stmt->rowCount(),
			"table_list" => $stmt->fetchAll(PDO::FETCH_CLASS)
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function removeTable($table_id){
	global $__TABLE_PREFIX;
	$prefix = $__TABLE_PREFIX;

	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("DELETE FROM `table` WHERE table_id = :table_id");
		$stmt->bindParam(':table_id', $table_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Table not found.");
		}
		
		$rs = [
			"table_id" => $table_id
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function removeTableList($identifier, $identifier_id){
	global $__TABLE_PREFIX;
	$prefix = $__TABLE_PREFIX;
	$identifier_list = ["map_id", "place_id"];

	if(!in_array($identifier, $identifier_list) || notPositiveInt($identifier_id)){
		reject($prefix, "09", "Identifier error.");
	}

	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("DELETE FROM `table` WHERE $identifier = :$identifier");
		$stmt->bindParam(":$identifier", $identifier_id, PDO::PARAM_INT);
		$stmt->execute();
		
		$rs = [
			"$identifier" => $identifier_id,
			"quantity" => $stmt->rowCount()
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function prepareTableData($table_raw, $isEdit = false){
	global $__TABLE_PREFIX, $__TABLE_DEFAULT;

	$error = [];
	$prefix = $__TABLE_PREFIX;

	$table = prepareJSON($prefix, $table_raw, $__TABLE_DEFAULT);


	if($isEdit && (!isset($table['table_id']) || notPositiveInt($table['table_id']))){
		$error[] = "Table ID";
	}
	if(!$isEdit && (!isset($table['map_id']) || notPositiveInt($table['map_id']))){
		$error[] = "Map ID";
	}
	$table['code'] = trim($table['code']);
	if($table['code'] == ""){
		$error[] = "Table Code";
	}

	if(!empty($error)){
		reject($prefix, "04", "Error parameter(s) - ".implode(", ", $error));
	}


	if(is_nan($table['x'])){
		$table['x'] = $__TABLE_DEFAULT['x'];
	}
	if(is_nan($table['y'])){
		$table['y'] = $__TABLE_DEFAULT['y'];
	}
	if(is_nan($table['rotation'])){
		$table['rotation'] = $__TABLE_DEFAULT['rotation'];
	}
	if(notPositiveInt($table['table_type_id'])){
		$table['table_type_id'] = $__TABLE_DEFAULT['table_type_id'];
	}


	return $table;
}

?>
