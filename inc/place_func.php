<?

if(!isset($_IN_SITE)){
	die("Access denied ai sus!!!");
}

require_once("../../inc/db_connect.php");
require_once("../../inc/init_response_func.php");
require_once("../../inc/json_func.php");

$__PLACE_PREFIX = "IP";
$__PLACE_DEFAULT = [
	"name" => "",
	"latitude" => "",
	"longitude" => "",
	"logo_image" => "",
	"info" => ""
];



function addPlace($place_raw){
	global $__PLACE_PREFIX;
	$prefix = $__PLACE_PREFIX;

	try{
		global $DB_PDO;

		$place = prepareData($place_raw);


		$stmt = $DB_PDO->prepare("SELECT place_id FROM place WHERE name = :name LIMIT 1");
		$stmt->bindParam(':name', $place['name']);
		$stmt->execute();
		
		if($stmt->rowCount() > 0){
			reject($prefix, "15", "Duplicated name.");
		}


		$stmt = $DB_PDO->prepare("INSERT INTO place (name, location, logo_image, info) VALUES (:name, POINT(:x, :y), :logo_image, :info)");
		$stmt->bindParam(':name', $place['name']);
		$stmt->bindParam(':x', $place['latitude']);
		$stmt->bindParam(':y', $place['longitude']);
		$stmt->bindParam(':logo_image', $place['logo_image']);
		$stmt->bindParam(':info', $place['info']);
		$stmt->execute();
		
		$place_id = $DB_PDO->lastInsertId();
		if($place_id == 0){
			reject($prefix, "19", "Place add failed.");
		}

		$rs = [
			"place_id" => $place_id
		];

		return $rs;
	}
	catch(PDOException $e){
		reject($prefix, "10", $e->getMessage());
	}
}

function getPlace($place_id){
	try{
		global $DB_PDO;


		$stmt = $DB_PDO->prepare("SELECT place_id, X(location) as latitude, Y(location) as longitude, logo_image, info FROM place WHERE place_id = :place_id LIMIT 1");
		$stmt->bindParam(':place_id', $place_id, PDO::PARAM_INT);
		$stmt->execute();
		
		if($stmt->rowCount() == 0){
			reject($prefix, "14", "No info.");
		}
		
		$rs = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return $rs;
	}
	catch(PDOException $e){
		reject($e->getMessage());
	}
}

function prepareData($place_raw){
	global $__PLACE_PREFIX, $__PLACE_DEFAULT;

	$error = [];
	$prefix = $__PLACE_PREFIX;

	$place = prepareJSON($prefix, $place_raw, $__PLACE_DEFAULT);


	$place['name'] = trim($place['name']);
	if($place['name'] == ""){
		$error[] = "Name";
	}
	$place['latitude'] = trim($place['latitude']);
	if(!is_numeric($place['latitude'])){
		$error[] = "Latitude";
	}
	$place['longitude'] = trim($place['longitude']);
	if(!is_numeric($place['longitude'])){
		$error[] = "Longitude";
	}

	if(!empty($error)){
		reject($prefix, "04", "Error parameter(s) - ".implode(", ", $error));
	}


	$place['logo_image'] = trim($place['logo_image']);
	$place['info'] = trim($place['info']);


	return $place;
}

?>
