<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/db_connect.php");
require_once("../../inc/init_response_func.php");
require_once("../../inc/json_func.php");

$__TABLE_PREFIX = "IT";
$__TABLE_DEFAULT = [
	"code" => "",
	"x" => 0,
	"y" => 0,
	"table_type_id" => 0
];



function addTable($table_raw){
	global $__TABLE_PREFIX;
	$prefix = $__TABLE_PREFIX;

	try{
		global $DB_PDO;

		$table = prepareData($table_raw);


		$stmt = $DB_PDO->prepare("SELECT place_id FROM map WHERE map_id = :map_id LIMIT 1");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "Location not found.");
		}
		$table['place_id'] = $stmt->fetch(PDO::FETCH_ASSOC)['place_id'];


		$stmt = $DB_PDO->prepare("SELECT code FROM `table` WHERE map_id = :map_id AND code = :code LIMIT 1");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->bindParam(':code', $table['code']);
		$stmt->execute();
		
		if($stmt->rowCount() > 0){
			reject($prefix, "15", "Duplicated table code.");
		}


		$stmt = $DB_PDO->prepare("INSERT INTO `table` (map_id, place_id, code, location, table_type_id) VALUES (:map_id, :place_id, :code, POINT(:x, :y), :table_type_id)");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->bindParam(':place_id', $table['place_id'], PDO::PARAM_INT);
		$stmt->bindParam(':code', $table['code']);
		$stmt->bindParam(':x', $table['x'], PDO::PARAM_INT);
		$stmt->bindParam(':y', $table['y'], PDO::PARAM_INT);
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

function getTable($table_id){
	global $__TABLE_PREFIX;
	$prefix = $__TABLE_PREFIX;

	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT table_id, map_id, place_id, code, X(location) as x, Y(location) as y, table_type_id FROM `table` WHERE table_id = :table_id LIMIT 1");
		$stmt->bindParam(':table_id', $table_id, PDO::PARAM_INT);
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

function getTableList($map_id){
	global $__TABLE_PREFIX;
	$prefix = $__TABLE_PREFIX;

	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("SELECT table_id, map_id, place_id, code, X(location) as x, Y(location) as y, table_type_id FROM `table` WHERE map_id = :map_id");
		$stmt->bindParam(':map_id', $map_id, PDO::PARAM_INT);
		$stmt->execute();
		
		$n = $stmt->rowCount();
		if($n == 0){
			reject($prefix, "14", "No info.");
		}
		
		$rs = [
			"quantity" => $n,
			"table_list" => $stmt->fetchAll(PDO::FETCH_CLASS)
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function prepareData($table_raw){
	global $__TABLE_PREFIX, $__TABLE_DEFAULT;

	$error = [];
	$prefix = $__TABLE_PREFIX;

	$table = prepareJSON($prefix, $table_raw, $__TABLE_DEFAULT);


	if(!isset($table['map_id']) || is_nan($table['map_id']) || $table['map_id'] <= 0){
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
	if(is_nan($table['table_type_id']) || $table['table_type_id'] <= 0){
		$table['table_type_id'] = $__TABLE_DEFAULT['table_type_id'];
	}


	return $table;
}

?>
