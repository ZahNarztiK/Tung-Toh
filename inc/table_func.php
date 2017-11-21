<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/db_connect.php");

$__TABLE_DEFAULT = [
	"x" => 0,
	"y" => 0,
	"table_type_id" => 0
];



function addTable($table_raw){
	try{
		global $DB_PDO;

		$table = prepareData($table_raw);


		$stmt = $DB_PDO->prepare("SELECT TOP 1 place_id FROM map WHERE map_id = :map_id");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject("IT14", "Location not found.");
		}
		$table['place_id'] = $stmt->fetch(PDO::FETCH_ASSOC)[0];


		$stmt = $DB_PDO->prepare("SELECT TOP 1 code FROM table WHERE map_id = :map_id AND code = :code");
		$stmt->bindParam(':map_id', $table['map_id'], PDO::PARAM_INT);
		$stmt->bindParam(':code', $table['code']);
		$stmt->execute();
		
		if($stmt->rowCount() > 0){
			reject("IT15", "Duplicated table code.");
		}


		$stmt = $DB_PDO->prepare("INSERT INTO table (place_id, map_id, code, location, table_type_id) VALUES (:place_id, :map_id, :code, POINT(:x, :y), :table_type_id)");
		$stmt->bindParam(':map_id', $table['place_id'], PDO::PARAM_INT);
		$stmt->bindParam(':code', $table['code']);
		$stmt->bindParam(':x', $table['x'], PDO::PARAM_INT);
		$stmt->bindParam(':y', $table['y'], PDO::PARAM_INT);
		$stmt->bindParam(':table_type_id', $table['table_type_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		$table_id = $DB_PDO->lastInsertId();
		if($table_id == 0){
			reject("IT19", "Table add failed.");
		}

		$rs = [
			"table_id" => $table_id
		]

		return $rs;
	}
	catch(PDOException $e){
		reject("IT10", $e->getMessage());
	}
}

function prepareData($table){
	global $__TABLE_DEFAULT;

	$error = [];
	$table += $__TABLE_DEFAULT;

	if(!isset($table['code']) || $table['code'] == ""){
		$error[] = "Table Code";
	}
	if(!isset($table['map_id']) || is_nan($table['map_id'])){
		$error[] = "Map ID";
	}

	if(!isset($table['x']) || is_nan($table['x'])){
		$table['x'] = $__TABLE_DEFAULT['x'];
	}
	if(!isset($table['y']) || is_nan($table['y'])){
		$table['y'] = $__TABLE_DEFAULT['y'];
	}
	if(!isset($table['table_type_id']) || is_nan($table['table_type_id'])){
		$table['table_type_id'] = $__TABLE_DEFAULT['table_type_id'];
	}

	if(!empty($error)){
		reject("IT04", "Error parameter(s) - ".implode(", ", $error));
	}

	return $table;
}

?>