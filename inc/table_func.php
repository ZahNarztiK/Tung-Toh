<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/db_connect.php");

$__TABLE_DEFAULT = [
	"x" => 0,
	"y" => 0,
	"table_type" => 0
];



function addTable($table){
	try{
		global $DB_PDO;

		$stmt = $DB_PDO->prepare("SELECT place_id FROM map WHERE map_id = :map_id");
		$stmt->bindParam(':map_id', $table['map_id']);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject("IT14", "Location not found.");
		}
		$table['place_id'] = $stmt->fetch(PDO::FETCH_ASSOC)[0];

		$stmt = $DB_PDO->prepare("SELECT code FROM table WHERE map_id = :map_id AND code = :code");
		$stmt->bindParam(':map_id', $table['map_id']);
		$stmt->bindParam(':code', $table['code']);
		$stmt->execute();
		
		if($stmt->rowCount() > 0){
			reject("IT05", "Duplicated table code.");
		}

		$stmt = $DB_PDO->prepare("INSERT INTO table (place_id, map_id, code, location, table_type) VALUES (:place_id, :map_id, :code, POINT(:x, :y), :table_type)");
		$stmt->bindParam(':map_id', $table['place_id']);
		$stmt->bindParam(':code', $table['code']);
		$stmt->bindParam(':x', $table['x']);
		$stmt->bindParam(':y', $table['y']);
		$stmt->bindParam(':table_type', $table['table_type']);
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

?>